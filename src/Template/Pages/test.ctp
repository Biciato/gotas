<legend>Código de Barras</legend>

<table class="table table-responsive table-hover table-bordered text-center">
    <thead>
        <th class="text-center">Code 128</th>
        <th class="text-center">QR Code</th>
        <th class="text-center">PDF 417 Barcode</th>
    </thead>
    <tbody>
        <tr>
            <td><span id='code128'></span></td>
            <td><span id='qrCode'></span></td>
            <td><canvas id='pdf417BarCode'></canvas>
                <div id='canvas_destination'></div>
                <img id="canvas_img" src="" />
            </td>
        </tr>
    </tbody>
</table>


<?php
echo $this->Html->script('scripts/pages/test');
echo $this->fetch("script");

?>