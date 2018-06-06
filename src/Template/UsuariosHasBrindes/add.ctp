<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Usuarios Has Brindes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Clientes Has Brindes Habilitados'), ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Clientes Has Brindes Habilitado'), ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="usuariosHasBrindes form large-9 medium-8 columns content">
    <?= $this->Form->create($usuariosHasBrinde) ?>
    <fieldset>
        <legend><?= __('Add Usuarios Has Brinde') ?></legend>
        <?php
            echo $this->Form->control('usuarios_id', ['options' => $usuarios]);
            echo $this->Form->control('brindes_habilitados_id', ['options' => $clientesHasBrindesHabilitados]);
            echo $this->Form->control('preco');
            echo $this->Form->control('audit_insert', ['empty' => true]);
            echo $this->Form->control('audit_update', ['empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Salvar')) ?>
    <?= $this->Form->end() ?>
</div>
