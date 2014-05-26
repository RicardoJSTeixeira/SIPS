var date_limit = function(selector, limit)
{
    var me = this;
    this.limit = limit;
    this.geral_name = selector.attr("id");
    this.init = function(callback)
    {
        selector.empty();

        var html = ' <div class="formRow">\n\
                                 <input class="date_option_radio" type="radio" id="radio_date_type_filter1' + me.geral_name + '" name="rdtf' + me.geral_name + '" checked="checked" value="1"> <label class="radio_name radio inline" for="radio_date_type_filter1' + me.geral_name + '"><span></span>Data Fixa</label>\n\
                                <input class="date_option_radio" type="radio" id="radio_date_type_filter2' + me.geral_name + '" name="rdtf' + me.geral_name + '" value="2" > <label class="radio_name radio inline" for="radio_date_type_filter2' + me.geral_name + '"><span></span>Data Dinâmica</label>\n\
                            </div> \n\
                            <div id="date_fixed' + me.geral_name + '" style="display:none"> \n\
<div class="formRow" > \n\
<strong>Data Inicial</strong> \n\
<div class="formRight "> \n\
<input size="16" type="text" class="datelimit_datetime validate[required] " name="fixed_date1' + me.geral_name + '" id="fixed_date1' + me.geral_name + '"> \n\
</div>\n\
 </div>\n\
 <div class="formRow"> \n\
<strong>Data Final</strong>\n\
 <div class="formRight "> \n\
<input size="16" type="text" class="datelimit_datetime validate[required]" name="fixed_date2' + me.geral_name + '" id="fixed_date2' + me.geral_name + '"> \n\
</div> \n\
</div>\n\
 </div>\n\
 <div id="date_dynamic' + me.geral_name + '" style="display:none">\n\
 <div class="formRow">\n\
 <strong>Data Inicial</strong>\n\
 <div class="formRight">\n\
 <table id="table_date1' + me.geral_name + '">\n\
 <thead><th>Anos</th><th>Meses</th><th>Dias</th><th>Horas</th></thead>\n\
 <tbody> <tr>\n\
<td> <input id="year_i' + me.geral_name + '" name="year_i" class="input-mini spinner validate[groupRequired[fixed_date]]" > </td>\n\
 <td><input id="month_i' + me.geral_name + '" name="month_i" class="input-mini spinner validate[groupRequired[fixed_date]]" > </td>\n\
 <td><input id="day_i' + me.geral_name + '" name="day_i" class="input-mini spinner validate[groupRequired[fixed_date]]" > </td> \n\
<td><input id="hour_i' + me.geral_name + '" name="hour_i" class="input-mini spinner validate[groupRequired[fixed_date]]" > </td> </tr>\n\
 </tbody> \n\
</table> \n\
</div> \n\
</div>\n\
<div class="formRow"> \n\
<strong>Data Final</strong> \n\
<div class="formRight"> \n\
<table id="table_date2' + me.geral_name + '">\n\
 <thead><th>Anos</th><th>Meses</th><th>Dias</th><th>Horas</th></thead>\n\
 <tbody> <tr>\n\
<td> <input id="year_f' + me.geral_name + '" name="year_f" class="input-mini spinner validate[groupRequired[fixed_date]]" > </td> \n\
<td><input id="month_f' + me.geral_name + '" name="month_f" class="input-mini spinner validate[groupRequired[fixed_date]]" > </td> \n\
<td><input id="day_f' + me.geral_name + '" name="day_f" class="input-mini spinner validate[groupRequired[fixed_date]]" > </td>\n\
 <td><input id="hour_f' + me.geral_name + '" name="hour_i" class="input-mini spinner validate[groupRequired[fixed_date]]" > </td> </tr> \n\
</tbody> \n\
</table> \n\
</div> </div> </div> ';










        selector.append(html);
        selector.find(".datelimit_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
        if ( typeof(limit)=="Object")
            if (Object.keys(limit).length)
            {
                if (me.limit.type == "dynamic")
                {
                    var data_i = me.limit.data_inicial.split("|");
                    var data_f = me.limit.data_final.split("|");
                    var table1 = selector.find("#table_date1" + me.geral_name);
                    table1.find("#year_i" + me.geral_name).val(data_i[0] == "#" ? "" : data_i[0]);
                    table1.find("#month_i" + me.geral_name).val(data_i[1] == "#" ? "" : data_i[1]);
                    table1.find("#day_i" + me.geral_name).val(data_i[2] == "#" ? "" : data_i[2]);
                    table1.find("#hour_i" + me.geral_name).val(data_i[3] == "#" ? "" : data_i[3]);
                    var table2 = selector.find("#table_date2" + me.geral_name);
                    table2.find("#year_f" + me.geral_name).val(data_f[0] == "#" ? "" : data_f[0]);
                    table2.find("#month_f" + me.geral_name).val(data_f[1] == "#" ? "" : data_f[1]);
                    table2.find("#day_f" + me.geral_name).val(data_f[2] == "#" ? "" : data_f[2]);
                    table2.find("#hour_f" + me.geral_name).val(data_f[3] == "#" ? "" : data_f[3]);
                    selector.find("#radio_date_type_filter2" + me.geral_name).prop("checked", true);
                    selector.find("#date_dynamic" + me.geral_name).show();
                }
                else if (me.limit.type == "fixed")
                {
                    selector.find("#fixed_date1" + me.geral_name).val(limit.data_inicial.length ? limit.data_inicial : "");
                    selector.find("#fixed_date2" + me.geral_name).val(limit.data_final.length ? limit.data_final : "");
                    selector.find("#radio_date_type_filter1" + me.geral_name).prop("checked", true);
                    selector.find("#date_fixed" + me.geral_name).show();
                }
            }
            else
            {

                selector.find("#radio_date_type_filter1" + me.geral_name).prop("checked", true);
                selector.find("#date_fixed" + me.geral_name).show();
            }

        selector.find(".spinner").spinner({});
        selector.find(".date_option_radio").on("click", function() {
            selector.find("#date_fixed" + me.geral_name).hide();
            selector.find("#date_dynamic" + me.geral_name).hide();
            if ($(this).val() == "1")
                selector.find("#date_fixed" + me.geral_name).show();
            else
                selector.find("#date_dynamic" + me.geral_name).show();
        });
        selector.find("#date_dynamic input" + me.geral_name).on("blur", function() {
            $(this).val($(this).val().replace(/[^0-9]*$/g, ""));
        });
        if (typeof callback === "function")
        {
            callback();
        }
    };
    this.get_time = function()
    {
        if (selector.find(" #radio_date_type_filter1" + me.geral_name).is(":checked"))//FIXED
        {
            return {"type": "fixed", "data_inicial": selector.find(" #fixed_date1" + me.geral_name).val(), "data_final": selector.find("#fixed_date2" + me.geral_name).val()};
        }
        else//DYNAMIC
        {
            var i_anos, i_meses, i_dias, i_horas;
            var f_anos, f_meses, f_dias, f_horas;
            var table1 = selector.find("#table_date1" + me.geral_name);
            var table2 = selector.find("#table_date2" + me.geral_name);
            i_anos = table1.find("#year_i" + me.geral_name).val().length ? table1.find("#year_i" + me.geral_name).val() : "#";
            i_meses = table1.find(" #month_i" + me.geral_name).val().length ? table1.find(" #month_i" + me.geral_name).val() : "#";
            i_dias = table1.find(" #day_i" + me.geral_name).val().length ? table1.find(" #day_i" + me.geral_name).val() : "#";
            i_horas = table1.find(" #hour_i" + me.geral_name).val().length ? table1.find(" #hour_i" + me.geral_name).val() : "#";
            f_anos = table2.find(" #year_f" + me.geral_name).val().length ? table2.find(" #year_f" + me.geral_name).val() : "#";
            f_meses = table2.find(" #month_f" + me.geral_name).val().length ? table2.find(" #month_f" + me.geral_name).val() : "#";
            f_dias = table2.find(" #day_f" + me.geral_name).val().length ? table2.find(" #day_f" + me.geral_name).val() : "#";
            f_horas = table2.find(" #hour_f" + me.geral_name).val().length ? table2.find(" #hour_f" + me.geral_name).val() : "#";
            return {"type": "dynamic", "data_inicial": i_anos + "|" + i_meses + "|" + i_dias + "|" + i_horas, "data_final": f_anos + "|" + f_meses + "|" + f_dias + "|" + f_horas};
        }
    };
    this.has_limit = function()
    {
        if ( typeof(limit)=="Object")
            return Object.keys(limit).length;
        else
            return 0;
    };
    this.destroy = function()
    {
        selector.empty();
    };
};

