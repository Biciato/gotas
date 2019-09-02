<?php

/**
 * src\Template\Gotas\relatorio_entrada_saida.ctp
 *
 * Tela de Relatório de Caixa dos Funcionários de um Posto. Visualizado por um gerente
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
                    <form action="/cupons/relatorioCaixaFuncionariosGerente/" method="post">
                        <div class="form-group row">
                            <div class="col-lg-4">

                                <label for="funcionario">Posto:</label>
                                <select name="clientesList" id="clientesList" class="form-control"></select>
                            </div>
                            <div class="col-lg-4">

                                <label for="brinde">Brinde:</label>
                                <?= $this->Form->input(
                                    "brinde",
                                    array(
                                        "type" => "select",
                                        "name" => "brinde",
                                        "id" => "brinde",
                                        "empty" => "Selecionar...",
                                        "placeholder" => "Brinde...",
                                        "label" => false,
                                        "options" => $brindesList,
                                        "value" => $brindeSelecionado,
                                    )
                                );
                                ?>

                            </div>

                            <div class="col-lg-2">
                                <label for="dataInicio">Data de Pesquisa:</label>
                                <input type="text" class="form-control datepicker-input" format="d/m/Y" name="data_pesquisa" id="data_pesquisa" placeholder="Data Início...">
                            </div>

                            <div class="col-lg-2">
                                <label for="tipo_relatorio">Tipo de Relatório:</label>
                                <?= $this->Form->input(
                                    "tipo_relatorio",
                                    array(
                                        "type" => "select",
                                        "name" => "tipo_relatorio",
                                        "id" => "tipo_relatorio",
                                        "label" => false,
                                        "options" => array(
                                            REPORT_TYPE_ANALYTICAL => REPORT_TYPE_ANALYTICAL,
                                            REPORT_TYPE_SYNTHETIC => REPORT_TYPE_SYNTHETIC
                                        ),
                                        "value" => $tipoRelatorio
                                    )
                                ); ?>
                            </div>

                            <input type="hidden" name="data_pesquisa_envio" id="data_pesquisa_envio" value="<?= $dataPesquisa ?>" class="data-pesquisa-envio">

                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary botao-pesquisar">
                                    <span class="fa fa-search"></span>
                                    Pesquisar
                                </button>
                                <?php if (count($dadosRelatorio) > 0) : ?>
                                    <button type="button" class="imprimir btn btn-default print-button-thermal" id="imprimir">
                                        <i class="fa fa-print"></i>
                                        Impressora Térmica
                                    </button>
                                    <button type="button" class="imprimir btn btn-default print-button-common" id="imprimir">
                                        <i class="fa fa-print"></i>
                                        Impressora Comum
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>


<script src="/webroot/js/scripts/gotas/relatorio_entrada_saida<?= $debugExtension ?>.js"></script>
<link rel="stylesheet" href="/webroot/css/styles/gotas/relatorio_entrada_saida<?= $debugExtension ?>.css" />
