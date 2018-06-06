<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\PontuacoesPendente[]|\Cake\Collection\CollectionInterface $pontuacoesPendentes
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Pontuacoes Pendente'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="pontuacoesPendentes index large-9 medium-8 columns content">
    <h3><?= __('Pontuacoes Pendentes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('clientes_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('funcionarios_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('data') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_insert') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_update') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pontuacoesPendentes as $pontuacoesPendente): ?>
            <tr>
                <td><?= $pontuacoesPendente->has('cliente') ? $this->Html->link($pontuacoesPendente->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $pontuacoesPendente->cliente->id]) : '' ?></td>
                <td><?= $this->Number->format($pontuacoesPendente->usuarios_id) ?></td>
                <td><?= $pontuacoesPendente->has('usuario') ? $this->Html->link($pontuacoesPendente->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $pontuacoesPendente->usuario->id]) : '' ?></td>
                <td><?= h($pontuacoesPendente->data) ?></td>
                <td><?= h($pontuacoesPendente->audit_insert->format('d/m/Y H:i:s')) ?></td>
                <td><?= h(isset($pontuacoesPendente->audit_update) ? $pontuacoesPendente->audit_update->format('d/m/Y H:i:s') : null) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $pontuacoesPendente->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $pontuacoesPendente->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $pontuacoesPendente->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pontuacoesPendente->id)]) ?>
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
