var date_limit = function(selector, limit)
{
    var me = this;
    this.limit = limit;


    this.init = function()
    {
        selector.empty();
        $.get("/sips-admin/script_dinamico/items/date_limit.html", function(data) {
            selector.append(data);
            $("#date_limit_management .spinner").spinner({});
            $("#date_limit_management .datelimit_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});

            $("#date_limit_management .date_option_radio").on("click", function() {
                $("#date_limit_management .form_datetime").val("");
                $("#date_limit_management #date_dynamic input").val("");
                $("#date_limit_management #date_fixed").hide();
                $("#date_limit_management #date_dynamic").hide();
                if ($(this).val() == "1")
                    $("#date_limit_management #date_fixed").show();
                else
                    $("#date_limit_management #date_dynamic").show();
            });


            $("#date_limit_management #date_dynamic input").on("blur", function() {
                $(this).val($(this).val().replace(/[^0-9]*$/g, ""));
            });




            if (me.limit.type == "dynamic")
            {
                var data_i = me.limit.data_inicial.split("|");
                var data_f = me.limit.data_final.split("|");

                console.log(data_i);
                $("#date_limit_management #table_date1 .date_year").val(data_i[0].toString() == "#" ? "" : data_i[0].toString());
                $("#date_limit_management #table_date1 .date_month").val(data_i[1].toString() == "#" ? "" : data_i[1].toString());
                $("#date_limit_management #table_date1 .date_day").val(data_i[2].toString() == "#" ? "" : data_i[2].toString());
                $("#date_limit_management #table_date1 .date_hour").val(data_i[3].toString() == "#" ? "" : data_i[3].toString());

            }
            else
            {

            }




        });
    };



    this.get_time = function()
    {
        if ($("#date_limit_management #radio_date_type_filter1").is(":checked"))//FIXED
        {
            return {"type": "fixed", "data_inicial": $("#date_limit_management #fixed_date1").val() == "" ? "no limit" : $("#date_limit_management #fixed_date1").val(), "data_final": $("#date_limit_management#fixed_date2").val() == "" ? "no limit" : $("#date_limit_management #fixed_date2").val()};
        }
        else//DYNAMIC
        {
            var i_anos, i_meses, i_dias, i_horas;
            var f_anos, f_meses, f_dias, f_horas;
            i_anos = $("#table_date1 .date_year").val().length === 0 ? "#" : $("#table_date1 .date_year").val();
            i_meses = $("#table_date1 .date_month").val().length === 0 ? "#" : $("#table_date1 .date_month").val();
            i_dias = $("#table_date1 .date_day").val().length === 0 ? "#" : $("#table_date1 .date_day").val();
            i_horas = $("#table_date1 .date_hour").val().length === 0 ? "#" : $("#table_date1 .date_hour").val();
            f_anos = $("#table_date2 .date_year").val().length === 0 ? "#" : $("#table_date2 .date_year").val();
            f_meses = $("#table_date2 .date_month").val().length === 0 ? "#" : $("#table_date2 .date_month").val();
            f_dias = $("#table_date2 .date_day").val().length === 0 ? "#" : $("#table_date2 .date_day").val();
            f_horas = $("#table_date2 .date_hour").val().length === 0 ? "#" : $("#table_date2 .date_hour").val();
            return {"type": "dynamic", "data_inicial": i_anos + "|" + i_meses + "|" + i_dias + "|" + i_horas, "data_final": f_anos + "|" + f_meses + "|" + f_dias + "|" + f_horas};
        }
    };
};

