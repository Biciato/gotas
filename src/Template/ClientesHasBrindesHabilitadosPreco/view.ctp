<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\BrindesHabilitadosPreco $brindesHabilitadosPreco
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Brindes Habilitados Preco'), ['action' => 'edit', $brindesHabilitadosPreco->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Brindes Habilitados Preco'), ['action' => 'delete', $brindesHabilitadosPreco->id], ['confirm' => __('Are you sure you want to delete # {0}?', $brindesHabilitadosPreco->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Brindes Habilitados Preco'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Brindes Habilitados Preco'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Brindes Habilitados'), ['controller' => 'BrindesHabilitados', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Brindes Habilitado'), ['controller' => 'BrindesHabilitados', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="brindesHabilitadosPreco view large-9 medium-8 columns content">
    <h3><?= h($brindesHabilitadosPreco->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Brindes Habilitado') ?></th>
            <td><?= $brindesHabilitadosPreco->has('brindes_habilitado') ? $this->Html->link($brindesHabilitadosPreco->brindes_habilitado->id, ['controller' => 'BrindesHabilitados', 'action' => 'view', $brindesHabilitadosPreco->brindes_habilitado->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($brindesHabilitadosPreco->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Preco') ?></th>
            <td><?= $this->Number->format($brindesHabilitadosPreco->preco) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Data Preco') ?></th>
            <td><?= h($brindesHabilitadosPreco->data_preco) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($brindesHabilitadosPreco->audit_insert->format('d/m/Y H:i:s')) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h($brindesHabilitadosPreco->audit_update->format('d/m/Y H:i:s')) ?></td>
        </tr>
    </table>
</div>
