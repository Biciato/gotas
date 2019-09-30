<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\Usuarios
 * @filename rtibrindes/src/Template/Usuarios/relatorio_usuarios_cadastrados.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     17/03/2018
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$debugExtension = Configure::read("debug") ? ".min" : "";
$title = __("Relatório de Usuários Cadastrados por Funcionarios");

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);
?>

<nav class="col-lg-3 col-md-2 " id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
    </ul>
</nav>

<div class="col-lg-9 col-md-8 ">
    <legend><?= __($title) ?> </legend>

    <!-- Filtro -->
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
                            <div class="col-lg-6">

                                <label for="funcionario">Estabelecimento:</label>
                                <select name="clientes-list" id="clientes-list" class="form-control"></select>
                            </div>
                            <div class="col-lg-6">
                                <label for="brinde">Funcionários:</label>
                                <select name="funcionarios-list" id="funcionarios-list" class="form-control"></select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label for="data-inicio">Data Início:</label>
                                <input type="text" class="form-control datepicker-input" format="d/m/Y" name="data-inicio" id="data-inicio" placeholder="Data Início...">
                            </div>
                            <div class="col-lg-4">
                                <label for="data-fim">Data Fim:</label>
                                <input type="text" class="form-control datepicker-input" format="d/m/Y" name="data-fim" id="data-fim" placeholder="Data Início...">
                            </div>

                            <div class="col-lg-4">
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

    <!-- Botões de Exportar e Imprimir -->

    <!-- Tabela de dados -->
    <table class="table table-responsive table-bordered table-hover table-condensed" id="tabela-dados">
        <tbody>
            <span></span>
        </tbody>

    </table>

</div>


<!-- Libs -->
<script src="/webroot/js/scripts/usuarios/relatorio_usuarios_cadastrados_funcionarios<?php $debugExtension?>.js"></script>
