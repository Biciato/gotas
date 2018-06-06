<nav class="col-md-2 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Actions') ?></a></li>
        <li><?= $this->Html->link(__('List {0}', 'Brindes Estoque'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List {0}', 'Brindes'), ['controller' => 'Brindes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New {0}', 'Brinde'), ['controller' => 'Brindes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List {0}', 'Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New {0}', 'Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="brindesEstoque form col-md-10 columns content">
    <?= $this->Form->create($brindesEstoque) ?>
    <fieldset>
        <legend><?= 'Add Brindes Estoque' ?></legend>
        <?php
            echo $this->Form->input('brindes_id', ['options' => $brindes]);
            echo $this->Form->input('usuarios_id', ['options' => $usuarios, 'empty' => true]);
            echo $this->Form->input('quantidade');
            echo $this->Form->input('tipo_operacao');
            echo $this->Form->input('data');
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
