var alerts_class = function()
{

    var alert_lst = [];



    this.add = function(item)
    {
        alert_lst.push(item);

        this.create_alert();
    }



    this.remove = function(callback)
    {
        alert_lst.pop();
        if (typeof callback === "function") {
            callback();
        }
    }

    this.search = function(id, callback)
    {
        var found = false;
        $.each(alert_lst, function()
        {
            if (id = this.id)
                found = true;
        });

        return found;
        if (typeof callback === "function") {
            callback();
        }
    }


    this.create_alert = function(callback)
    {

        if ($(".modal:visible").length)
        {
            return false;
        }
        if ($(".bootbox").length)
        {
            $(".bootbox .modal-footer a").click(function()
            {
                this.create_alert();
            })
            return false;
        }
        $.each(alert_lst, function()
        {
            bootbox.alert(this.message, function(a) {
                if (typeof this.callback === "function") {
                    this.callback();
                }
            });
        });


        if (typeof callback === "function") {
            callback();
        }
    }
}