<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\RedesHasCliente $redesHasCliente
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Redes Has Cliente'), ['action' => 'edit', $redesHasCliente->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Redes Has Cliente'), ['action' => 'delete', $redesHasCliente->id], ['confirm' => __('Are you sure you want to delete # {0}?', $redesHasCliente->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Redes Has Clientes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Redes Has Cliente'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="redesHasClientes view large-9 medium-8 columns content">
    <h3><?= h($redesHasCliente->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($redesHasCliente->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Redes Id') ?></th>
            <td><?= $this->Number->format($redesHasCliente->redes_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Clientes Id') ?></th>
            <td><?= $this->Number->format($redesHasCliente->clientes_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($redesHasCliente->audit_insert) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h($redesHasCliente->audit_update) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Ativado') ?></th>
            <td><?= $redesHasCliente->ativado ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
