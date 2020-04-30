<?php

?>
<h1><?= $this->fetch('title') ?></h1>
<?= $this->fetch('content') ?>

<!--
<?= $this->Form->input(
    'text',
    [
        'type' => 'text',
        'id' => 'code'
    ]
) ?>

<?= $this->Form->input(
    'button',
    [
        'type' => 'button',
        'id' => 'code_generator'
    ]
) ?>


<canvas id='canvas_origin'>

</canvas>

<div id='canvas_destination'></div>


<script>

$(document).ready(function () {

    $("#code_generator").on('click', function () {

        generateNewPDF417Barcode($("#code").val(), 'canvas_origin', 'canvas_destination' )
    });
});

</script> -->

<!-- <button class="div-modal">
chamar modal com conteúdo
</div>

<button class="div-modal-empty">
chamar modal sem conteúdo
</div> -->

<script>
    // $(document).ready(function () {

    //     $(".div-modal").on('click', function () {

    //         var content = $("<div></div>");

    //         var title = $("<legend>Dados gravados para o usuário </legend>");

    //         var table = $("<table class='table table-responsive table-hover table-condensed'></table>");

    //         var header = $("<thead><th>Gota</th><th>Quantidade</th></thead>");

    //         table.append(header);

    //         var rows = [];

    //         for (index = 0; index < 3; index++) {
    //             var pontuacao = {
    //                 gota: {
    //                     nome_parametro: 'Teste'
    //                 },
    //                 quantidade_gotas: 100.00
    //             };
    //             rows.push(pontuacao);
    //         }

    //         $.each(rows, function (index, pontuacao) {
    //             var row = "<tr><td>" + pontuacao.gota.nome_parametro + "</td><td>" + pontuacao.quantidade_gotas + "</td></tr>";
    //             table.append(row);
    //         });

    //         var total = $("<table class='table table-responsive'><th style='width: 32%'>Total:</th><td> 100</td></table>");

    //         content.append(title);
    //         content.append(table);

    //         content.append(total);

    //         callModalSave(content);
    //     });

    //     $(".div-modal-empty").on('click', function(){
    //         callModalSave();
    //     });
    // });
</script>
