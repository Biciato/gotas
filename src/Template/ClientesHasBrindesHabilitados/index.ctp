<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\ClientesHasBrindesHabilitado[]|\Cake\Collection\CollectionInterface $clientesHasBrindesHabilitados
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Clientes Has Brindes Habilitado'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Brindes Habilitados'), ['controller' => 'BrindesHabilitados', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Brindes Habilitado'), ['controller' => 'BrindesHabilitados', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="clientesHasBrindesHabilitados index large-9 medium-8 columns content">
    <h3><?= __('Clientes Has Brindes Habilitados') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('clientes_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('brindes_habilitados_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientesHasBrindesHabilitados as $clientesHasBrindesHabilitado): ?>
            <tr>
                <td><?= $clientesHasBrindesHabilitado->has('cliente') ? $this->Html->link($clientesHasBrindesHabilitado->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $clientesHasBrindesHabilitado->cliente->id]) : '' ?></td>
                <td><?= $clientesHasBrindesHabilitado->has('brindes_habilitado') ? $this->Html->link($clientesHasBrindesHabilitado->brindes_habilitado->id, ['controller' => 'BrindesHabilitados', 'action' => 'view', $clientesHasBrindesHabilitado->brindes_habilitado->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $clientesHasBrindesHabilitado->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $clientesHasBrindesHabilitado->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $clientesHasBrindesHabilitado->id], ['confirm' => __('Are you sure you want to delete # {0}?', $clientesHasBrindesHabilitado->id)]) ?>
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
