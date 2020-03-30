<?php

/**
 * src\Template\Clientes\rel_cliente_final.ctp
 *
 * Tela de Relatório de cliente final
 *
 * @author Vinícius Carvalho de Abreu <vinicius@aigen.com.br>
 * @since 1.1.6
 * @date 2020-03-21
 */

use Cake\Core\Configure;

$debug = Configure::read("debug");
// $debugExtension = $debug ? ".min" : "";
$debugExtension = $debug ? "" : "";

$title = "Relatório de cliente final";
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
                            <div class="col-lg-2">
                                <label for="usuario-cpf">Pesquisar Por:</label>
                                <select id="usuario-options-search" name="usuario-options-search" class="form-control" autofocus>
                                    <option value="nome">Nome</option>
                                    <option value="cpf">CPF</option>
                                    <option value="telefone" selected>Telefone</option>
                                    <option value="placa">Placa</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="usuario-cpf">Dados de Pesquisa do Usuário:</label>
                                <input type="text" name="usuario-parameter-search" id="usuario-parameter-search" class="form-control" placeholder="" title="">
                            </div>
                            
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                            <div class="btn btn-primary" id="btn-buscar-usuarios">
                                    <span class="fa fa-search"></span>
                                    Buscar usuários
                                </div>
                            </div>
                        </div>
                        <div id="veiculo-region" style="display:none">
                            <h4>Dados do Veículo:</h4>

                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <label for="veiculo-placa">Placa:</label>
                                    <input type="text" name="veiculo-placa" id="veiculo-placa" class="form-control" disabled placeholder="Placa do Veículo..." title="Placa do Veículo">
                                </div>
                                <div class="col-lg-3">
                                    <label for="veiculo-modelo">Modelo:</label>
                                    <input type="text" name="veiculo-modelo" id="veiculo-modelo" class="form-control" disabled placeholder="Modelo do Veículo..." title="Modelo do Veículo">

                                </div>
                                <div class="col-lg-3">
                                    <label for="veiculo-fabricante">Fabricante:</label>
                                    <input type="text" name="veiculo-fabricante" id="veiculo-fabricante" class="form-control" disabled placeholder="Fabricante do Veículo..." title="Fabricante do Veículo">

                                </div>
                                <div class="col-lg-3">
                                    <label for="veiculo-ano">Ano:</label>
                                    <input type="text" name="veiculo-ano" id="veiculo-ano" class="form-control" disabled placeholder="Ano do Veículo..." title="Ano do Veículo">

                                </div>
                            </div>
                        </div>

                        <div id="usuarios-region" style="display:none">
                            <h4>Usuários Encontrados:</h4>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered table-hover table-striped table-condensed" id='usuarios-table'>
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Nome</th>
                                                <th>Telefone</th>
                                                <th>Data Nasc.</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label for="usuario-selecionado">Usuário selecionado</label>
                                <input type="text" class="form-control" name="usuario-selecionado" id="usuario-selecionado" disabled="disabled">
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


<script src="/app_gotas/js/scripts/clientes/rel_cliente_final<?= $debugExtension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
