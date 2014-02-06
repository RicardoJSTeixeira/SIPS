var request = function(options_ext)
{
    var me = this;
    this.config = new Object();
    $.extend(true, this.config, options_ext);
    this.init = function()
    {

    };
    this.apoio_marketing = function(am_zone)
    {
        var ldpinput_count = 2;
        am_zone.empty().off();
        $.get("/AM/view/requests/apoio_marketing.html", function(data) {
            am_zone.append(data);
            $('#apoio_am_form').stepy({backLabel: "Anterior", nextLabel: "Seguinte", next: function() {
                    if (am_zone.find("#apoio_am_form").validationEngine("validate"))
                        return true;
                    else
                        return false;
                }, finishButton: false});
            am_zone.find(".form_datetime_day").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).on('changeDate', function(ev) {
                am_zone.find(".form_datetime_hour").datetimepicker('update', $(this).val());
            });
            am_zone.find(".form_datetime_hour").datetimepicker({format: 'yyyy-mm-dd hh:ii', autoclose: true, language: "pt", startView: 1, maxView: 1});
            //Adiciona Linhas
            am_zone.on("click", "#button_ldptable_add_line", function(e)
            {
                e.preventDefault();
                am_zone.find("#table_tbody_ldp").append("<tr><td><input name='ldp_cp" + ldpinput_count + "' class='validate[required]'></td><td><input name='ldp_freg" + ldpinput_count + "' class='validate[required]'></td><td> <button class='btn btn-danger button_ldptable_remove_line' ><span class='icon-minus'></span></button></td></tr>");
                ldpinput_count++;
            });
            //Remove Linhas
            am_zone.on("click", ".button_ldptable_remove_line", function(e)
            {
                e.preventDefault();
                $(this).parent().parent().remove();
            });

            //SUBMIT
            am_zone.on("click", "#submit_am", function(e)
            {
                e.preventDefault();
                if (am_zone.find("#apoio_am_form").validationEngine("validate"))
                    alert("validado e enviado");
            });
        });
    };
    this.relatorio_correio = function(rc_zone)
    {
        rc_zone.empty().off();
        $.get("/AM/view/requests/relatorio_correio.html", function(data) {
            rc_zone.append(data);
             rc_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
            //SUBMIT
            rc_zone.on("click", "#submit_rc", function(e)
            {
                e.preventDefault();
                if (rc_zone.find("#relatorio_correio_form").validationEngine("validate"))
                    alert("validado e enviado");
            });
        });
    };
    this.relatorio_frota = function(rf_zone)
    {
        var rfinput_count = 2;
        rf_zone.empty().off();
        $.get("/AM/view/requests/relatorio_frota.html", function(data) {
            rf_zone.append(data);
            rf_zone.find(".rf_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
            //Adiciona Linhas
            rf_zone.on("click", "#button_rf_table_add_line", function(e)
            {
                e.preventDefault();
                rf_zone.find("#table_tbody_rf").append("<tr><td> <input size='16' type='text' name='rf_data" + rfinput_count + "' class='rf_datetime validate[required]' readonly id='rf_datetime" + rfinput_count + "' placeholder='Data'></td><td><input class='validate[required]' type='text' name='rf_ocorr" + rfinput_count + "'></td><td>  <input class='validate[required]' type='number' value='1' name='rf_km" + rfinput_count + "' min='1'></td><td>     <button class='btn btn-danger button_rf_table_remove_line'><span class='icon-minus'></span></button></td></tr>");
                rf_zone.find("#rf_datetime" + rfinput_count).datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
                rfinput_count++;
            });
            //Remove Linhas
            rf_zone.on("click", ".button_rf_table_remove_line", function(e)
            {
                e.preventDefault();
                $(this).parent().parent().remove();
            });
            //SUBMIT
            rf_zone.on("click", "#submit_rf", function(e)
            {
                e.preventDefault();
                if (rf_zone.find("#relatorio_frota_form").validationEngine("validate"))
                    alert("validado e enviado");
            });
        });
    };
};
