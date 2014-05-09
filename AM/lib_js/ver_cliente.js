var cliente_info = function(lead_id, options_ext)
{
    var me = this;

    this.config = {};
    $.extend(true, this.config, options_ext);
    this.init = function(callback)
    {

        var html = ($("<div>", {class: "summary-title items-info-profile pull-left", id: "profile_info"}));

        $.post("/AM/ajax/client.php", {action: "byLeadToInfo",
            id: lead_id},
        function(data1)
        {

            var destiny = "#first_column";



            $.each(data1, function()
            {

                switch (this.name)
                {
                    case "first_name":
                    case "phone_number":
                    case "address1":
                    case "city":
                    case "alt_phone":
                    case "postal_code":
                        destiny = "#profile_info";
                        break;
                    case "email":
                    case "comments":
                    case "extra1":
                    case "extra2":
                        // destiny = "#second_column";
                        break;
                }

               
                    html.find(destiny).append($("<div>", {class: "info-profile"})
                            .append($("<div>", {class: "info-left-profile"}).text(this.original_texto))
                            .append($("<div>", {class: "info-right-profile"}).text(this.value)));
            });


            bootbox.alert(html);

        }, "json");
        if (typeof callback === "function")
            callback();

    };
};