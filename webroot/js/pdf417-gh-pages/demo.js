PDF417.init(code);
var barcode = PDF417.getBarcodeArray(),
    bw = 2,
    bh = 2,
    e = "",
    canvas = document.createElement("canvas");
canvas.width = bw * barcode.num_cols;
canvas.height = bh * barcode.num_rows;
document.getElementById("barcode").appendChild(canvas);
for (var ctx = canvas.getContext("2d"), y = 0, r = 0; r < barcode.num_rows; ++r) {
    for (var x = 0, c = 0; c < barcode.num_cols; ++c) 1 == barcode.bcode[r][c] && ctx.fillRect(x, y, bw, bh), x += bw;
    y += bh
};
