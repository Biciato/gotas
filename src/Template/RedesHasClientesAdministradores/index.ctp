<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\RedesHasClientesAdministradore[]|\Cake\Collection\CollectionInterface $redesHasClientesAdministradores
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Redes Has Clientes Administradore'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="redesHasClientesAdministradores index large-9 medium-8 columns content">
    <h3><?= __('Redes Has Clientes Administradores') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('redes_has_clientes_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_insert') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_update') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($redesHasClientesAdministradores as $redesHasClientesAdministradore): ?>
            <tr>
                <td><?= $this->Number->format($redesHasClientesAdministradore->redes_has_clientes_id) ?></td>
                <td><?= $this->Number->format($redesHasClientesAdministradore->usuarios_id) ?></td>
                <td><?= h($redesHasClientesAdministradore->audit_insert) ?></td>
                <td><?= h($redesHasClientesAdministradore->audit_update) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $redesHasClientesAdministradore->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $redesHasClientesAdministradore->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $redesHasClientesAdministradore->id], ['confirm' => __('Are you sure you want to delete # {0}?', $redesHasClientesAdministradore->id)]) ?>
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
