<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\ClientesHasUsuario $clientesHasUsuario
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Clientes Has Usuario'), ['action' => 'edit', $clientesHasUsuario->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Clientes Has Usuario'), ['action' => 'delete', $clientesHasUsuario->id], ['confirm' => __('Are you sure you want to delete # {0}?', $clientesHasUsuario->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Clientes Has Usuarios'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Clientes Has Usuario'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="clientesHasUsuarios view large-9 medium-8 columns content">
    <h3><?= h($clientesHasUsuario->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Cliente') ?></th>
            <td><?= $clientesHasUsuario->has('cliente') ? $this->Html->link($clientesHasUsuario->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $clientesHasUsuario->cliente->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Usuario') ?></th>
            <td><?= $clientesHasUsuario->has('usuario') ? $this->Html->link($clientesHasUsuario->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $clientesHasUsuario->usuario->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($clientesHasUsuario->id) ?></td>
        </tr>
    </table>
</div>
