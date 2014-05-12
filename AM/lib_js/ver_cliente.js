var cliente_info = function(lead_id, options_ext)
{
    var me = this;

    this.config = {};
    $.extend(true, this.config, options_ext);
    this.init = function(callback)
    {



        var client_info = $("<div>"), client_address = $("<div>"), client_extra = $("<div>");
        var temp = "";

        $.post("/AM/ajax/client.php", {action: "byLeadToInfo",
            id: lead_id},
        function(data1)
        {
            $.each(data1, function()
            {
                if (this.value)
                {
                    temp = ($("<div>", {class: "formRow"})
                            .append($("<span>").text(this.original_texto + ":"))
                            .append($("<div>", {class: "formRight"})
                                    .append($("<span>").text(this.value))));
                    switch (this.name)
                    {
                        case "first_name":
                        case "phone_number":
                        case "alt_phone":
                        case "email":
                            client_info.append(temp);
                            break;
                        case "address1":
                        case "city":
                        case "postal_code":
                            client_address.append(temp);
                            break;

                        case "comments":
                        case "extra1":
                        case "extra2":
                            client_extra.append(temp);
                            break;
                    }
                }
            }); 
            var final = ($("<div>", {class: "row-fluid"}).append($("<div>", {class: "span6"}).append($("<h4>", {class:"icon-user"}).text(" Info de cliente")).append(client_info))
                    .append($("<div>", {class: "span6"}).append($("<h4>", {class:"icon-home"}).text(" Morada")).append(client_address))
                    .append($("<div>", {class: "span12"}).append($("<h4>", {class:"icon-star"}).text(" Info Extra")).append(client_extra)));
                    bootbox.alert(final);
        }, "json");
        if (typeof callback === "function")
            callback();

    };
};