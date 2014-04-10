$(function() {

    var init = function(data, user) {
        var
                sch,
                modals = {},
                client = getUrlVars();

        modals.client = $("#calendar_client_modal");
        modals.special = $("#calendar_special_event");

        function startC(data) {
            sch = new calendar($("#calendar"), data, modals, $('#external-events'), client, user);

            sch.initModal(data.refs);
            sch.reserveConstruct(data.tipo);

            sch.makeRefController(data.refs);
        }

        if (client.id) {
            client.id = atob(client.id);
            $.post("/AM/ajax/client.php", {action: "default", id: client.id}, function(clientI) {
                client = clientI;
                startC(data);
                sch.userWidgetPopulate();
            }, "json");
        } else {
            $("#special-event").removeClass("hide");
            startC(data);
        }

    };
    $.post("/AM/ajax/user_info.php", function(user) {
        $.post("/AM/ajax/calendar.php",
                {action: "Init"},
        function(data) {
            init(data, user);
        }, "json");
    }, "json");


    $("#special-event-beg")
            .datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, startDate: moment().format("YYYY-MM-DD")})
            .on('changeDate', function() {
                $("#special-event-end").datetimepicker('setStartDate', moment($(this).val()).format('YYYY-MM-DD'));
            });
    $("#special-event-end")
            .datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, startDate: moment().format("YYYY-MM-DD")})
            .on('changeDate', function() {
                $("#datetime_from").datetimepicker('setEndDate', moment($(this).val()).format('YYYY-MM-DD'));
            });

    $("#special-event-form").submit(function(e) {
        e.preventDefault();
        var
                that = this,
                start = moment($(that).find("#special-event-beg").val()).unix(),
                end = moment($(that).find("#special-event-end").val()).add("hours", 23).unix(),
                obs = $(that).find("#special-event-obs").val(),
                rtype = $(that).find("[name=special-event-type]:checked").val();
        $.post("/AM/ajax/calendar.php", {action: "special-event", start: start, end: end, rtype: rtype, obs: obs}, function(data) {
            $.jGrowl("Criado com sucesso.", {sticky: 4000});
            $("[name=single-refs]:checked").change();
            that.reset();
        }, "json");
    });
});