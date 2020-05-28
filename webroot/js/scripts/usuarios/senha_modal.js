/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\usuarios\senha_modal.js
 * @date 23/08/2017
 * 
 */

$(document).ready(function () {
    $("#current_password").on('keydown', function (event) {
        if (event.keyCode == 13)
        {
            $(".modal-confirm").click();
        }   
   })
});