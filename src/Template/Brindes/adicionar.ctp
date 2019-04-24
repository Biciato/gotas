<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/adicionar_brinde_rede.ctp
 * @date     09/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$titleBrindesIndex = "";
$title = "";
if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) {

    $titleBrindesIndex = "Configurações de IHM";
    $title = "Cadastrar IHM";
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
    $this->Breadcrumbs->add('Detalhes da Rede', array('controller' => 'redes', 'action' => 'verDetalhes', $redesId));
    $this->Breadcrumbs->add('Detalhes da Unidade', array("controller" => "clientes", "action" => "verDetalhes", $clientesId), ['class' => 'active']);
    $this->Breadcrumbs->add($titleBrindesIndex, array("controller" => "brindes", "action" => "index", $clientesId), array());
} else {
    $titleBrindesIndex = "Configurações de Brindes";
    $title = "Adicionar IHM";

    $this->Breadcrumbs->add($titleBrindesIndex, array("controller" => "brindes", "action" => "index", $clientesId), array());
}

$this->Breadcrumbs->add($title, array(), array('class' => 'active'));

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>
<?= $this->element("../Brindes/left_menu", array("mode" => "add")) ?>
<div class="brindes form col-lg-9 col-md-10 columns content">
    <legend><?= $title ?></legend>
    <?= $this->Form->create($brinde) ?>
    <fieldset>
        <?= $this->element('../Brindes/brindes_form', ['brinde' => $brinde, 'clientesId' => $clientesId]); ?>
    </fieldset>

    <?= $this->Form->end() ?>
</div>
