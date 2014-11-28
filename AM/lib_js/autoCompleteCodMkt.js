/**
 * Created by andre on 30-09-2014.
 */
var AutoCompleteCodMkt = function (input, modal) {

    this.init = function () {
        $.msg();
        $.post("ajax/requests.php", {
            action: "get_marketing_code_to_datatable"
        }, function (data) {
            $.msg('unblock');
            var codes = [];
            for (var i = 0; i < data["aaData"].length; i++) {
                codes.push({value: data["aaData"][i][1], desc: data["aaData"][i][2]});
            }
            if (modal)
                input.autocomplete({source: codes, appendTo: input.parents(".modal")});
            else
                input.autocomplete({source: codes});

            input.data("uiAutocomplete")._renderItem = function (ul, item) {
                return $("<li>")
                    .append("<a>" + item.value + "<br>" + item.desc + "</a>")
                    .appendTo(ul);
            };

        }, "json")
            .fail(function (data) {
                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                $.msg('unblock', 5000);
            });

    }


};