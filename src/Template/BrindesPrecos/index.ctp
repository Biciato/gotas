<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\BrindesHabilitadosPreco[]|\Cake\Collection\CollectionInterface $brindes_habilitados_preco
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Brindes Habilitados Preco'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Brindes Habilitados'), ['controller' => 'BrindesHabilitados', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Brindes Habilitado'), ['controller' => 'BrindesHabilitados', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="brindesHabilitadosPreco index large-9 medium-8 columns content">
    <h3><?= __('Brindes Habilitados Preco') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('brindes_habilitados_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('preco') ?></th>
                <th scope="col"><?= $this->Paginator->sort('data_preco') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_insert') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_update') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes_has_brindes_habilitados_precos as $key => $clientes_has_brindes_habilitados_precos): ?>
            <tr>
                <td><?= $clientes_has_brindes_habilitados_precos->has('brindes_habilitado') ? $this->Html->link($clientes_has_brindes_habilitados_precos->brindes_habilitado->id, ['controller' => 'BrindesHabilitados', 'action' => 'view', $clientes_has_brindes_habilitados_precos->brindes_habilitado->id]) : '' ?></td>
                <td><?= $this->Number->format($clientes_has_brindes_habilitados_precos->preco) ?></td>
                <td><?= h($clientes_has_brindes_habilitados_precos->data_preco) ?></td>
                <td><?= h($clientes_has_brindes_habilitados_precos->audit_insert->format('d/m/Y H:i:s')) ?></td>
                <td><?= h(isset($clientes_has_brindes_habilitados_precos->audit_update) ? $clientes_has_brindes_habilitados_precos->audit_update->format('d/m/Y H:i:s') : null) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $clientes_has_brindes_habilitados_precos->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $clientes_has_brindes_habilitados_precos->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $clientes_has_brindes_habilitados_precos->id], ['confirm' => __('Are you sure you want to delete # {0}?', $clientes_has_brindes_habilitados_precos->id)]) ?>
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
