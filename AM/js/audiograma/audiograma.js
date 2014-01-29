var audiograma = function(lead_id) {

    var values_regex = /[^0-9 \-\+\<\>]+/g;
    var contas_regex = /[^0-9\-]+/g;
    var lead_id = lead_id;
    var me = this;
    $("#audiograma_form #right_ear").text("Sem dados");
    $("#audiograma_form #left_ear").text("Sem dados");

    $("#audiograma_form").on("submit", function(e)
    {
        e.preventDefault();
    });

    this.calculate = function(callback)
    {

        var right_ear = {"value": 0, "text": "Sem Perda"};
        var left_ear = {"value": 0, "text": "Sem Perda"};
        var all_ear = {"value": 0, "text": "Ambos os ouvidos: Sem Perda"};
        if (me.validate())
        {
//APRESENTA SO VALORES CALCULADOS DE CADA OUVIDO

            var ar500 = (($("#audiograma_form #AR500").val().replace(contas_regex, "")));
            var al500 = (($("#audiograma_form #AL500").val().replace(contas_regex, "")));
            var ar1000 = (($("#audiograma_form #AR1000").val().replace(contas_regex, "")));
            var al1000 = (($("#audiograma_form #AL1000").val().replace(contas_regex, "")));
            var ar2000 = (($("#audiograma_form #AR2000").val().replace(contas_regex, "")));
            var al2000 = (($("#audiograma_form #AL2000").val().replace(contas_regex, "")));
            var ar4000 = (($("#audiograma_form #AR4000").val().replace(contas_regex, "")));
            var al4000 = (($("#audiograma_form #AL4000").val().replace(contas_regex, "")));

            right_ear.value = ((ar500 * 4) + (ar1000 * 3) + (ar2000 * 2) + (ar4000 * 1)) / 10;
            left_ear.value = ((al500 * 4) + (al1000 * 3) + (al2000 * 2) + (al4000 * 1)) / 10;

            if (right_ear.value < 35 && left_ear.value < 35)
            {
                all_ear.text = "Ambos os ouvidos: Sem perda";
                all_ear.value = 0;
            }
            else
            {
                all_ear.text = "";
                all_ear.value = 1;
                if (right_ear.value >= 35 && right_ear.value < 65)
                {
                    right_ear.text = "Perda";
                } else if (right_ear.value >= 65)
                {
                    right_ear.text = "Perda Power";
                }
                if (left_ear.value >= 35 && left_ear.value < 65)
                {
                    left_ear.text = "Perda";
                } else if (left_ear.value >= 65)
                {
                    left_ear.text = "Perda Power";
                }
            }

            $("#audiograma_form #right_ear").text(right_ear.text);
            $("#audiograma_form #right_ear_value").val(right_ear.value);
            $("#audiograma_form #left_ear").text(left_ear.text);
            $("#audiograma_form #left_ear_value").val(left_ear.value);
            $("#audiograma_form #all_ear").text(all_ear.text);
            $("#audiograma_form #all_ear_value").val(all_ear.value);
            if (typeof callback === "function")
            {
                callback();
            }
            return  all_ear.value;

        }
       

        return false;


    }
    ;

    this.save = function(lead_id) { //Grava na BASE DE DADOS
        if (me.validate())
        {
            $.post("ajax/audiograma/audiograma.php", {action: "save_audiograma", lead_id: lead_id, info: $("#audiograma_form").serializeArray()}, "json");
        alert("a");
        }
        };





    this.validate = function()
    {


        var bcr = 0;
        var bcl = 0;
        var bcstatus = true;
        $.each($("#bcr_tr input"), function()
        {
            if (!$(this).val())
                bcr = 1;
        });
        $.each($("#bcl_tr input"), function()
        {
            if (!$(this).val())
                bcl = 1;
        });
        if (bcr && bcl)
        {
            bcstatus=false;
            $('#bc_tooltip').tooltip('show');
        }
        else
            $('#bc_tooltip').tooltip('hide');
  
        return $("#audiograma_form").validationEngine('validate') && bcstatus;
    };



//VALIDATE DOS MAX E MIN VALUES
    $("#audiograma_form #audiograma_table input").on("focusout", function()
    {
        var element = $(this);
        var min = element.data("min");
        var max = element.data("max");
        element.val(element.val().replace(values_regex, ""));

        if (element.val() > max)
        {
            element.val("+" + max);
        }

        if (element.val() < min)
        {
            if (min <= "0")
                element.val(min);
            else
                element.val("-" + min);
        }
    });



    $.post("ajax/audiograma/audiograma.php", {action: "populate", lead_id: lead_id},
    function(data)
    {
        $.each(data, function()
        {
            $.each(this.value, function()
            {
                $("#audiograma_form #" + this.name).val(this.value);
            });
        });
    }, "json");
};