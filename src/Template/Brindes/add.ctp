<nav class="col-md-2 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Actions') ?></a></li>
        <li><?= $this->Html->link(__('List {0}', 'Brindes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List {0}', 'Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New {0}', 'Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="brindes form col-md-10 columns content">
    <?= $this->Form->create($brinde) ?>
    <fieldset>
        <legend><?= 'Add Brinde' ?></legend>
        <?php
            echo $this->Form->input('clientes_id', ['options' => $clientes]);
            echo $this->Form->input('nome');
            echo $this->Form->input('ilimitado');
            echo $this->Form->input('preco_padrao');
            echo $this->Form->input('audit_insert');
            echo $this->Form->input('audit_update');
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
