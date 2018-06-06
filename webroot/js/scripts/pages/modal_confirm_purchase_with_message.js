/**
 * Classe javascript para ações de uso de janela modal
 * @author   Gustavo Souza Gonçalves
 * @file     webroot/js/scripts/pages/modal_confirm_purchase_with_message.js
 * @date     27/01/2018
 */


$(document).ready(function () {
    $("#submit_button").on('click', function () {
        // $(".form_resgate_brinde").submit();

        $.post($(".form_resgate_brinde").attr('action'), $(".form_resgate_brinde").serialize(),
            function (data, textStatus, jqXHR) {
                console.log(data);    
            }
        );

    });
});
