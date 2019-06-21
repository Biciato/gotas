<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $usuariosHasVeiculo->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $usuariosHasVeiculo->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Usuarios Has Veiculos'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Veiculos'), ['controller' => 'Veiculos', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Veiculo'), ['controller' => 'Veiculos', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="usuariosHasVeiculos form large-9 medium-8 columns content">
    <?= $this->Form->create($usuariosHasVeiculo) ?>
    <fieldset>
        <legend><?= __('Edit Usuarios Has Veiculo') ?></legend>
        <?php
            echo $this->Form->control('usuarios_id', ['options' => $usuarios]);
            echo $this->Form->control('veiculos_id', ['options' => $veiculos]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Salvar')) ?>
    <?= $this->Form->end() ?>
</div>
