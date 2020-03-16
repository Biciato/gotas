<?php

/**
 * index.ctp
 *
 * View para redes_cpf_lista_negra/index
 *
 * @filesource src\Template\RedesCpfListaNegra\redes_cpf_lista_negra.ctp
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.1.8
 * @date 2020-03-11
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RedesCpfListaNegra[]|\Cake\Collection\CollectionInterface $redesCpfListaNegra
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$title = "Lista Negra de CPF";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ["class" => "active"]);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<nav class="col-lg-3 col-md-2 " id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
        <li>
            <a href="#" id="back-button"><span><i class="fas fa-arrow-alt-circle-left"></i> Voltar</span></a>
        </li>
        <li>
            <a href="#" id="new-button"><span><i class="fas fa-plus-circle"></i> Novo</span></a>
        </li>
    </ul>
</nav>
<div class="clientes index col-lg-9 col-md-10 columns content container-fluid">
    <div id="dados">

        <legend><?= $title ?></legend>

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

                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label for="redes_list">Rede:</label>
                                    <select name="redes_list" id="redes-list" class="form-control"></select>
                                </div>
                                <div class="col-lg-6">
                                    <label for="cpf_form_search">CPF:</label>
                                    <input type="text" name="cpf_form_search" id="cpf-form-search" class="form-control"></select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12 text-right">
                                    <div class="btn btn-primary" id="btn-pesquisar">
                                        <span class="fa fa-search"></span>
                                        Pesquisar
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h4>CPF's Bloqueados:</h4>
        <table class="table table-striped table-hover table-condensed table-responsive brindes-list" id="data-table">
            <thead>
                <!-- <tr>
                    <th></th>
                    <th>CPF</th>
                    <th>Ações</th>
                </tr> -->
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>


    <div id="region-add" class="region-add">
        <legend>Adicionar CPF</legend>
        <div class="form-group row">
            <div class="col-lg-12">
                <label for="cpf_save">CPF:</label>
                <input type="text" class="form-control" name="cpf_save" id="cpf-save" title="CPF à ser adicionado" placeholder="CPF à ser adicionado..." />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 text-right">
                <div class="btn btn-primary" id="btn-save">
                    <span class="fas fa-save"></span> Salvar
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modais -->

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
                <button type="button" class="btn btn-primary" id="confirmar">Remover</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<?php

$extensionDebug = Configure::read("debug") ? '' : '.min';

?>

<script src="/webroot/js/scripts/redes_cpf_lista_negra/index<?= $extensionDebug ?>.js"></script>

<link rel="stylesheet" href="/webroot/css/styles/redes_cpf_lista_negra/index<?= $extensionDebug ?>.css" />
