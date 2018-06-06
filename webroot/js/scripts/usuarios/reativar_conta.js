/**
 * @author Gustavo Souza Gonçalves
 * @date 28/07/2017
 * @
 * 
 */

$(document).ready(function(){
	$("#data_nasc_display").datetimepicker({
        minView: 2,
        maxView: 4,
        clearBtn: true,
        format: 'dd/mm/yyyy',
        linkField: "data_nasc",
        linkFormat: "yyyy-mm-dd"
    });
});