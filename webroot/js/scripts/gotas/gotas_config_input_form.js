/**
 * @file webroot/js/scripts/gotas/gotas_config_input_form.js
 * @author Gustavo Souza Gonçalves
 * @date 05/09/2017
 * @
 *
 */
$(document).ready(function () {
    // $("#multiplicador_gota").mask("#.##");
    // $("#multiplicador_gota").maskMoney();
    $("#multiplicador_gota").mask("###0.00", {reverse: true});

    var multiplicadorGotaKeyUp = function(element, value){

        if (value > 1000.00){
            value = "1000.00";
        }

        $("#" + element.target.id).val(value);
    }

    $("#multiplicador_gota").on("keyup", function(element){
        multiplicadorGotaKeyUp(element, element.target.value);
    });

	$(".call-modal-how-it-works").on('click', function () {
		callHowItWorks(this.attributes['target-id'].value);
	});
});

