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
                ['action' => 'delete', $pontuaco->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $pontuaco->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Pontuacoes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Brindes Habilitados'), ['controller' => 'BrindesHabilitados', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Brindes Habilitado'), ['controller' => 'BrindesHabilitados', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Gotas'), ['controller' => 'Gotas', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Gota'), ['controller' => 'Gotas', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="pontuacoes form large-9 medium-8 columns content">
    <?= $this->Form->create($pontuaco) ?>
    <fieldset>
        <legend><?= __('Edit Pontuaco') ?></legend>
        <?php
            echo $this->Form->control('usuarios_id', ['options' => $usuarios]);
            echo $this->Form->control('brindes_habilitados_id', ['options' => $brindesHabilitados, 'empty' => true]);
            echo $this->Form->control('gotas_id', ['options' => $gotas, 'empty' => true]);
            echo $this->Form->control('quantidade');
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
