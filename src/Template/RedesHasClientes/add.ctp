<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Redes Has Clientes'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="redesHasClientes form large-9 medium-8 columns content">
    <?= $this->Form->create($redesHasCliente) ?>
    <fieldset>
        <legend><?= __('Add Redes Has Cliente') ?></legend>
        <?php
            echo $this->Form->control('redes_id');
            echo $this->Form->control('clientes_id');
            echo $this->Form->control('ativado');
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
