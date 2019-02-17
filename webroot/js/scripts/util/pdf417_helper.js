/**
 *
 * @param {*} text
 * @param {*} object_canvas
 */
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
