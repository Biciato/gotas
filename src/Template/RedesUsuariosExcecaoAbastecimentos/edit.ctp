<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RedesUsuariosExcecaoAbastecimento $redesUsuariosExcecaoAbastecimento
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $redesUsuariosExcecaoAbastecimento->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $redesUsuariosExcecaoAbastecimento->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Redes Usuarios Excecao Abastecimentos'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Redes'), ['controller' => 'Redes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Rede'), ['controller' => 'Redes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="redesUsuariosExcecaoAbastecimentos form large-9 medium-8 columns content">
    <?= $this->Form->create($redesUsuariosExcecaoAbastecimento) ?>
    <fieldset>
        <legend><?= __('Edit Redes Usuarios Excecao Abastecimento') ?></legend>
        <?php
            echo $this->Form->control('redes_id', ['options' => $redes]);
            echo $this->Form->control('adm_rede_id');
            echo $this->Form->control('usuarios_id', ['options' => $usuarios]);
            echo $this->Form->control('quantidade_dia');
            echo $this->Form->control('validade', ['empty' => true]);
            echo $this->Form->control('habilitado');
            echo $this->Form->control('audit_insert', ['empty' => true]);
            echo $this->Form->control('audit_update', ['empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
