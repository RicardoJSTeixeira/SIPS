var AlertBoxes = function()
{
    var
            alert_lst = [],
            me = this,
            alert_timeout = "";

    this.init = function() {
        alert_timeout = setInterval(me.create_alert, 1000);
    };

    this.add = function(item) {
        var found = false;
        for (var i = 0; i < alert_lst.length; i++) {
            if (alert_lst[i].id === item.id)
            {
                alert_lst[i] = item;
                return true;
                found = true;
            }
        }
        if (!found)
            alert_lst.push(item);
        me.create_alert();
    };


    this.create_alert = function(callback) {
        if ($(".modal:visible").length) {
            return false;
        }

        var alert = "";
        if (alert_lst.length)
            alert = alert_lst.pop();
        else
            return false;

        bootbox.alert(alert.message, function(a) {
            if (typeof alert.callback === "function") {
                alert.callback();
            }
        });

        if (typeof callback === "function") {
            callback();
        }

    };

    this.clear = function(callback) {
        alert_lst = [];
        if (typeof callback === "function") {
            callback();
        }
    };

};