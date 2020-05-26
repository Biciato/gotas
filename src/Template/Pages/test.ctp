<html>

<head>
    <script src="/webroot/js/jquery/jquery.min.js"></script>
    <script src="/webroot/js/pdf417-gh-pages/pdf417.js"></script>
    <script src="/webroot/js/scripts/pages/test.js"></script>
    <title>Barcode</title>
</head>

<body>
    <legend>Código de Barras</legend>

    <table class="table table-responsive table-hover table-bordered text-center">
        <thead>
            <th class="text-center">PDF 417 Barcode</th>
        </thead>
        <tbody>
            <tr>
                <td><canvas id='pdf417BarCode'></canvas>
                    <div id='canvas_destination'></div>
                    <img id="canvas_img" src="" />
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>