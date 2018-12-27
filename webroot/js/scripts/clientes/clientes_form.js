/**
 * @author Gustavo Souza Gonçalves
 * @date 12/07/2017
 * @
 *
 */

$(document).ready(function(){

	$("#codigo_equipamento_rti").on('blur', function(){
		if (this.value.toString().length == 1)
		{
			this.value = '0' + this.value;
		}
	});

	$("#cnpj").mask('99.999.999/9999-99');
	$("#tel-fixo").mask("(99)9999-9999");
	$("#tel-celular").mask("(99)99999-9999");
	$("#tel-fax").mask("(99)9999-9999");
	$("#cep").mask("99.999-999");

    initializeDatePicker("data_nasc");
});
