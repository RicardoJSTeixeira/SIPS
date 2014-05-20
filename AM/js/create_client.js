$(function()
{

    $('#form_create_client').stepy(
            {
                backLabel: "Anterior",
                nextLabel: "Seguinte",
                next: function() {
                    return ($(this).validationEngine("validate"));
                },
                finishButton: false
            });

    $("#verif_client_data").validationEngine();
    $.post("ajax/create_client.php", {action: "get_fields"},
    function(data)
    {
        $("#inputs_div1").empty();
        $("#inputs_div2").empty();
        $("#inputs_div3").empty();
        var input,
                custom_class = "",
                input1 = $("#inputs_div1"),
                input2 = $("#inputs_div2"),
                input3 = $("#inputs_div3"),
                elmt,
                specialE,
                hide = "";
        $.each(data, function()
        {
 
            if (this.name === "extra5") {
                elmt = $("<select>", {id: this.name, name: this.name});
                var optionsRaw = ["", "ADM (ADME/ADMA/ADMFA)", "ADSE", "APL", "CGD", "Centro Nac. de Protecção Contra Riscos Profissionais", "EDP", "PETROGAL", "PT/CTT ACS", "SAD-PSP", "SAD/GNR (ADMG)", "SAMS", "SEG. SOCIAL", "Serviços Sociais do Ministério da Justiça", "OUTRAS"];
                options = optionsRaw.map(function(v) {
                    return new Option(v, v);
                });
                magia=function() {
                            if ($(this).val() === "OUTRAS") {
                                $(this).replaceWith(
                                        $('<div>', {class: 'input-append'})
                                        .append(
                                                $("<input>", {type: "text", id: $(this).prop("id"), name: $(this).prop("name")})
                                                )
                                        .append(
                                                $('<btn>', {class: 'btn icon-alone'})
                                                .append(
                                                        $('<i>', {class: 'icon-undo'})
                                                        )
                                                .click(function() {
                                                    $(this).parent().replaceWith(specialE.val("").change(magia));
                                                })
                                                )
                                        );
                            }
                        };
                elmt
                        .append(options)
                        .change(magia);
                specialE = elmt;
            } else if (this.name === "TITLE") {
                elmt = $(mt = $("<select>", {id: this.name, name: this.name, class: "input-mini"}).attr('data-prompt-position','topRight:120').append([new Option("", ""), new Option("Sr.", "Sr."), new Option("Sra. D.", "Sra. D.")]));
            } else if (this.name === "extra6") {
                elmt = $("<input>", {type: "text", readonly: true, id: this.name, name: this.name, value: "NO"});
            } else {
                elmt = $("<input>", {type: "text", id: this.name, name: this.name});
            }
            switch (this.name)
            {
                case "PHONE_NUMBER":
                    custom_class = "validate[required,custom[onlyNumberSp],minSize[9]]";
                    input = input1;
                    break;
                case "ADDRESS3":
                case "ALT_PHONE":
                    custom_class = "validate[custom[onlyNumberSp]]";
                    input = input1;
                    break;

                case "FIRST_NAME":
                    custom_class = "validate[required]";
                    input = input1;
                    break;
                case "DATE_OF_BIRTH":
                    custom_class = "form_datetime  input-small validate[required]";
                    input = input1;
                    break;
                case "EMAIL":
                    input = input1;
                    custom_class = "validate[custom[email]]";
                    break;
                case "TITLE":
                    custom_class = "validate[required]";
                case "MIDDLE_INITIAL":
                    input = input1;
                    break;
                case "extra2":
                case "LAST_NAME":
                    input = input1;
                    break;
                case "ADDRESS1":
                case "CITY":
                    custom_class = "validate[required]";
                case "ADDRESS2":
                case "POSTAL_CODE":
                case "PROVINCE":
                case "STATE":
                case "COUNTRY_CODE":
                case "extra3":
                case "extra4":
                case "extra10":
                    input = input2;
                    break;
                case "extra6":
                case "extra7":
                    hide = " hide";
                    break;
                default:
                    hide = "";
                    input = input3;
                    break;
            }
            elmt.addClass(custom_class);

            input.append($("<div>", {class: "formRow" + hide})
                    .append($("<label>").text(this.display_name))
                    .append($("<div>", {class: "formRight"})
                            .append(elmt)));
            custom_class = "";
        });
        $("#inputs_div1").append($("<div>", {class: "clear"}));
        $("#inputs_div2").append($("<div>", {class: "clear"}));
        $("#inputs_div3").append($("<div>", {class: "clear"}));
        $("#PHONE_NUMBER").autotab('numeric');
        $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, initialDate: new Date(moment().subtract('years', 65).format())}).attr('data-prompt-position','topRight:120');
    }
    , "json");

});

$("#form_create_client").submit(function(e)
{
    e.preventDefault();
    if ($("#form_create_client").validationEngine("validate"))
    {
        $.post("ajax/create_client.php", {action: "create_client", info: $("#form_create_client").serializeArray()},
        function(id)
        {
            $.jGrowl("Cliente '" + $("#FIRST_NAME").val() + "' criado com Sucesso", {sticky: 8000});
            $("#criar_marcacao").data("client_id", id).modal("show");
        }
        , "json");
    }
});


$("#btn_criar_marcacao").on("click", function()
{
    var en = btoa($("#criar_marcacao").modal("hide").data("client_id"));
    $.history.push("view/calendar.html?id=" + en);
});