<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

$title = "Top Brindes Nacional";

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

        <div id="items" class="box-container">
            <legend>Top Brindes Nacional Cadastrados</legend>
            <div id="box-parent" class="top-brindes-box-items-parent">
                <ul class="top-brindes-box-items" id="top-brindes-box-items" name="top-brindes-box-items">
                    <!-- <li class="item-box" name="item-box1" id="item-box1">1</li>
                    <li class="item-box" name="item-box2" id="item-box2">2</li>
                    <li class="item-box" name="item-box3" id="item-box3">3</li>
                    <li class="item-box" name="item-box4" id="item-box4">4</li> -->
                </ul>
            </div>
        </div>
    </div>

    <div id="form-vinculo" class="form-vinculo">
        <legend>Adicionar Brinde aos Top-Brindes Nacional</legend>
        <label for="postos_rede">Lista de Unidades:</label>
        <select name="postos_rede" id="postos-rede" class="form-control"></select>

        <div class="form-group">
            <div class="brindes-container">
                <h4>Brindes do Posto:</h4>
                <table class="table table-striped table-hover table-condensed table-responsive brindes-list" id="brindes-list">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Descrição</th>
                            <th>Definir</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>


<div id="modal-atribuir" class="modal fade" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atribuir Top Brinde</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Deseja atribuir o registro: <span id='nome-registro'></span> ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmar">Atribuir</button>
            </div>
        </div>
    </div>
</div>

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

<script src="/webroot/js/scripts/topBrindes/nacional<?= $extensionDebug ?>.js"></script>

<link rel="stylesheet" href="/webroot/css/styles/topBrindes/nacional<?= $extensionDebug ?>.css" />