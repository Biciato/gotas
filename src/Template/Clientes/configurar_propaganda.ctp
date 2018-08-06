<?php

/**
 * @var \App\View\AppView $this
 */

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Escolher Unidade para Configurar Propagandas', array("controller" => "RedesHasClientes", "action" => "propaganda_escolha_unidades"));
$this->Breadcrumbs->add('Propaganda Para a Unidade', array(), array("class" => "active"));

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../RedesHasClientes/left_menu') ?>

<div class="redes form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($cliente, ['enctype' => 'multipart/form-data']) ?>
    <fieldset>
        <legend><?= __('Configurar Propaganda') ?></legend>
        <?= $this->element('../Clientes/propaganda_form') ?>
    </fieldset>

    <?= $this->Form->end() ?>
</div>
