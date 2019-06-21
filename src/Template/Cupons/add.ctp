<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Cupons'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Brindes'), ['controller' => 'Brindes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Brinde'), ['controller' => 'Brindes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="cupons form large-9 medium-8 columns content">
    <?= $this->Form->create($cupom) ?>
    <fieldset>
        <legend><?= __('Add Cupom') ?></legend>
        <?php
            echo $this->Form->control('brindes_id', ['options' => $brindes]);
            echo $this->Form->control('clientes_id', ['options' => $clientes]);
            echo $this->Form->control('usuarios_id', ['options' => $usuarios]);
            echo $this->Form->control('tipo_banho');
            echo $this->Form->control('tempo_banho');
            echo $this->Form->control('senha');
            echo $this->Form->control('cupom_emitido');
            echo $this->Form->control('data');
            echo $this->Form->control('audit_insert', ['empty' => true]);
            echo $this->Form->control('audit_update', ['empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('{0} Salvar',
        $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
        [
            'class' => 'btn btn-primary',
            'escape' => false
        ]
    
    ) ?>
    <?= $this->Form->end() ?>
</div>
