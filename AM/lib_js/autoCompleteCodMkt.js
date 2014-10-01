/**
 * Created by andre on 30-09-2014.
 */
var AutoCompleteCodMkt = function (input,modal) {

    this.init = function () {
        $.msg();
        $.post("ajax/requests.php", {
            action: "get_marketing_code_to_datatable"
        }, function (data) {
            $.msg('unblock');
            var codes = [];
            for (var i = 0; i < data["aaData"].length; i++) {
                codes.push(data["aaData"][i] [1]);
            }
            if(modal)
                input.autocomplete({source: codes,   appendTo : input.parents(".modal")});
            else
                input.autocomplete({source: codes});

        }, "json").fail(function (data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });

    }


}