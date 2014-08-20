$(function() {

    var init = function(data, user) {
        var
                sch,
                modals = {},
                client = getUrlVars();

        modals.client = $("#calendar_client_modal");
        modals.special = $("#calendar_special_event");
        modals.mkt = $("#calendar_mkt");
        modals.acf = $("#calendar_acf");

        function startC(data, client) {
            sch = new calendar($("#calendar"), data, modals, $('#external-events'), client, user);

            sch.initModal(data.refs);
            sch.reserveConstruct(data.tipo);

            sch.makeRefController(data.refs);
        }
        var client_box;

        if (client.id) {
            client.id = atob(client.id);
            client_box = new clientBox({id: client.id});
            client_box.init(function(clientI) {
                clientI.nc=client.nc;
                startC(data, clientI);
            });
        } else {
            $("#special-event").removeClass("hide");
            startC(data);
        }

    };
    $.post("/AM/ajax/calendar.php",
            {action: "Init"},
    function(data) {
        init(data, SpiceU);
    }, "json");


    $("#special-event-beg")
            .datetimepicker({format: 'yyyy-mm-dd hh:ii', autoclose: true, language: "pt", startDate: moment().format("YYYY-MM-DD hh:ii"), minuteStep: 15})
            .on('changeDate', function() {
                $("#special-event-end").datetimepicker('setStartDate', moment($(this).val(), "YYYY-MM-DD HH:mm").add("minutes", 15).format("YYYY-MM-DD HH:mm"));
            });
    $("#special-event-end")
            .datetimepicker({format: 'yyyy-mm-dd hh:ii', autoclose: true, language: "pt", startDate: moment().format("YYYY-MM-DD hh:ii"), minuteStep: 15})
            .on('changeDate', function() {
                $("#special-event-beg").datetimepicker('setEndDate', moment($(this).val(), "YYYY-MM-DD HH:mm").subtract("minutes", 15).format("YYYY-MM-DD HH:mm"));
            });

    $("#special-event-form").submit(function(e) {
        e.preventDefault();
        var
                that = this,
                start = moment($(that).find("#special-event-beg").val()).unix(),
                end = moment($(that).find("#special-event-end").val()).unix(),
                obs = $(that).find("#special-event-obs").val(),
                rtype = $(that).find("[name=special-event-type]:checked").val();
        $.post("/AM/ajax/calendar.php", {action: "special-event", start: start, end: end, rtype: rtype, obs: obs, resource: $("[name=single-refs]:checked").val()}, function() {
            $.jGrowl("Criado com sucesso.", {sticky: 4000});
            $("[name=single-refs]:checked").change();
            that.reset();
        }, "json");
    });
});