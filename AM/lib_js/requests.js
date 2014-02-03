var request = function(options_ext)
{
    var me = this;
    this.config = new Object();
    $.extend(true, this.config, options_ext);


    this.init = function()
    {

    };

    this.apoio_marketing = function(am_zone)
    {
        $.get("/AM/view/requests/apoio_marketing.html", function(data) {
            am_zone.empty();
            am_zone.append(data);
            $('#apoio_mkt_form').stepy({backLabel: "Anterior", nextLabel: "Seguinte", finishButton: false});
        });
    };
};
