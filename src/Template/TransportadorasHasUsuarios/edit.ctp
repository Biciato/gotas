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
                ['action' => 'delete', $transportadorasHasUsuario->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $transportadorasHasUsuario->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Transportadoras Has Usuarios'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Transportadoras'), ['controller' => 'Transportadoras', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Transportadora'), ['controller' => 'Transportadoras', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="transportadorasHasUsuarios form large-9 medium-8 columns content">
    <?= $this->Form->create($transportadorasHasUsuario) ?>
    <fieldset>
        <legend><?= __('Edit Transportadoras Has Usuario') ?></legend>
        <?php
            echo $this->Form->control('transportadoras_id', ['options' => $transportadoras]);
            echo $this->Form->control('usuarios_id', ['options' => $usuarios]);
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
