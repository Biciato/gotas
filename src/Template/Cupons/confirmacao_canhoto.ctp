<?php

/**
 * @category View
 * @package  App\Template
 * @file     \src\Template\Cupons\confirmacao_canhoto.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     22/06/2018
 */
?>
  <!-- Layout de confirmação da impressão do canhoto  -->
<div class="container-confirmacao-canhoto">

    <legend>Confirmação de Impressão do Canhoto</legend>

    <h4>O canhoto foi emitido com sucesso?</h4>
    <div class="form-group row">
        <div class="col-lg-2"></div>
        <div class="col-lg-3">
            <!-- Confirmação de impressão do canhoto SMART Shower -->
            <?= $this->Html->link(
            __("{0} Sim", $this->Html->tag("i", '', ['class' => 'fa fa-check'])),
            $urlRedirectConfirmacao,
            ['escape' => false, 'class' => 'btn btn-primary btn-block']
        ); ?>
        </div>
        <div class="col-lg-2"></div>
        <div class="col-lg-3">
            <!-- Reimprime canhoto smart shower -->
            <?= $this->Html->tag('button', __("{0} Não, Reimprimir", $this->Html->tag('i', '', ['class' => 'fa fa-remove'])), [
            'id' => 'reimpressao-canhoto',
            'class' => 'reimpressao-canhoto btn btn-danger btn-block'
        ]) ?>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
