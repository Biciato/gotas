/**
 * @file webroot/js/scripts/gotas/impressao_brinde_layout.js
 * @author Gustavo Souza Gonçalves
 * @date 17/06/2018
 * @
 *
 */

$(document).ready(function () {


    // generateNewPDF417Barcode($(".impressao-cupom .cupom_emitido").text(), 'canvas_origin', 'canvas_destination', 'canvas_img');
    generateNewPDF417Barcode("", 'canvas_origin', 'canvas_destination', 'canvas_img');

});
