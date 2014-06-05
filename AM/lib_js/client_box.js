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
                                <div class='pull-left'>Cliente</div>\n\
                                <div class='pull-right'>\n\
                                    <button title='Abrir Pdf' class='btn icon-alone'><i class='icon-user'></i></button>\n\
                                    <button onclick='history.back();' title='Voltar' class='btn icon-alone'><i class='icon-circle-arrow-left'></i></button>\n\
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
                    .text(clientI.postal)
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
            if (typeof callback === 'function') {
                callback(clientI);
            }
        }, "json");
    };

    this.destroy = function() {
        $(me.target).empty();
    };

    this.get_info = function() {
        return me.client_info;
    };
};