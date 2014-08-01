var clientBox = function(configs) {
    var
            me = this,
            defaults = {
                target: "#clientBox",
                id: 0,
                byReserv: false
            },
    config = $.extend(defaults, configs),
            template = "<div class='grid'>\n\
                            <div class='grid-title'>\n\
                                                              <div class='pull-right'>\n\
                                  <button id='button_proposta_comercial' title='Propostas comerciais' class='btn icon-alone'><i class='icon-money'></i></button>\n\
                                    <button id='open_pdf' class='btn  icon-alone'><i class='icon-user'></i> </button>\n\
                                                                   </div>\n\
                            </div>\n\
                            <div class='user c_bg-2'>\n\
                                <div class='user-name' id='client_cod_camp'></div>\n\
                                <div class='user-email' id='client_ref_client'></div>\n\
                                <div class='user-email' id='client_compart'></div>\n\
                                <div class='user-email' style='height:auto; ' id='client_name'></div>\n\
                                <div class='user-date' id='client_address'></div>\n\
                                <div class='user-date' id='client_postal'></div>\n\
                                <div class='user-date' id='client_local'></div>\n\
                                <div class='user-date' id='client_tel'></div>\n\
                                <div class='user-date' id='client_tel1'></div>\n\
                                <div class='user-date' id='client_tel2'></div>\n\
                                <div class='user-date' id='client_birth_date'></div>\n\
                                <div class='user-date' id='client_date'></div>\n\
                                <div class='user-date' id='client_rsc'></div>\n\
                                <div class='user-date' id='client_comments'></div>\n\
                                <div class='clear'></div>\n\
                            </div>\n\
                        </div>";
    this.client_info = [];
    this.init = function(callback) {
        var action = (config.byReserv) ? 'byReserv' : 'default';
        $.msg();
        $.post("/AM/ajax/client.php", {action: action, id: config.id}, function(clientI) {
            me.client_info = clientI;
            $(config.target)
                    .append(template)
                    .find("#client_name")
                    .text(clientI.name)
                    .end()
                    .find("#client_address")
                    .text(clientI.address)
                    .end()
                    .find("#client_postal")
                    .text(clientI.postalCode)
                    .end()
                    .find("#client_local")
                    .text(clientI.local)
                    .end()
                    .find("#client_ref_client")
                    .text(clientI.refClient)
                    .end()
                    .find("#client_cod_camp")
                    .text(clientI.codCamp)
                    .end()
                    .find("#client_compart")
                    .text(clientI.compart)
                    .end()
                    .find("#client_tel")
                    .text(clientI.phone)
                    .end()
                    .find("#client_tel1")
                    .text(clientI.phone1)
                    .end()
                    .find("#client_tel2")
                    .text(clientI.phone2)
                    .end()
                    .find("#client_birth_date")
                    .text(clientI.bDay)
                    .end()
                    .find("#client_date")
                    .text(function() {
                        return (clientI.date) ? moment(clientI.date).format('LLLL') : '';
                    })
                    .end()
                    .find("#client_rsc")
                    .text(clientI.rscName)
                    .end()
                    .find("#client_comments")
                    .text(clientI.comments)
                    .end();



            $.post('/AM/ajax/upload_file.php', {
                action: "get_pdfs",
                navid: me.client_info.navId || "",
                ref_cliente: me.client_info.refClient
            }, function(data) {

                if (data) {
                    $("#open_pdf").click(function() {
                        var c = encodeURIComponent(data);
                        document.location = '/AM/ajax/downloader.php?file=' + c;
                    });
                }
                else {
                    $("#open_pdf").click(function() {
                        $.jGrowl("Cliente sem ficheiro associado", 3000);
                    });
                }
                $.msg('unblock');
            }, "json").fail(function(data) {
                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                $.msg('unblock', 5000);
            });


            $("#button_proposta_comercial").click(function() {
                $.msg();
                var propostas = "";
                var final = "<h5>Data de criação da proposta comercial</h5>";
                $.post('/AM/ajax/users.php', {action: "get_propostas", lead_id: me.client_info.id},
                function(data) {
                    if (data.length) {
                        var menu = "";
                        $.each(data, function() {
                            $.each(this.proposta, function() {
                                propostas += " <tr><td>" + this.modelo + "</td><td>" + this.valor + "</td><td>" + this.quantidade + "</td><td>" + this.entrada + "</td><td>" + this.meses + "</td></tr>"
                            });
                            menu = "<div>\n\
                                        <div class='formRow'>\n\
                                            <label>" + this.data + "</label><button class='btn right' onclick='$(this).next(\".formRight\").toggle(); '><i class='icon-eye-open icon-alone'></i></button>\n\
                                            <div class='formRight' style='display:none'>\n\
                                                <table class='table table-striped table-mod table-bordered'>\n\
                                                    <thead>\n\
                                                        <tr>\n\
                                                            <th>Modelo</th>\n\
                                                            <th>Valor</th>\n\
                                                            <th>Quantidade</th>\n\
                                                            <th>Entrada</th>\n\
                                                            <th>Meses</th>\n\
                                                        </tr>\n\
                                                    </thead>\n\
                                                    <tbody id='tbody_proposta_comercial'>" + propostas + "</tbody>\n\
                                                </table>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                    <div class='clear'></div>";
                            final += menu;
                            propostas = "";
                        });
                        bootbox.alert(final, function() {
                        });
                    }
                    else {
                        bootbox.alert("Cliente sem propostas comerciais.", function() {
                        });
                    }
                    $.msg('unblock');
                }, "json").fail(function(data) {

                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                    $.msg('unblock', 5000);
                });
            });

            if (typeof callback === 'function') {
                callback(clientI);
            }
        }
        , "json");
    };

    this.destroy = function() {
        $(me.target).empty();
    };

    this.get_info = function() {
        return me.client_info;
    };


};


 