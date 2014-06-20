$(function() {


    $("#input_data_inicio").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, endDate: moment().format("YYYY-MM-DD")})
            .on('changeDate', function() {
                $("#input_data_fim").datetimepicker('setStartDate', moment($(this).val()).format('YYYY-MM-DD'));
            });
    $("#input_data_fim").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, endDate: moment().format("YYYY-MM-DD")})
            .on('changeDate', function() {
                $("#input_data_inicio").datetimepicker('setEndDate', moment($(this).val()).format('YYYY-MM-DD'));
            });

    $(".chosen-select").chosen(({
        no_results_text: "Sem resultados"
    }));
    var uploader;

    uploader = new plupload.Uploader({
        browse_button: 'browse', // this can be an id of a DOM element or the DOM element itself
        url: '/AM/ajax/upload_file.php?action=upload_report',
        filters: {
            mime_types: [
                {title: "Text file", extensions: "txt"}],
            max_file_size: "20mb",
            prevent_duplicates: true
        },
        init: {
            PostInit: function() {
                document.getElementById('filelist').innerHTML = '';
                document.getElementById('start-upload').onclick = function() {

                    uploader.start();
                    return false;
                };
            },
            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    $('#filelist').append('\
                                            <tr id="' + file.id + '">\n\
                                                <td>' + file.name + '</td>\n\
                                                <td>\n\
                                                    <div class="progress progress-warning active progress-striped">\n\
                                                    <div class="bar" style="width: 0%"></div>\n\
                                                    </div>\n\
                                                </td>\n\
                                                <td>' + plupload.formatSize(file.size) + '\n\
                                                    <div class="view-button">\n\
                                                        <button data-id="' + file.id + '" class="btn btn-mini icon-alone delete_anexo_line" ><i class="icon-trash"></i></button></td>\n\
                                                    </div>\n\
                                                </td>\n\
                                            </tr>');
                });
                $("#start-upload").show();

            },
            UploadProgress: function(up, file) {
                document.getElementById(file.id).getElementsByClassName('bar')[0].style.width = file.percent + '%';
            },
            Error: function(up, err) {
                $.jGrowl(err.file.name + "&#8594;" + err.message, {life: 3000});
            },
            FileUploaded: function(up, file, info) {
                $.msg();
                $.post('/ajax/report/importNav.php', {
                    file: file.name
                }, function() {
                    $.msg('unblock');
                }, "json").fail(function(data) {
                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                    $.msg('unblock', 5000);
                });
                $.jGrowl(info.response, {life: 3000});
                $("#filelist").find(".delete_anexo_line").prop("disabled", true);
            }
        }
    });
    uploader.init();
});
$("#download_report").click(function(){
    $.post("requests.php", {action: "upload_report"},
    function(data)    {
        $("#download_report").prop("disabled", false);
        $('#loading').hide();
        document.location.href = "requests.php?action=get_report_file&file=" + data;
    }, "json");
});

