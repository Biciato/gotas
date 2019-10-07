<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

$title = "Importação de Gotas da SEFAZ em massa";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ["class" => "active"]);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
    </ul>
</nav>
<div class="gotas index col-lg-9 col-md-10 columns content">
    <div id="dados">

        <legend><?= $title ?></legend>

        <div id="pesquisa-qrcode">
            <div class="form-group row">
                <div class="col-lg-12">
                    <label for="pesquisa-nome">Informe QR Code para Importação em Massa</label>
                    <input type="text" name="qr_code" id="qr-code" class="form-control" placeholder="Informe QR Code..." title="Informe QR Code" autofocus>
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

        <div id="processamento">

            <div class="form-group row">

                <div class="col-lg-6">
                    <label for="rede">Rede Encontrada</label>
                    <input type="text" id="nome-rede" name="nome_rede" class="form-control" readonly disabled>
                </div>

                <div class="col-lg-6">
                    <label for="rede">Estabelecimento Encontrado</label>
                    <input type="text" id="nome-rede" name="nome_rede" class="form-control" readonly disabled>
                </div>
            </div>

            <h4>Gotas Encontradas na NF</h4>
            <table class="table table-striped table-hover" id="tabela-dados">
                <thead>
                    <tr>
                        <th><?= 'Nome' ?></th>
                        <th><?= 'Quantidade Multiplicador' ?></th>
                        <th><?= 'Importar?' ?></th>
                        <th><?= 'Ações' ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="form-group row ">
                <div class="col-lg-2 pull-right">
                    <button type="submit" class="btn btn-primary btn-block" id="botao-gravar-gotas">
                        <span class="fas fa-save"></span>
                        Gravar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="form-edicao">
        <legend id='titulo'>Edição de Gota à ser Importada</legend>

        <div class="form-group row">
            <input type="hidden" id="id" name="id" />
            <div class="col-lg-6">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" placeholder="Nome..." title="Nome" id="nome" name="nome" required readonly disabled />
            </div>
            <div class="col-lg-6">
                <label for="nome">Multiplicador</label>
                <input type="text" class="form-control" placeholder="Multiplicador..." title="Multiplicador" id="quantidade-multiplicador" name="quantidade_multiplicador" required />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 text-right">
                <button type="button" class="btn btn-primary" id="botao-confirmar">
                    <span class="fa fa-save"></span>
                    Salvar
                </button>
                <div class="btn btn-danger" id="botao-cancelar">
                    <span class="fa fa-window-close"></span>
                    Cancelar
                </div>
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



<?php

$extensionDebug = Configure::read("debug") ? '' : '.min';

?>

<script src="/webroot/js/scripts/gotas/importacao_gotas_sefaz<?= $extensionDebug ?>.js"></script>

<link rel="stylesheet" href="/webroot/css/styles/gotas/importacao_gotas_sefaz<?= $extensionDebug ?>.css" />
