<?php

/**
 * @var \App\View\AppView $this
 */

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Redes', ['controler' => 'redes', "action" => "index"], ['class' => 'active']);

$this->Breadcrumbs->add('Adicionar Rede', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);
?>
<?= $this->element('../Redes/left_menu', ['go_back_url' => ['controller' => 'redes', 'action' => 'index']]) ?>
<div class="redes form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($rede, ['enctype' => 'multipart/form-data']) ?>
    <fieldset>
        <legend><?= __('Adicionar Rede') ?></legend>
        <?= $this->element('../Redes/redes_form') ?>
    </fieldset>

    <?= $this->Form->end() ?>
</div>
