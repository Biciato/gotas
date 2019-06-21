<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Pontuaco $pontuaco
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Pontuaco'), ['action' => 'edit', $pontuaco->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Pontuaco'), ['action' => 'delete', $pontuaco->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pontuaco->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Pontuacoes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pontuaco'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Brindes Habilitados'), ['controller' => 'BrindesHabilitados', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Brindes Habilitado'), ['controller' => 'BrindesHabilitados', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Gotas'), ['controller' => 'Gotas', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Gota'), ['controller' => 'Gotas', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="pontuacoes view large-9 medium-8 columns content">
    <h3><?= h($pontuaco->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Usuario') ?></th>
            <td><?= $pontuaco->has('usuario') ? $this->Html->link($pontuaco->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $pontuaco->usuario->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Brindes Habilitado') ?></th>
            <td><?= $pontuaco->has('brindes_habilitado') ? $this->Html->link($pontuaco->brindes_habilitado->id, ['controller' => 'BrindesHabilitados', 'action' => 'view', $pontuaco->brindes_habilitado->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Gota') ?></th>
            <td><?= $pontuaco->has('gota') ? $this->Html->link($pontuaco->gota->id, ['controller' => 'Gotas', 'action' => 'view', $pontuaco->gota->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($pontuaco->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Quantidade') ?></th>
            <td><?= $this->Number->format($pontuaco->quantidade) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Data') ?></th>
            <td><?= h($pontuaco->data) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($pontuaco->audit_insert->format('d/m/Y H:i:s')) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h(isset($pontuaco->audit_update) ? $pontuaco->audit_update->format('d/m/Y H:i:s') : null) ?></td>
        </tr>
    </table>
</div>
