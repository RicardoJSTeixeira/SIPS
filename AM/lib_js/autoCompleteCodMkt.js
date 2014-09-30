/**
 * Created by andre on 30-09-2014.
 */
var AutoCompleteCodMkt = function (input) {

    this.init = function () {
        $.msg();
        $.post("ajax/requests.php", {
            action: "get_marketing_code_to_datatable"
        }, function (data) {
            $.msg('unblock');


            input.autocomplete({source:[] });
            if (typeof callback === "function") {
                callback();
            }
        }, "json").fail(function (data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    }


}