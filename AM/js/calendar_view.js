$(function() {

    var init = function(data) {
        var
                sch,
                modal_ext = $("#calendar_client_modal"),
                client = getUrlVars();


        function startC(data) {
            sch = new calendar($("#calendar"), data, modal_ext, $('#external-events'), client);

            sch.initModal(modal_ext);
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
            startC(data);
        }


                

    };

    $.post("/AM/ajax/calendar.php",
            {action: "Init"},
    init, "json");
});