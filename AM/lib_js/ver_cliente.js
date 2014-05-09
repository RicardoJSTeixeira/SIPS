var cliente_info = function(lead_id, options_ext)
{
    var me = this;

    this.config = {};
    $.extend(true, this.config, options_ext);
    this.init = function(callback)
    {


        $.post("/AM/ajax/client.php", {action: "default",
            id: lead_id},
        function(data1)
        {

            console.log(data1);

        }, "json");
        if (typeof callback === "function")
            callback();

    };
};