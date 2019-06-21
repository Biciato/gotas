<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Cupom $cupom
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Cupom'), ['action' => 'edit', $cupom->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Cupom'), ['action' => 'delete', $cupom->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cupom->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Cupons'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Cupom'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Brindes'), ['controller' => 'Brindes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Brinde'), ['controller' => 'Brindes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="cupons view large-9 medium-8 columns content">
    <h3><?= h($cupom->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Brinde') ?></th>
            <td><?= $cupom->has('brinde') ? $this->Html->link($cupom->brinde->id, ['controller' => 'Brindes', 'action' => 'view', $cupom->brinde->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Cliente') ?></th>
            <td><?= $cupom->has('cliente') ? $this->Html->link($cupom->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $cupom->cliente->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Usuario') ?></th>
            <td><?= $cupom->has('usuario') ? $this->Html->link($cupom->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $cupom->usuario->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Cupom Emitido') ?></th>
            <td><?= h($cupom->cupom_emitido) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($cupom->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tipo Banho') ?></th>
            <td><?= $this->Number->format($cupom->tipo_banho) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tempo Banho') ?></th>
            <td><?= $this->Number->format($cupom->tempo_banho) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Senha') ?></th>
            <td><?= $this->Number->format($cupom->senha) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Data') ?></th>
            <td><?= h($cupom->data) ?></td>
        </tr>
           <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($cupom->audit_insert->format('d/m/Y H:i:s')) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h($cupom->audit_update->format('d/m/Y H:i:s')) ?></td>
        </tr>
    </table>
</div>
