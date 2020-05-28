'use strict';
$(document).ready(function () {


    function convertToASCII(value) {

        return String.fromCharCode(value);
    }

    var generateNewPDF417Barcode = function (text, object_canvas, object_destination, image_destination) {

        if (text.length > 0) {

            PDF417.init(text);
            var barcode = PDF417.getBarcodeArray(),
                bw = 3,
                bh = 3,
                e = "",
                canvas = document.getElementById(object_canvas);
            canvas.width = bw * barcode.num_cols;
            canvas.height = bh * barcode.num_rows;

            document.getElementById(object_destination).appendChild(canvas);
            for (var ctx = canvas.getContext("2d"), y = 0, r = 0; r < barcode.num_rows; ++r) {
                for (var x = 0, c = 0; c < barcode.num_cols; ++c) 1 == barcode.bcode[r][c] && ctx.fillRect(x, y, bw, bh), x += bw;
                y += bh
            };
            canvas.style.display = 'none';

            var img = document.getElementById(image_destination);

            img.src = canvas.toDataURL("image/png");
        }

    }


    function generateDateASCIIValue() {
        var date = new Date();

        var day = date.getDate() + 33;
        // 32 pois mês em javascript começa em 0
        var month = date.getMonth() + 34;
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

    console.log(date);
    var asciiDia = convertToASCII(date.dia);
    var asciiMes = convertToASCII(date.mes);
    var asciiAno = convertToASCII(date.ano);
    var asciiHora = convertToASCII(date.hora);
    var asciiMinuto = convertToASCII(date.minuto);
    var asciiSegundo = convertToASCII(date.segundo);
    var asciiDiaSemana = convertToASCII(date.diaSemana);


    value = value + asciiDia + asciiMes + asciiAno + asciiHora + asciiMinuto + asciiSegundo + asciiDiaSemana;

    console.log(value);

    generateNewPDF417Barcode(value, 'pdf417BarCode', 'canvas_destination', 'canvas_img');
});