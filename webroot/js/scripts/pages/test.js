'use strict';
$(document).ready(function () {


    function convertToASCII(value) {

        return String.fromCharCode(value);
    }
    
    function generateDateASCIIValue() {
        var date = new Date();

        var day = date.getDate() + 33;
        // 32 pois mês em javascript começa em 0
        var month = date.getMonth() + 32;
        var year = date.getFullYear() + 33;
        year = year.toString().substr(2);
        var second = date.getSeconds() + 33;
        var minute = date.getMinutes() + 33;
        var hour = date.getHours() + 33;
        var dayOfWeek = date.getDay() + 1 + 33;

        var value = {
            dia: day,
            mes: month,
            ano: parseInt(year),
            segundo: second,
            minuto: minute,
            hora: hour,
            diaSemana: dayOfWeek
        };

        return value;
    }

    var value = "##*1";

    var date = generateDateASCIIValue();

    // console.log(date);
    var asciiDia = convertToASCII(date.dia);
    var asciiMes = convertToASCII(date.mes);
    var asciiAno = convertToASCII(date.ano);
    var asciiHora = convertToASCII(date.hora);
    var asciiMinuto = convertToASCII(date.minuto);
    var asciiSegundo = convertToASCII(date.segundo);
    var asciiDiaSemana = convertToASCII(date.diaSemana);
   
   
    value = value + asciiDia + asciiMes + asciiAno + asciiHora + asciiMinuto + asciiSegundo + asciiDiaSemana;

    // console.log(value);

     // Gera código code128
     $("#code128").barcode(value, "code128", {
        barWidth: 2,
        barHeight: 70,
        showHRI: false,
        output: "bmp"
    });

    generateQRCode("#qrCode", value);

    generateNewPDF417Barcode(value, 'pdf417BarCode', 'canvas_destination', 'canvas_img');
});