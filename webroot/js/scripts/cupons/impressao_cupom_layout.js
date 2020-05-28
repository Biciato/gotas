/**
 * @file webroot/js/scripts/gotas/impressao_cupom_layout.js
 * @author Gustavo Souza Gonçalves
 * @date 29/01/2018
 * @
 * 
 */

$(document).ready(function () {


    generateNewPDF417Barcode($(".impressao-cupom-comum .cupom_emitido").text(), 'canvas_origin', 'canvas_destination', 'canvas_img');

});
