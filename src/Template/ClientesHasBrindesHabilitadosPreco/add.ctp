<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Brindes Habilitados Preco'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Brindes Habilitados'), ['controller' => 'BrindesHabilitados', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Brindes Habilitado'), ['controller' => 'BrindesHabilitados', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="brindesHabilitadosPreco form large-9 medium-8 columns content">
    <?= $this->Form->create($brindesHabilitadosPreco) ?>
    <fieldset>
        <legend><?= __('Add Brindes Habilitados Preco') ?></legend>
        <?php
            echo $this->Form->control('brindes_habilitados_id', ['options' => $brindesHabilitados]);
            echo $this->Form->control('preco');
            echo $this->Form->control('data_preco');
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
