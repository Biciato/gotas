<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Clientes Has Brindes Habilitados'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Brindes Habilitados'), ['controller' => 'BrindesHabilitados', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Brindes Habilitado'), ['controller' => 'BrindesHabilitados', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="clientesHasBrindesHabilitados form large-9 medium-8 columns content">
    <?= $this->Form->create($clientesHasBrindesHabilitado) ?>
    <fieldset>
        <legend><?= __('Add Clientes Has Brindes Habilitado') ?></legend>
        <?php
            echo $this->Form->control('clientes_id', ['options' => $clientes]);
            echo $this->Form->control('brindes_habilitados_id', ['options' => $brindesHabilitados]);
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
