<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Cupom[]|\Cake\Collection\CollectionInterface $cupons
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Cupom'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Brindes'), ['controller' => 'Brindes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Brinde'), ['controller' => 'Brindes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="cupons index large-9 medium-8 columns content">
    <h3><?= __('Cupons') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('brindes_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('clientes_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('tipo_banho') ?></th>
                <th scope="col"><?= $this->Paginator->sort('tempo_banho') ?></th>
                <th scope="col"><?= $this->Paginator->sort('senha') ?></th>
                <th scope="col"><?= $this->Paginator->sort('cupom_emitido') ?></th>
                <th scope="col"><?= $this->Paginator->sort('data') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_insert') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_update') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cupons as $cupom): ?>
            <tr>
                <td><?= $cupom->has('brinde') ? $this->Html->link($cupom->brinde->id, ['controller' => 'Brindes', 'action' => 'view', $cupom->brinde->id]) : '' ?></td>
                <td><?= $cupom->has('cliente') ? $this->Html->link($cupom->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $cupom->cliente->id]) : '' ?></td>
                <td><?= $cupom->has('usuario') ? $this->Html->link($cupom->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $cupom->usuario->id]) : '' ?></td>
                <td><?= $this->Number->format($cupom->tipo_banho) ?></td>
                <td><?= $this->Number->format($cupom->tempo_banho) ?></td>
                <td><?= $this->Number->format($cupom->senha) ?></td>
                <td><?= h($cupom->cupom_emitido) ?></td>
                <td><?= h($cupom->data) ?></td>
                <td><?= h($cupom->audit_insert->format('d/m/Y H:i:s')) ?></td>
                <td><?= h(isset($cupom->audit_update) ? $cupom->audit_update->format('d/m/Y H:i:s') : null) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $cupom->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $cupom->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $cupom->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cupom->id)]) ?>
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
