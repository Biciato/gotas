<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\TransportadorasHasUsuario $transportadorasHasUsuario
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Transportadoras Has Usuario'), ['action' => 'edit', $transportadorasHasUsuario->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Transportadoras Has Usuario'), ['action' => 'delete', $transportadorasHasUsuario->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transportadorasHasUsuario->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Transportadoras Has Usuarios'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Transportadoras Has Usuario'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Transportadoras'), ['controller' => 'Transportadoras', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Transportadora'), ['controller' => 'Transportadoras', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="transportadorasHasUsuarios view large-9 medium-8 columns content">
    <h3><?= h($transportadorasHasUsuario->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Transportadora') ?></th>
            <td><?= $transportadorasHasUsuario->has('transportadora') ? $this->Html->link($transportadorasHasUsuario->transportadora->id, ['controller' => 'Transportadoras', 'action' => 'view', $transportadorasHasUsuario->transportadora->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Usuario') ?></th>
            <td><?= $transportadorasHasUsuario->has('usuario') ? $this->Html->link($transportadorasHasUsuario->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $transportadorasHasUsuario->usuario->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($transportadorasHasUsuario->id) ?></td>
        </tr>
    </table>
</div>
