/**
 * @file webroot/js/scripts/gotas/gotas_config_input_form.js
 * @author Gustavo Souza Gonçalves
 * @date 05/09/2017
 * @
 *
 */
'use strict';
$(document).ready(function () {
    // $("#multiplicador_gota").mask("#.##");
    // $("#multiplicador_gota").maskMoney();
    var originalValue = $("#multiplicador_gota").val();

    $("#multiplicador_gota").mask("###0.00", {reverse: true});

    if (originalValue == 1000){
        $("#multiplicador_gota").val("1000.00");
    }

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

