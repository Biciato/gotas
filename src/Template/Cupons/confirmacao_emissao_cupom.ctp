<?php

/**
 * @category View
 * @package  App\Template
 * @file     \src\Template\Cupons\confirmacao_canhoto.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     22/06/2018
 */
?>
<!-- Layout de confirmação da impressão do cupom  -->
<div class="container-confirmacao-cupom">
    <legend>Confirmação de emissão</legend>

    <h4>O Cupom foi emitido com sucesso?</h4>

    <div class="form-group row">
        <div class="col-lg-2"></div>
        <div class="col-lg-3">
            <!-- Sim Impressão Brinde Smart  -->

            <?= $this->Html->tag('button', __("{0} Sim, Imprimir Canhoto", $this->Html->tag('i', '', ['class' => 'fa fa-check'])), [
                    'id' => 'imprimir-canhoto',
                    'class' => 'imprimir-canhoto btn btn-primary btn-block'
                ]) ?>

        </div>
        <div class="col-lg-2"></div>
        <div class="col-lg-3">
            <!-- Sim Impressão Brinde Smart  -->

            <?= $this->Html->tag('button', __("{0} Não, Reimprimir", $this->Html->tag('i', '', ['class' => 'fa fa-remove'])), [
                    'id' => 'reimpressao-cupom',
                    'class' => 'reimpressao-cupom btn btn-danger btn-block'
                ]) ?>

        </div>
        <div class="col-lg-2"></div>

    </div>
</div>
