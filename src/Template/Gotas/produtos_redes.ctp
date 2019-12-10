<?php

/**
 * src\Template\Gotas\produtos_redes.ctp
 *
 * Tela de Gestão de Produtos
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-12-09
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

        <li><a href="#"><i class="fas fa-plus-circle"></i> Novo</a></li>
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
                            <div class="col-lg-6">
                                <label for="redes_list">Rede:</label>
                                <select name="redes_list" id="redes-list" class="form-control"></select>
                            </div>
                            <div class="col-lg-6">
                                <label for="clientes_list">Cliente:</label>
                                <select name="clientes_list" id="clientes-list" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 text-right">
                                <div class="btn btn-primary" id="btn-search">
                                    <span class="fa fa-search"></span>
                                    Pesquisar
                                </div>
                                <div class="imprimir btn btn-default print-button-thermal" id="btn-clear">
                                    <i class="fas fa-eraser"></i>
                                    Limpar
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <table class="table table-striped table-hover" id="data-table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Gota</th>
                <th>Multiplicador</th>
                <th>Status</th>
                <th>Ações
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes">
                        <span class="fa fa-book"> Legendas</span>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <!-- <tr>
                <td class="text-warning text-center" colspan="6">A consulta não retornou dados!</td>
            </tr> -->
        </tbody>
    </table>
    <div id="create-edit-record">
        <legend>
            <span id="text-action-record"></span> <?= ' Produtos de Redes' ?>
        </legend>
        <fieldset>
            <div class="form-group row">
                <div class="col-lg-6">
                    <label for="redes_id">Rede</label>
                    <select name="redes_id" id="redes-id" class="form-control"></select>
                </div>
                <div class="col-lg-6">
                    <label for="clientes_id">Estabelecimento</label>
                    <select name="clientes_id" id="clientes-id" class="form-control"></select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-4">
                    <label for="nome_parametro">Nome do Parâmetro*</label>
                    <input value="" type="text" class="form-control" name="nome_parametro" placeholder="Nome do Parâmetro..." id="nome_parametro" required />
                </div>
                <div class="col-lg-4">
                    <label for="multiplicador_gota">Multiplicador de Gotas*</label>
                    <input value="" type="text" class="form-control" name="multiplicador_gota" id="multiplicador_gota" placeholder="Multiplicador de Gotas..." maxlength="7" required />
                    <!-- max="1000,00" -->
                </div>
                <div class="col-lg-4">
                    <label for=tipo_cadastro>Tipo de Cadastro</label>
                    <select name="tipo_cadastro" id="tipo-cadastro" readonly disabled class="form-control">
                        <option></option>
                        <option value="1"><?= GOTAS_REGISTER_TYPE_AUTOMATIC ?></option>
                        <option value="0"><?= GOTAS_REGISTER_TYPE_MANUAL ?></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-12 text-right">
                <button type="submit" class="btn btn-primary botao-confirmar">
                    <i class="fa fa-save"></i>
                    Salvar
                </button>

                <a href="/gotas/gotas-minha-rede/" class="btn btn-danger botao-cancelar">
                    <span class="fa fa-window-close"></span>
                    Cancelar
                </a>
            </div>
        </fieldset>

    </div>
</div>


<script src="/webroot/js/scripts/gotas/produtos_redes<?= $debugExtension ?>.js?<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/css/styles/gotas/produtos_redes<?= $debugExtension ?>.css?<?= SYSTEM_VERSION ?>" />
