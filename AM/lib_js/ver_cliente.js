var cliente_info = function(lead_id, options_ext)
{
    var me = this;

    this.config = {};
    $.extend(true, this.config, options_ext);
    this.init = function(callback)
    {



        var client_info = $("<div>"), client_address = $("<div>"), client_extra = $("<div>");
        var client_extra_count = 0;
        var temp = "";

        $.post("/AM/ajax/client.php", {action: "byLeadToInfo",
            id: lead_id},
        function(data1)
        {
            console.log(data1);
            $.each(data1, function()
            {
                if (this.value)
                {
                    temp = ($("<div>", {class: "formRow"})
                            .append($("<span>").text(this.display_name + ":"))
                            .append($("<div>", {class: "right"})
                                    .append($("<span>").text(this.value))));
                    switch (this.name.toLowerCase())
                    {
                        case "first_name":
                        case "middle_initial":
                        case "last_name":
                        case "phone_number":
                        case "alt_phone":
                        case "email":
                        case "date_of_birth":
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
                        case "extra8":
                            client_extra.append(temp);
                            client_extra_count++;
                            break;
                    }
                }
            });
            var final = ($("<div>", {class: "row-fluid"}).append($("<div>", {class: "span6"}).append($("<h4>", {class: "icon-user"}).text(" Info de cliente")).append(client_info))
                    .append($("<div>", {class: "span6  "}).append($("<h4>", {class: "icon-home"}).text(" Morada")).append(client_address)))
                    .append($("<div>", {class: "row-fluid"}).append($("<div>", {class: "span7 ", id: "extra_info_div"}).append($("<h4>", {class: "icon-star"}).text(" Info Extra")).append(client_extra)));
            if (!client_extra_count)
                final.find("#extra_info_div").parent().remove();
            bootbox.alert(final);
        }, "json");
        if (typeof callback === "function")
            callback();

    };
};