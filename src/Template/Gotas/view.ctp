<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Gota $gota
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Gota'), ['action' => 'edit', $gota->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Gota'), ['action' => 'delete', $gota->id], ['confirm' => __('Are you sure you want to delete # {0}?', $gota->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Gotas'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Gota'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="gotas view large-9 medium-8 columns content">
    <h3><?= h($gota->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Cliente') ?></th>
            <td><?= $gota->has('cliente') ? $this->Html->link($gota->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $gota->cliente->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($gota->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Multiplicador Gota') ?></th>
            <td><?= $this->Number->format($gota->multiplicador_gota) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($gota->audit_insert->format('d/m/Y H:i:s')) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h($gota->audit_update->format('d/m/Y H:i:s')) ?></td>
        </tr>
    </table>
</div>
