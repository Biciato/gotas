<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\TransportadorasHasUsuario[]|\Cake\Collection\CollectionInterface $transportadorasHasUsuarios
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Transportadoras Has Usuario'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Transportadoras'), ['controller' => 'Transportadoras', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Transportadora'), ['controller' => 'Transportadoras', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="transportadorasHasUsuarios index large-9 medium-8 columns content">
    <h3><?= __('Transportadoras Has Usuarios') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('transportadoras_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transportadorasHasUsuarios as $transportadorasHasUsuario): ?>
            <tr>
                <td><?= $transportadorasHasUsuario->has('transportadora') ? $this->Html->link($transportadorasHasUsuario->transportadora->id, ['controller' => 'Transportadoras', 'action' => 'view', $transportadorasHasUsuario->transportadora->id]) : '' ?></td>
                <td><?= $transportadorasHasUsuario->has('usuario') ? $this->Html->link($transportadorasHasUsuario->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $transportadorasHasUsuario->usuario->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $transportadorasHasUsuario->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $transportadorasHasUsuario->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $transportadorasHasUsuario->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transportadorasHasUsuario->id)]) ?>
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
