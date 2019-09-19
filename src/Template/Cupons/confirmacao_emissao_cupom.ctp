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

    <div class="form-group row text-center">
        <div class="col-lg-3">
            <a href="<?= "/".strtolower(implode("/", array_values($urlRedirectConfirmacao))) ?>" class="btn btn-primary">
                <i class="fas fa-check"></i>
                Concluir
            </a>
        </div>
        <div class="col-lg-3">
            <button type="button" id="imprimir-canhoto" class="imprimir-canhoto btn btn-primary">
                <i class="fas fa-check"></i>
                Imprimir Canhoto
            </button>
        </div>
        <div class="col-lg-3">
            <button type="button" id="validar-brinde" class="imprimir-canhoto btn btn-primary">
                <i class="fas fa-check"></i>
                Validar Brinde
            </button>
        </div>
        <div class="col-lg-3">
            <button type="button" id="reimpressao-cupom" class="reimpressao-cupom btn btn-danger">
                <i class="fas fa-check"></i>
                Reimprimir
            </button>
        </div>

    </div>
</div>
