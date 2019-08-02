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
            <div id="box-parent" class="box-items-parent">
                <ul class="box-items" id="box-items" name="box-items">
                    <!-- <div class="parent-item-box" id="parent-item-box1"></div>
                    <div class="parent-item-box" id="parent-item-box2"></div>
                    <div class="parent-item-box" id="parent-item-box3"></div>
                    <div class="parent-item-box" id="parent-item-box4"></div> -->
                    <li class="item-box" name="item-box1" id="item-box1">1</li>
                    <li class="item-box" name="item-box2" id="item-box2">2</li>
                    <li class="item-box" name="item-box3" id="item-box3">3</li>
                    <li class="item-box" name="item-box4" id="item-box4">4</li>
                    <!-- <li>1</li>
                    <li>2</li>
                    <li>3</li>
                    <li>4</li>
                    <li>5</li>
                    <li>6</li>
                    <li>7</li>
                    <li>8</li>
                    <li>9</li> -->
                </ul>
            </div>
        </div>
    </div>

</div>

<form id="formVinculo">

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

<script src="/webroot/js/scripts/topBrindes/nacional<?= $extensionDebug ?>.js"></script>

<link rel="stylesheet" href="/webroot/css/styles/topBrindes/nacional<?= $extensionDebug ?>.css" />