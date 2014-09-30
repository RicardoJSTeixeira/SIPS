$(function() {
    $("#select_section").chosen({
        no_results_text: "Sem resultados"
    });
    $("#date_start").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).on('changeDate', function(ev) {
        $("#date_end").datetimepicker('setStartDate', moment($(this).val()).format('YYYY-MM-DD'));
    });
    $("#date_end").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).on('changeDate', function(ev) {
        $("#date_start").datetimepicker('setEndDate', moment($(this).val()).format('YYYY-MM-DD'));
    });
});


$("#button_apply_filters").click(function(e) {
    e.preventDefault();
    if ($("#filter_form").validationEngine("validate")) {
        search();
    }
});


function search()
{
    $.msg();
    $.post("ajax/timeline.php", {action: "get_filtered", date_start: $("#date_start").val(), date_end: $("#date_end").val(), section: $("#select_section").val()}, function(data) {
        if (data) {
            create_timeline(data);
        }
        $.msg('unblock');
    }, "json").fail(function(data) {
        $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
        $.msg('unblock', 5000);
    });
}


function create_timeline(data)
{
    $("#timeline_main").empty();
    var log = "",
        inverted = 0,
        class_badge = "timeline-badge",
        class_badge_icon = "icon-asterisk",
        info = [];
    $.each(data, function() {
        class_badge = "timeline-badge";
        class_badge_icon = "icon-asterisk";
        switch (this.type) {
            case "Update":
                class_badge += "  info";
                class_badge_icon = "icon-edit";
                break;
            case "Remove":
                class_badge += "  danger";
                class_badge_icon = "icon-remove";
                break;
            case "Insert":
                class_badge += "  success";
                class_badge_icon = "icon-plus";
                break;
           }
        inverted=!inverted;


        info = [];
        try {
            var a = JSON.parse(this.note);
            createList(a);
        }
        catch (e) {
            info.push('<ul>')
                .push('<li>' + this.note)
                .push('</li>')
                .push('</ul>');
        }

        function createList(arr) {
            info.push('<ul>');
            $.each(arr, function(i, val) {
                if (i !== "pass") {
                    if (typeof val === "object")
                        info.push('<li>' + i.toString().toUpperCase() + " &#x27a1; " + val.join(" ,"));
                    else
                        info.push('<li>' + i.toString().toUpperCase() + " &#x27a1; " + val);
                }
                if (typeof val === 'object') {
                    createList(val);
                }
                info.push('</li>');
            });
            info.push('</ul>');
        }

        log = ($("<li>", {class: inverted ? "timeline-inverted" : ""})
                .append($("<div>", {class: class_badge})
                        .append($("<i>", {class: class_badge_icon}))
                        )
                .append($("<div>", {class: "timeline-panel"})
                        .append($("<div>", {class: "timeline-heading"})
                                .append($("<h4>", {class: "timeline-title"}).text(this.type + " - " + this.section + " - " + this.record_id))
                                .append($("<h7>").text(" " + moment(this.event_date, "YYYY-MM-DD HH:mm:ss").fromNow() + " - " + this.event_date + " - " + this.username).prepend($("<i>", {class: "icon-time"})))
                                )
                        .append($("<div>", {class: "timeline-body"})
                                .append($("<p>").html(info)
                                        )
                                ))
                );
        $("#timeline_main").prepend(log);
    });
}