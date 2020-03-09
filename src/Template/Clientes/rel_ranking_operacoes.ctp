<?php

/**
 * src\Template\Clientes\rel_ranking_operacoes.ctp
 *
 * Tela de Relatório de Ranking de Operações
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.1.6
 * @date 2020-03-04
 */

use Cake\Core\Configure;

$debug = Configure::read("debug");
// $debugExtension = $debug ? ".min" : "";
$debugExtension = $debug ? "" : "";

$title = "Ranking de Operações";
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
                            <div class="col-lg-4">
                                <label for="redes_list">Rede:</label>
                                <select name="redes_list" id="redes-list" class="form-control"></select>
                            </div>
                            <div class="col-lg-4">

                                <label for="clientes_list">Estabelecimento:</label>
                                <select name="clientes_list" id="clientes-list" class="form-control"></select>
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
        <div class="form-group row">
            <div id="container-report" class="col-lg-12">
            </div>

        </div>
    </div>

</div>


<script src="/webroot/js/scripts/clientes/rel_ranking_operacoes<?= $debugExtension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/css/styles/clientes/rel_ranking_operacoes<?= $debugExtension ?>.css?version=<?= SYSTEM_VERSION ?>" />
