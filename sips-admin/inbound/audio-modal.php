<?php 
#ficheiros de audio asterisk
                $path = '/var/lib/asterisk/sounds/';
                $path_rplc = '/^\/var\/lib\/asterisk\/sounds\//';

                $directory = new RecursiveDirectoryIterator($path,RecursiveDirectoryIterator::SKIP_DOTS);
                $iterator = new RecursiveIteratorIterator($directory,RecursiveIteratorIterator::LEAVES_ONLY);
                
                $files=array();
                foreach ($iterator as $file) {
                    $filetypes = array("gsm", "wav","sln");
                    $filetype = pathinfo($file, PATHINFO_EXTENSION);
                    if (in_array(strtolower($filetype), $filetypes)) {
                      $files[] = preg_replace($path_rplc,'',$file->getPathname()); 
                    } 
                }
                
                #sort($files);
                $file_list=array();
                foreach ($files as $filename) { 
                        $file_list[$filename]=preg_replace('"\.(gsm|wav|sln)$"','', $filename);
                 }
?>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-fileupload.min.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-fileupload.min.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />
        <style>
            .dataTables_filter {
                width: 50%;
                float: left;
                text-align: left;
                }
        </style>
<div id="modal-audio-upload" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Gestor de Audio</h3><h4></h4>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <div class="span5">
                <table class="table table-mod-2" id="audio-table">
                    <thead>
                        <tr>
                            <th>Audio</th>
                            <th>Ouvir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($file_list as $key => $value) {
                            echo "<tr>\n
                                        \t<td>".basename($value)."</td>\n
                                        \t<td><div class='btn-group'> <a href='#' class='btn playaudio' file='$key'><i class='icon-play'></i></a><a href='#' class='btn addaudio' file='".preg_replace("/.(gsm|sln|wav)/i","",$key)."'><i class='icon-plus-sign'></i></div></td>\n
                                 </tr>\n";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="span7" id="div-upload">
                <form id="audio-upload" action="upload.php" method="POST" enctype="multipart/form-data" >
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="input-append left">
                            <div class="uneditable-input span3" style="height: 30px"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"></span></div><span class="btn btn-file"><span class="fileupload-new">Seleccione um ficheiro</span><span class="fileupload-exists">Alterar</span><input name="audio-file" type="file" id="audio-file"/></span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remover</a>
                        </div>
                        <a id="btn-audio-up" class="btn btn-primary right"><i class="icon-upload"></i>Enviar</a>
                    </div>
                    <div class="progress progress-striped active clear hide">
                        <div class="bar" style="width: 0%;" id="up-progress"></div>
                    </div>
                </form>
            </div>
            <div class="span7" style="padding-top:20px">
                <div id="audio-div" style="padding:56px 0 0 50px">
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
    </div>
</div>
<script>
$(function(){
    //upload files by ajax
                $('#btn-audio-up').click(function(e){
                    e.preventDefault();
                    var form=$("#audio-upload");
                    if (form.find('input[type="file"]').val()==='')return false;
                    var formData = new FormData(form[0]);
                    $.ajax({
                        url: 'upload.php',
                        type: 'POST',
                        xhr: function() {
                            myXhr = $.ajaxSettings.xhr();
                            if(myXhr.upload){ // check if upload property exists
                                myXhr.upload.addEventListener('progress',audio_up_progress, false); // for handling the progress of the upload
                            }
                            return myXhr;
                        },
                        //Ajax events
                        beforeSend: beforeSendHandler,
                        success: successHandler,
                        error: errorHandler,
                        complete:completeHandler,
                        // Form data
                        data: formData,
                        dataType:"json",
                        //Options to tell JQuery not to process data or worry about content-type
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                });
               
                function audio_up_progress(e){
                    if(e.lengthComputable){
                        $('#up-progress').css({width:""+Math.round((e.loaded*100/e.total))+"%"});
                    }
                }
                function errorHandler(e){
                    e=$.parseJSON(e.responseText);
                    if(e!==null){
                    if(e.error==="f_e"){
                        makeAlert("#div-upload","Ficheiro já existente","Já existe um ficheiro no sistema exactamente com o mesmo nome.",3,false,false);
                    }
                    if(e.error==="f_h"){
                        makeAlert("#div-upload","Ficheiro damasiado grande","O sistema só permite ficheiros até 10mb.",3,false,false);
                    }
                    if(e.error==="c_e"){
                        makeAlert("#div-upload","Erro de conversão","Verifique se a extensão do ficheiro corresponde ao formato.",1,false,false);
                    }
                    return false;
                    }
                    makeAlert("#div-upload","Erro","Se voltar a acontecer contacte o responsável.",1,false,false);
                }
                function successHandler(e){
                    makeAlert("#div-upload","Successo","O ficheiro foi carregado com sucesso. Pode agora adicionalo a qualquer mensagem, ou até ouvilo :-).",4,false,false);
                    playFile(e.path);
                    var path=e.path.toString().replace(".gsm","");
                    path;
                    $('#audio-table').dataTable().fnAddData( [
                    basename(path),
                    "<div class='btn-group'> <a href='#' class='btn playaudio' file='"+e.path+"'><i class='icon-play'></i></a><a href='#' class='btn addaudio' file='"+path+"'><i class='icon-plus-sign'></i></div>"]);
                }
                function completeHandler(){
                    $('#up-progress').css({width:"0%"}).parent().addClass("hide");
                }
                function beforeSendHandler(){
                    $('#up-progress').parent().removeClass("hide");
                }
                
                //validate file
                var re_ext=new RegExp("(wav|ogg|mp3|gsm|sln)","i");
                $('#audio-file').change(function(){
                    var file = this.files[0];
                    name = file.name;
                    size = (Math.round((file.size/1024/1024)*100)/100);
                    type = file.type;
                    if(size>10){
                        makeAlert($("#audio-div").parent(),"Tamanha exedido","O tamanha do ficheiro ultrapasso os 10mb permitidos.",1,false,false);
                        $(this).fileupload('clear');}
                    if(!re_ext.test(type)){
                        makeAlert($("#audio-div").parent(),"Extensão Não valida","A extensão do ficheiro seleccionado não é valida.",1,false,false);
                        $(this).fileupload('clear');}
                    console.log("name:"+name);
                    console.log("size:"+size+"mb");
                    console.log("type:"+type);
                    console.log("");
                    //your validation
                });
                
                $('#audio-table').dataTable( {
                    "sDom": 'f<"top">rt<"bottom">p<"clear">',
                    "iDisplayLength": 6,
                    "aaSorting": [[ 0, "desc" ]],
                    "oLanguage": {
                        "sLengthMenu": "Mostrar _MENU_ registos por pagina",
                        "sZeroRecords": "Nada encontrado - desculpe",
                        "sInfo": "A ver _TOTAL_ registos",
                        "sInfoEmpty": "Sem registos",
                        "sInfoFiltered": "(Filtrado do total de _MAX_ registos)"
                    },
                    "aoColumns": [
                              { "bSortable": true, "bSearchable": true, "sWidth": "176" },
                              { "bSortable": false, "bSearchable": false, "sWidth": "61"}]
                });
                     
                
                $(".playaudio").live("click",function(){
                    var file=$(this).attr("file");
                    console.log(file);
                    playFile(file);
                });
                
                $(".addaudio").live("click",function(){
                    var file=$(this).attr("file");
                    console.log(file);
                    if(audio_on_fly.val().length>0){
                        audio_on_fly.val(audio_on_fly.val()+"|"+file);}
                    else{
                        audio_on_fly.val(file);}
                    $("#modal-audio-upload").modal("hide");
                    return false;
                });
});

               
                function playFile(file){
                    $.post("audio_play.php",{file:file},function(d){
                        var audioPlayer= new Audio();
                        audioPlayer.src=d.path;
                        audioPlayer.controls="controls";
                        audioPlayer.autoplay="autoplay";
                        $("#audio-div").html("<label>"+file+"</label>").append(audioPlayer);
                    },"json").fail(function(d){
                        d=$.parseJSON(d.responseText);
                        if (d.error===2){
                            makeAlert($("#audio-div").parent(),"Erro","Este erro parece ser facil de resolver, contacte o responsavél.",1,false,false);
                        }else{
                            makeAlert($("#audio-div").parent(),"Erro","Algo de estranho se passou.",1,false,false);
                        }
                    });
                }
                
                function basename(path) {
                    return path.replace(/\\/g,'/').replace( /.*\//, "" );
                }
        </script>
        <style>
        .modal{
            width:860px;
            margin-left:-380px;
        }
        #audio-table i{
            padding: 0px;
        }
        </style>