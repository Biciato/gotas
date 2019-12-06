<?php

/**
 * src\Template\Gotas\rel_gestao_gotas.ctp
 *
 * Tela de Relatório de Gestão de Gotas - Entrada e Saída
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-08-30
 */

use Cake\Core\Configure;

$debug = Configure::read("debug");
// $debugExtension = $debug ? ".min" : "";
$debugExtension = $debug ? "" : "";

$title = "Gotas de Redes";
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);


?>

<nav class="col-lg-3 col-md-2 " id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
    </ul>
</nav>

<div class="col-lg-9">
    <legend>
        <?= $title ?>
    </legend>

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
                <div>
                    <span class="fa fa-search"></span>
                    Exibir / Ocultar Filtros
                </div>
            </div>
            <div id="filter-coupons" class="panel-collapse collapse in">
                <div class="panel-body">
                    <form id="form">

                        <input type="hidden" name="cliente-selected" id="cliente-selected">
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label for="redes_list">Rede:</label>
                                <select name="redes_list" id="redes-list" class="form-control"></select>
                            </div>
                            <div class="col-lg-3">

                                <label for="clientes_list">Estabelecimento:</label>
                                <select name="clientes_list" id="clientes-list" class="form-control"></select>
                            </div>
                            <div class="col-lg-3">
                                <label for="gotas_list">Referência:</label>
                                <select name="gotas_list" id="gotas-list" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 text-right">
                                <div class="btn btn-primary" id="btn-pesquisar">
                                    <span class="fa fa-search"></span>
                                    Pesquisar
                                </div>
                                <div class="imprimir btn btn-default print-button-thermal" id="btn-imprimir">
                                    <i class="fa fa-print"></i>
                                    Imprimir
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="print-region">

        <h3 class="text-center"><?= $title ?></h3>
        <table class="table table-responsive table-bordered table-hover table-condensed" id="tabela-dados">
            <tbody>
                <span></span>
            </tbody>
        </table>

        <div id='tabela-resumo-brinde'>
            <h3>
                <div>Informações de Brinde "<span id='nome-brinde'></span>"</div>
            </h3>
            <div class="form-group row">

                <div class="col-lg-4">
                    <label for="quantidade_emitida">Quantidade Emitida:</label>
                    <input type="text" name="quantidade_emitida" id="quantidade-emitida" class="form-control text-right" readonly />
                </div>
                <div class="col-lg-4">
                    <label for="total_gotas_brinde">Total Gotas do Brinde:</label>
                    <input type="text" name="total_gotas_brinde" id="total-gotas-brinde" class="form-control text-right" readonly />
                </div>
                <div class="col-lg-4">
                    <label for="total_reais_brinde">Total Reais do Brinde:</label>
                    <input type="text" name="total_reais_brinde" id="total-reais-brinde" class="form-control text-right" readonly />
                </div>
            </div>

        </div>

        <h3>Resumo Sintético</h3>

        <div class="form-group row" id='tabela-resumo-sintetico'>
            <div class="col-lg-6">
                <label for="total_gotas_ontem">Total Gotas até Ontem:</label>
                <input type="text" name="total_gotas_ontem" id="total-gotas-ontem" readonly class="form-control text-right">
            </div>
            <div class="col-lg-6">
                <label for="total_gotas_resgatadas">Total Gotas Resgatadas:</label>
                <input type="text" name="total_gotas_resgatadas" id="total-gotas-resgatadas" readonly class="form-control text-right">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6">
                <label for="gotas_adquiridas_periodo">Total Gotas Adquiridas no Período:</label>
                <input type="text" name="gotas_adquiridas_periodo" id="gotas-adquiridas-periodo" readonly class="form-control text-right">
            </div>
            <div class="col-lg-6">
                <label for="gotas_expiradas_periodo">Gotas expiradas no período:</label>
                <input type="text" name="gotas_expiradas_periodo" id="gotas-expiradas-periodo" readonly class="form-control text-right">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6">
                <label for="caixa_hoje_gotas">Caixa de Hoje - Gotas:</label>
                <input type="text" name="caixa_hoje_gotas" id="caixa-hoje-gotas" readonly class="form-control text-right">
            </div>
            <div class="col-lg-6">
                <label for="caixa_hoje_reais">Caixa de Hoje - Reais:</label>
                <input type="text" name="caixa_hoje_reais" id="caixa-hoje-reais" readonly class="form-control text-right">
            </div>
        </div>
    </div>

</div>


<script src="/webroot/js/scripts/gotas/gotas_redes<?= $debugExtension ?>.js?"<?= SYSTEM_VERSION ?> ></script>
<link rel="stylesheet" href="/webroot/css/styles/gotas/rel_gestao_gotas<?= $debugExtension ?>.css?"<?= SYSTEM_VERSION ?> />
