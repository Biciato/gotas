<?php
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/editar_brinde_rede.ctp
 * @date     18/08/2017
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

<nav class="col-lg-3 col-md-2 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Menu') ?></a></li>
    </ul>
</nav>
<div class="brindes form col-lg-9 col-md-10 columns content">
    <legend><?= 'Editar Brinde' ?></legend>
    <?= $this->Form->create($brinde) ?>
    <fieldset>
        <?= $this->element('../Brindes/brindes_form', ['brinde' => $brinde, "imagemOriginal" => $brinde["nome_img_completo"]]); ?>
    </fieldset>

    <?= $this->Form->end() ?>
</div>
<?= $this->fetch('script') ?>
