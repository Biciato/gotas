<?php

/**
 * src\Template\Gotas\relatorio_entrada_saida.ctp
 *
 * Tela de Relatório de Caixa dos Funcionários de um Estabelecimento. Visualizado por um gerente
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-08-30
 */

use Cake\Core\Configure;

$debug = Configure::read("debug");
// $debugExtension = $debug ? ".min" : "";
$debugExtension = $debug ? "" : "";

$title = "Relatório de Gotas - Entrada e Saída";
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

                        <input type="hidden" name="cliente-selected" id="cliente-selected" value="<?= $clientesId ?>">
                        <div class="form-group row">
                            <div class="col-lg-3">

                                <label for="funcionario">Estabelecimento:</label>
                                <select name="clientes-list" id="clientes-list" class="form-control"></select>
                            </div>
                            <div class="col-lg-3">

                                <label for="brinde">Brinde:</label>
                                <select name="brindes-list" id="brindes-list" class="form-control"></select>

                            </div>

                            <div class="col-lg-2">
                                <label for="data-inicio">Data Início:</label>
                                <input type="text" class="form-control datepicker-input" format="d/m/Y" name="data-inicio" id="data-inicio" placeholder="Data Início...">
                            </div>
                            <div class="col-lg-2">
                                <label for="data-fim">Data Fim:</label>
                                <input type="text" class="form-control datepicker-input" format="d/m/Y" name="data-fim" id="data-fim" placeholder="Data Início...">
                            </div>

                            <div class="col-lg-2">
                                <label for="tipo_relatorio">Tipo de Relatório:</label>

                                <select name="tipo_relatorio" id="tipo-relatorio" class="form-control">
                                    <option value="<?= REPORT_TYPE_ANALYTICAL ?>"><?= REPORT_TYPE_ANALYTICAL ?></option>
                                    <option value="<?= REPORT_TYPE_SYNTHETIC ?>" selected><?= REPORT_TYPE_SYNTHETIC ?></option>
                                </select>
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
    <table class="table table-responsive table-bordered table-hover table-condensed" id="tabela-dados">
        <tbody>
            <span></span>
        </tbody>
    </table>

    <h3>Resumo</h3>

    <div class="form-group row">
        <div class="col-lg-6">
            <label for="total_gotas_ontem">Total Gotas até Ontem</label>
            <input type="text" name="total_gotas_ontem" id="total-gotas-ontem" readonly class="form-control">
        </div>
        <div class="col-lg-6">
            <label for="total_gotas_resgatadas">Total Gotas Resgatadas</label>
            <input type="text" name="total_gotas_resgatadas" id="total-gotas-resgatadas" readonly class="form-control">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            <label for="gotas_adquiridas_periodo">Total Gotas Adquiridas no Período:</label>
            <input type="text" name="gotas_adquiridas_periodo" id="gotas-adquiridas-periodo" readonly class="form-control">
        </div>
        <div class="col-lg-6">
            <label for="gotas_expiradas_periodo">Gotas expiradas no período</label>
            <input type="text" name="gotas_expiradas_periodo" id="gotas-expiradas-periodo" readonly class="form-control">
        </div>
    </div>
</div>


<script src="/webroot/js/scripts/pontuacoes/relatorio_entrada_saida<?= $debugExtension ?>.js"></script>
<link rel="stylesheet" href="/webroot/css/styles/pontuacoes/relatorio_entrada_saida<?= $debugExtension ?>.css" />
