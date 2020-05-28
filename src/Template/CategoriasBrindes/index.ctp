<?php

use Cake\Core\Configure;

$debugExtension = Configure::read("debug") ? '' : '.min';

$title = "Categorias de Brindes";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ["class" => "active"]);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
        <li>
            <a href="#" id="novo"><span>Novo</span></a>
        </li>
    </ul>
</nav>
<div class="clientes index col-lg-9 col-md-10 columns content">
    <div id="dados">

        <legend><?= $title ?></legend>

        <div id="filtro">
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
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <label for="pesquisa-nome">Nome</label>
                                    <input type="text" name="pesquisa-nome" id="pesquisa-nome" class="form-control" placeholder="Nome..." title="Nome" autofocus>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <div class="col-lg-2 pull-right">
                                    <button type="submit" class="btn btn-primary btn-block botao-pesquisar">
                                        <span class="fa fa-search"></span>
                                        Pesquisar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-striped table-hover" id="tabela-dados">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('nome', ['label' => 'Nome']) ?></th>
                    <th><?= $this->Paginator->sort('habilitado', ['label' => 'Habilitado']) ?></th>
                    <th><?= $this->Paginator->sort('data', ['label' => 'Data Criação']) ?></th>
                    <th class="actions">
                        <?= __('Ações') ?>
                        <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes"><span class=" fa fa-book"> Legendas</span></div>
                    </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <form id="formCadastro">
        <legend id='titulo'></legend>

        <div class="form-group row">
            <input type="hidden" id="id" name="id" />
            <div class="col-lg-12">
                <label for="nome">Nome*</label>
                <input type="text" class="form-control" placeholder="Nome..." title="Nome" id="nome" name="nome" required />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 text-right">
                <button type="submit" class="btn btn-primary  botao-confirmar"><span class="fa fa-save"></span> Salvar</button>
                <a href="" class="btn btn-danger botao-cancelar">
                    <span class="fa fa-window-close"></span>
                    Cancelar
                </a>
            </div>
        </div>
    </form>

    <div id="modal-remover" class="modal fade" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remover Registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Deseja remover o registro: <span id='nome-registro'></span> ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmar">Remover</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="/webroot/js/scripts/categoriasBrindes/index<?= $debugExtension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/css/styles/categoriasBrindes/index<?= $debugExtension ?>.css?version=<?= SYSTEM_VERSION ?>">
