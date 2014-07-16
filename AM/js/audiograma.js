var audiograma = function(lead_id) {
    var
            values_regex = /[^0-9\-\+\<\>]+|[\-\+\<\>] *$/g,
            contas_regex = /[^0-9\-]+/g,
            lead_id = lead_id,
            me = this;
    var tooltip_to_validate = 1;
    this.adg = $("#main_audiograma_div");
    me.adg.find("#right_ear").text("Sem dados");
    me.adg.find("#left_ear").text("Sem dados");
    me.adg.find("#audiograma_form").submit(function(e) {
        e.preventDefault();
    });

    this.calculate = function(callback) {
        var right_ear = {
            "value": 0,
            "text": "Sem Perda"
        },
        left_ear = {
            "value": 0,
            "text": "Sem Perda"
        },
        all_ear = {
            "value": 0,
            "text": "Ambos os ouvidos: Sem Perda"
        };

        //APRESENTA SO VALORES CALCULADOS DE CADA OUVIDO


//FALTA TIRAR O TOOLTIP DO CO DIREITO E CO ESQUERDA



        var
                ar500 =  me.adg.find("#AR500").val().replace(contas_regex, ""),
                al500 =  me.adg.find("#AL500").val().replace(contas_regex, ""),
                ar1000 = me.adg.find("#AR1000").val().replace(contas_regex, ""),
                al1000 = me.adg.find("#AL1000").val().replace(contas_regex, ""),
                ar2000 = me.adg.find("#AR2000").val().replace(contas_regex, ""),
                al2000 = me.adg.find("#AL2000").val().replace(contas_regex, ""),
                ar4000 = me.adg.find("#AR4000").val().replace(contas_regex, ""),
                al4000 = me.adg.find("#AL4000").val().replace(contas_regex, "");

        right_ear.value = ((ar500 * 4) + (ar1000 * 3) + (ar2000 * 2) + (ar4000 * 1)) / 10;
        left_ear.value = ((al500 * 4) + (al1000 * 3) + (al2000 * 2) + (al4000 * 1)) / 10;

        if (right_ear.value < 35 && left_ear.value < 35) {
            all_ear.text = "Resultado: Sem perda";
            all_ear.value = 0;
            me.adg.find(".non_required_fields").removeClass("validate[required]");
            tooltip_to_validate = 0;
        } else {
            if (!me.adg.find(".non_required_fields").hasClass("validate[required]"))
                me.adg.find(".non_required_fields").addClass("validate[required]");
            all_ear.text = "Resultado: Perda";
            all_ear.value = 1;
            if (right_ear.value >= 35 && right_ear.value < 65) {
                right_ear.text = "Perda";
            } else if (right_ear.value >= 65) {
                right_ear.text = "Perda Power";
            }
            if (left_ear.value >= 35 && left_ear.value < 65) {
                left_ear.text = "Perda";
            } else if (left_ear.value >= 65) {
                left_ear.text = "Perda Power";
            }
            tooltip_to_validate = 1;
        }

        me.adg.find("#right_ear").text(right_ear.text);
        me.adg.find("#right_ear_value").val(right_ear.value);
        me.adg.find("#left_ear").text(left_ear.text);
        me.adg.find("#left_ear_value").val(left_ear.value);
        me.adg.find("#all_ear").text(all_ear.text);
        me.adg.find("#all_ear_value").val(all_ear.value);
        if (typeof callback === "function") {
            callback();
        }
        return all_ear.value;
    };

    this.save = function(lead_id, reservation_id, callback) { //Grava na BASE DE DADOS
        $.msg();
        $.post("ajax/audiograma.php", {
            action: "save_audiograma",
            lead_id: lead_id,
            reservation_id: reservation_id,
            info: $("#main_audiograma_div #audiograma_form").serializeArray()
        }, function() {
            $.msg('unblock');
            if (typeof callback === "function") {
                callback();
            }
        }, "json").fail(function(data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    };

    this.validate = function(validado, nao_validado) {
        var bcr = 0;
        var bcl = 0;
        var bcstatus = true;
        $.each($("#bcr_tr input"), function() {
            if (!$(this).val())
                bcr = 1;
        });
        $.each($("#bcl_tr input"), function() {
            if (!$(this).val())
                bcl = 1;
        });
 
        if ((bcr && bcl) && tooltip_to_validate) {
            bcstatus = false;
            $('#bc_tooltip').tooltip('show');
        } else
            $('#bc_tooltip').tooltip('hide');


        if (me.adg.find("#audiograma_form").validationEngine('validate') && bcstatus) {
            if (typeof validado === "function") {
                validado();
            }
        } else {
            if (typeof nao_validado === "function") {
                nao_validado();
            }
        }
        return false;
    };



    me.adg.find(".perda_input").on("change", function() {
        var ready = 1;
        $.each(me.adg.find(".perda_input"), function() {
            if (!$(this).val().length){
                ready = 0;
                return false;
            }
        });
        if (ready)
            me.calculate();
    });

    //VALIDATE DOS MAX E MIN VALUES
    me.adg.find("#audiograma_table input").on("focusout", function() {
        var
                element = $(this),
                min = ~~element.data("min"),
                max = ~~element.data("max"),
                val = element.val();

        if (val === "")
            return true;

        val = val.replace(values_regex, "");
        val = Math.round(val / 5) * 5;
        element.val(val);

        if (+val > max) {
            element.val("+" + max);
            return true;
        }

        if (+val < min) {
            if (min <= 0) {
                element.val(min);
                return true;
            } else {
                element.val("-" + min);
                return true;
            }
        }
    }
    );
    $.msg();
    $.post("ajax/audiograma.php", {
        action: "populate",
        lead_id: lead_id
    },
    function(data) {
        $.each(data, function() {
            $.each(this.value, function() {
                me.adg.find("#" + this.name).val(this.value);
            });
        });
        me.calculate();
        $.msg('unblock');
    }, "json").fail(function(data) {
        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
        $.msg('unblock', 5000);
    });


};