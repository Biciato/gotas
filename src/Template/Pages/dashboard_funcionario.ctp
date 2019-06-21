<?php
//echo $this->fetch('content') ;
use Cake\Core\Configure;
?>

<header>
    <div class="header-title">
        <h1>Bem vindo ao sistema GOTAS</h1>
    </div>
</header>

<div>
    <div class="container-content-funcionario columns col-lg-12">

        <?= $this->element('../Pages/left_menu') ?>

    </div>

<?= $this->Form->create($funcionario) ?>
<?= $this->Form->input('id', ['type' => 'hidden', 'id' => 'funcionarios_id']) ?>
<?= $this->Form->input('estado_funcionario', ['type' => 'hidden', 'id' => 'estado_funcionario', 'value' => $estado_funcionario]) ?>

<?= $this->Form->end() ?>


</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/pages/dashboard_funcionario') ?>
    <?= $this->Html->css('styles/pages/dashboard_funcionario') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/pages/dashboard_funcionario.min') ?>
    <?= $this->Html->css('styles/pages/dashboard_funcionario.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
