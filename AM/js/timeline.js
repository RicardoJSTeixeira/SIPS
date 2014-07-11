$(function() {

    $.msg();
    $.post("ajax/timeline.php", {action: "get"}, function(data) {

        if (data)
        {
            var log = "";
            var inverted = 0;
            var class_badge = "timeline-badge";
            var class_badge_icon = "icon-asterisk";
            var info = [];
            $.each(data, function() {



//fazer limite de tempo e sections
//admin abo, que so ve produtos e stuff minor




                class_badge = "timeline-badge";
                class_badge_icon = "icon-asterisk";
            

                switch (this.type)
                {
                    case "Update":
                        class_badge += "  info";
                        class_badge_icon = "icon-edit";
                        inverted = 1;
                        break;

                    case "Remove":
                        class_badge += "  danger";
                        class_badge_icon = "icon-remove";
                        inverted = 0;
                        break;

                    case "Insert":
                        class_badge += "  success";
                        class_badge_icon = "icon-plus";
                        inverted = 0;
                        break;
                    default:
                        inverted = 1;
                        break;
                }




                info = [];
                try {
                    var a = JSON.parse(this.note);
                    createList(a);
                }
                catch (e) {
                    info.push('<ul>');
                    info.push('<li>' + this.note);
                    info.push('</li>');
                    info.push('</ul>');
                }


                function createList(arr) {
                    info.push('<ul>');
                    $.each(arr, function(i, val) {
                        if (i !== "pass")
                        {
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
                                        .append($("<h7>").text(" " + moment(this.event_date, "YYYY-MM-DD HH:mm:ss").fromNow() + " - " + this.event_date).prepend($("<i>", {class: "icon-time"})))
                                        )
                                .append($("<div>", {class: "timeline-body"})
                                        .append($("<p>").html(info)
                                                )
                                        ))
                        );


                $("#timeline_main").append(log);
            })

        }
        $.msg('unblock');

    }, "json").fail(function(data) {
        $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
        $.msg('unblock', 5000);
    });


})


/*
 
 <li>
 <div class="timeline-badge info"><i class="glyphicon glyphicon-hand-left"></i></div>
 <div class="timeline-panel">
 <div class="timeline-heading">
 <h4 class="timeline-title">Bootstrap released</h4>
 <p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> August 2011</small></p>
 </div>
 <div class="timeline-body">
 <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis pharetra varius quam sit amet vulputate. 
 Quisque mauris augue, molestie tincidunt condimentum vitae, gravida a libero. Aenean sit amet felis 
 dolor, in sagittis nisi. Sed ac orci quis tortor imperdiet venenatis. Duis elementum auctor accumsan. 
 Aliquam in felis sit amet augue.</p>
 </div>
 </div>
 </li>
 <li class="timeline-inverted">
 <div class="timeline-badge warning"><i class="glyphicon glyphicon-chevron-right"></i></div>
 <div class="timeline-panel">
 <div class="timeline-heading">
 <h4 class="timeline-title">Bootstrap 2</h4>
 </div>
 <div class="timeline-body">
 <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis pharetra varius quam sit amet vulputate. 
 Quisque mauris augue, molestie tincidunt condimentum vitae, gravida a libero. Aenean sit amet felis 
 dolor, in sagittis nisi. Sed ac orci quis tortor imperdiet venenatis. Duis elementum auctor accumsan. 
 Aliquam in felis sit amet augue.</p>
 </div>
 </div>
 </li>
 
 
 
 
 
 
 
 
 
 
 
 
 
 */