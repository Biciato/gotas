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

$title = "Gestão de Gotas";
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);


?>

<?php if ($usuarioLogado->tipo_perfil !== PROFILE_TYPE_WORKER) : ?>
    <nav class="col-lg-3 col-md-2 " id="actions-sidebar">
        <ul class="nav nav-pills nav-stacked">
            <li class="active"><a><?= __('Ações') ?></a></li>
        </ul>
    </nav>
<?php else : ?>

    <?= $this->element('../Pages/left_menu', ["item_selected" => "rel_gestao_gotas"]) ?>
<?php endif; ?>


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
                            <div class="col-lg-3">
                                <label for="brindes_list">Brinde:</label>
                                <select name="brindes_list" id="brindes-list" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label for="funcionarios_list">Funcionário:</label>
                                <select name="funcionarios_list" id="funcionarios-list" class="form-control">
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label for="tipo_relatorio">Tipo Relatório:</label>

                                <select name="tipo_relatorio" id="tipo-relatorio" class="form-control">
                                    <option value="<?= REPORT_TYPE_ANALYTICAL ?>"><?= REPORT_TYPE_ANALYTICAL ?></option>
                                    <option value="<?= REPORT_TYPE_SYNTHETIC ?>" selected><?= REPORT_TYPE_SYNTHETIC ?></option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label for="tipo_movimentacao">Tipo Movimentação:</label>
                                <select name="tipo_movimentacao" id="tipo-movimentacao" class="form-control">
                                    <option value="<?= TYPE_OPERATION_IN ?>"><?= TYPE_OPERATION_IN ?></option>
                                    <option value="<?= TYPE_OPERATION_OUT ?>"><?= TYPE_OPERATION_OUT ?></option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label for="data-inicio">Data Início:</label>
                                <input type="text" class="form-control datepicker-input" format="d/m/Y" name="data-inicio" id="data-inicio" placeholder="Data Início...">
                            </div>
                            <div class="col-lg-2">
                                <label for="data-fim">Data Fim:</label>
                                <input type="text" class="form-control datepicker-input" format="d/m/Y" name="data-fim" id="data-fim" placeholder="Data Início...">
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
                                <div class="imprimir btn btn-success" id="btn-exportar">
                                    <i class="fas fa-file-excel-o"></i>
                                    Exportar
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

        <div class="form-group row">
            <div id="container-report" class="col-lg-12">
            </div>

        </div>
    </div>

</div>


<script src="/webroot/js/scripts/pontuacoes/rel_gestao_gotas<?= $debugExtension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/css/styles/pontuacoes/rel_gestao_gotas<?= $debugExtension ?>.css?version=<?= SYSTEM_VERSION ?>" />
