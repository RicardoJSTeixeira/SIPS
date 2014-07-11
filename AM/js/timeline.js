$(function() {

    $.msg();
    $.post("ajax/timeline.php", {action: "get_log_history"}, function(data) {

        if (data)
        {
            var log = "";
            var inverted = 0;
            $.each(data, function()
            {
                console.log(this);
                log = ($("<li>", {class: inverted ? "timeline-inverted" : ""})
                        .append($("<div>", {class: "timeline-badge info"})
                                .append($("<i>", {class: "glyphicon glyphicon-hand-left"}))
                                )
                        .append($("<div>", {class: "timeline-panel"})
                                .append($("<div>", {class: "timeline-heading"})
                                        .append($("<h4>", {class: "timeline-title"}).text(this.type + "-" + this.section))
                                        )
                                .append($("<div>", {class: "timeline-body"})
                                        .append($("<p>").text("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                                )
                                        ))
                        );
                if (inverted)
                    inverted = 0;
                else
                    inverted = 1;

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