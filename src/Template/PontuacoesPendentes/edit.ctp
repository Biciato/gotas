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
                ['action' => 'delete', $pontuacoesPendente->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $pontuacoesPendente->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Pontuacoes Pendentes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="pontuacoesPendentes form large-9 medium-8 columns content">
    <?= $this->Form->create($pontuacoesPendente) ?>
    <fieldset>
        <legend><?= __('Edit Pontuacoes Pendente') ?></legend>
        <?php
            echo $this->Form->control('clientes_id', ['options' => $clientes]);
            echo $this->Form->control('usuarios_id');
            echo $this->Form->control('funcionarios_id', ['options' => $usuarios]);
            echo $this->Form->control('conteudo');
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
