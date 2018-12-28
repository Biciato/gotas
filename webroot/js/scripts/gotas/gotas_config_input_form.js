/**
 * @file webroot/js/scripts/gotas/gotas_config_input_form.js
 * @author Gustavo Souza Gonçalves
 * @date 05/09/2017
 * @
 *
 */
$(document).ready(function () {
    // $("#multiplicador_gota").mask("#.##");
    $("#multiplicador_gota").maskMoney();

	$(".call-modal-how-it-works").on('click', function () {
		callHowItWorks(this.attributes['target-id'].value);
	});
});

