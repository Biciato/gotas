<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Veiculo $veiculo
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Veiculo'), ['action' => 'edit', $veiculo->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Veiculo'), ['action' => 'delete', $veiculo->id], ['confirm' => __('Are you sure you want to delete # {0}?', $veiculo->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Veiculos'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Veiculo'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="veiculos view large-9 medium-8 columns content">
    <h3><?= h($veiculo->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Placa') ?></th>
            <td><?= h($veiculo->placa) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modelo') ?></th>
            <td><?= h($veiculo->modelo) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Fabricante') ?></th>
            <td><?= h($veiculo->fabricante) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($veiculo->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Ano') ?></th>
            <td><?= $this->Number->format($veiculo->ano) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($veiculo->audit_insert->format('d/m/Y H:i:s')) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h(isset($veiculo->audit_update) ? $veiculo->audit_update->format('d/m/Y H:i:s') : null) ?></td>
        </tr>
    </table>
</div>
