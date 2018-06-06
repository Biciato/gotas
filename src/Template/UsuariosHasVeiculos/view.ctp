<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\UsuariosHasVeiculo $usuariosHasVeiculo
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Usuarios Has Veiculo'), ['action' => 'edit', $usuariosHasVeiculo->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Usuarios Has Veiculo'), ['action' => 'delete', $usuariosHasVeiculo->id], ['confirm' => __('Are you sure you want to delete # {0}?', $usuariosHasVeiculo->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios Has Veiculos'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuarios Has Veiculo'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Veiculos'), ['controller' => 'Veiculos', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Veiculo'), ['controller' => 'Veiculos', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="usuariosHasVeiculos view large-9 medium-8 columns content">
    <h3><?= h($usuariosHasVeiculo->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Usuario') ?></th>
            <td><?= $usuariosHasVeiculo->has('usuario') ? $this->Html->link($usuariosHasVeiculo->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $usuariosHasVeiculo->usuario->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Veiculo') ?></th>
            <td><?= $usuariosHasVeiculo->has('veiculo') ? $this->Html->link($usuariosHasVeiculo->veiculo->id, ['controller' => 'Veiculos', 'action' => 'view', $usuariosHasVeiculo->veiculo->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($usuariosHasVeiculo->id) ?></td>
        </tr>
    </table>
</div>
