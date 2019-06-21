<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\RedesHasCliente[]|\Cake\Collection\CollectionInterface $redesHasClientes
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Redes Has Cliente'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="redesHasClientes index large-9 medium-8 columns content">
    <h3><?= __('Redes Has Clientes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('redes_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('clientes_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('ativado') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_insert') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_update') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($redesHasClientes as $redesHasCliente): ?>
            <tr>
                <td><?= $this->Number->format($redesHasCliente->redes_id) ?></td>
                <td><?= $this->Number->format($redesHasCliente->clientes_id) ?></td>
                <td><?= h($redesHasCliente->ativado) ?></td>
                <td><?= h($redesHasCliente->audit_insert) ?></td>
                <td><?= h($redesHasCliente->audit_update) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $redesHasCliente->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $redesHasCliente->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $redesHasCliente->id], ['confirm' => __('Are you sure you want to delete # {0}?', $redesHasCliente->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <center>
            <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('primeiro')) ?>
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape'=>false]) ?>
                <?= $this->Paginator->numbers(['escape'=>false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape'=>false]) ?>
            <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
</div>
