<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\UsuariosHasBrinde $usuariosHasBrinde
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Usuarios Has Brinde'), ['action' => 'edit', $usuariosHasBrinde->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Usuarios Has Brinde'), ['action' => 'delete', $usuariosHasBrinde->id], ['confirm' => __('Are you sure you want to delete # {0}?', $usuariosHasBrinde->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios Has Brindes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuarios Has Brinde'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Clientes Has Brindes Habilitados'), ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Clientes Has Brindes Habilitado'), ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="usuariosHasBrindes view large-9 medium-8 columns content">
    <h3><?= h($usuariosHasBrinde->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Usuario') ?></th>
            <td><?= $usuariosHasBrinde->has('usuario') ? $this->Html->link($usuariosHasBrinde->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $usuariosHasBrinde->usuario->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Clientes Has Brindes Habilitado') ?></th>
            <td><?= $usuariosHasBrinde->has('clientes_has_brindes_habilitado') ? $this->Html->link($usuariosHasBrinde->clientes_has_brindes_habilitado->id, ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'view', $usuariosHasBrinde->clientes_has_brindes_habilitado->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($usuariosHasBrinde->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Preco') ?></th>
            <td><?= $this->Number->format($usuariosHasBrinde->preco) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($usuariosHasBrinde->audit_insert->format('d/m/Y H:i:s')) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h(isset($usuariosHasBrinde->audit_update) ? $usuariosHasBrinde->audit_update->format('d/m/Y H:i:s') : null) ?></td>
        </tr>
    </table>
</div>
