var uploader;
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
    init_plupload();
    $("#filelist").on("click", ".delete_anexo_line", function() {
        if (uploader.removeFile(uploader.getFile($(this).data().id))) {
            $(this).closest("tr").remove();
        }
    });
    $.post("ajax/report/reports.php", {action: "get_agents"}, function(data) {
        $.each(data, function() {
            $("#agent_selector").append("<option  value='" + this.user + "' >" + this.full_name + "</option>");
        });
    }, "json").fail(function(data) {
        $.msg();
        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
        $.msg('unblock', 5000);
    });
});
$("#download_report").click(function() {
    if ($("#report_form").validationEngine("validate"))
        //  document.location.href = "/AM/ajax/report/reports.php?action=" + $("#report_selector option:selected").val() + "&data_inicial=" + $("#input_data_inicio").val() + "&data_fim=" + $("#input_data_fim").val();
        document.location.href = "/AM/ajax/report/reports.php?action=" + $("#report_selector option:selected").val() + "&data_inicial=2014-01-01&data_fim=2014-12-12";
});
function init_plupload() {
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
                if (up.files.length == 1) {
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
                }
            },
            UploadProgress: function(up, file) {
                document.getElementById(file.id).getElementsByClassName('bar')[0].style.width = file.percent + '%';
            },
            Error: function(up, err) {
                $.jGrowl(err.file.name + "&#8594;" + err.message, {life: 3000});
            },
            FileUploaded: function(up, file, info) {
                if (~~info.response) {
                    $.msg();
                    $.post('/AM/ajax/report/importNav.php', {
                        file: file.name
                    }, function(data) {
                        if (data.notok) {
                            var trs="";
                            $.each(data.notoklist, function() {
                                trs += "<tr>\n\
                                        <td>" + this.line + "</td>\n\
                                        <td>" + this.navid + "</td>\n\
                                        <td>" + this.id + "</td>\n\
                                      </tr>";
                            });
                            bootbox.alert("<div class='alert alert-warning'>Erro a importar o relatório.</div>\n\
                                        <table class='table table-mod table-bordered table-striped table-condensed'>\n\
                                            <thead>\n\
                                                <tr>\n\
                                                    <td>Linha</td>\n\
                                                    <td>Navid</td>\n\
                                                    <td>Id</td>\n\
                                                    </tr>\n\
                                            </thead>\n\
                                            <tbody>\n\
                                            " + trs + "\n\
                                            </tbody>\n\
                                        </table>");

                        }
                        else {
                            $.msg('replace', "Relatório carregado com sucesso!");
                            $.msg('unblock', 1000);
                        }
                        uploader.destroy();
                        init_plupload();
                    }, "json").fail(function(data) {
                        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                        $.msg('unblock', 1000);
                    });
                }
                else {
                    uploader.destroy();
                    init_plupload();
                    $.msg({content: info.response, autoUnblock: true});
                }
                $("#filelist").find(".delete_anexo_line").prop("disabled", true);
            }
        }
    });
    uploader.init();
}
