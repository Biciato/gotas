<nav class="col-md-2 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Actions') ?></a></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $brinde->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $brinde->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List {0}', 'Brindes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List {0}', 'Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New {0}', 'Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="brindes form col-md-10 columns content">
    <?= $this->Form->create($brinde) ?>
    <fieldset>
        <legend><?= 'Edit Brinde' ?></legend>
        <?= $this->element('../brindes/brindes_form', ['brinde' => $brinde]); ?>
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
