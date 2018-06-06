<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\ClientesHasBrindesHabilitado $clientesHasBrindesHabilitado
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Clientes Has Brindes Habilitado'), ['action' => 'edit', $clientesHasBrindesHabilitado->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Clientes Has Brindes Habilitado'), ['action' => 'delete', $clientesHasBrindesHabilitado->id], ['confirm' => __('Are you sure you want to delete # {0}?', $clientesHasBrindesHabilitado->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Clientes Has Brindes Habilitados'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Clientes Has Brindes Habilitado'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Brindes Habilitados'), ['controller' => 'BrindesHabilitados', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Brindes Habilitado'), ['controller' => 'BrindesHabilitados', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="clientesHasBrindesHabilitados view large-9 medium-8 columns content">
    <h3><?= h($clientesHasBrindesHabilitado->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Cliente') ?></th>
            <td><?= $clientesHasBrindesHabilitado->has('cliente') ? $this->Html->link($clientesHasBrindesHabilitado->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $clientesHasBrindesHabilitado->cliente->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Brindes Habilitado') ?></th>
            <td><?= $clientesHasBrindesHabilitado->has('brindes_habilitado') ? $this->Html->link($clientesHasBrindesHabilitado->brindes_habilitado->id, ['controller' => 'BrindesHabilitados', 'action' => 'view', $clientesHasBrindesHabilitado->brindes_habilitado->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($clientesHasBrindesHabilitado->id) ?></td>
        </tr>
    </table>
</div>
