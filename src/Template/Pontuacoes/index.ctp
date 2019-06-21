<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Pontuaco[]|\Cake\Collection\CollectionInterface $pontuacoes
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Pontuaco'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Brindes Habilitados'), ['controller' => 'BrindesHabilitados', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Brindes Habilitado'), ['controller' => 'BrindesHabilitados', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Gotas'), ['controller' => 'Gotas', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Gota'), ['controller' => 'Gotas', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="pontuacoes index large-9 medium-8 columns content">
    <h3><?= __('Pontuacoes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('brindes_habilitados_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('gotas_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantidade') ?></th>
                <th scope="col"><?= $this->Paginator->sort('data') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_insert') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_update') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pontuacoes as $pontuaco): ?>
            <tr>
                <td><?= $pontuaco->has('usuario') ? $this->Html->link($pontuaco->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $pontuaco->usuario->id]) : '' ?></td>
                <td><?= $pontuaco->has('brindes_habilitado') ? $this->Html->link($pontuaco->brindes_habilitado->id, ['controller' => 'BrindesHabilitados', 'action' => 'view', $pontuaco->brindes_habilitado->id]) : '' ?></td>
                <td><?= $pontuaco->has('gota') ? $this->Html->link($pontuaco->gota->id, ['controller' => 'Gotas', 'action' => 'view', $pontuaco->gota->id]) : '' ?></td>
                <td><?= $this->Number->format($pontuaco->quantidade) ?></td>
                <td><?= h($pontuaco->data) ?></td>
                <td><?= h($pontuaco->audit_insert->format('d/m/Y H:i:s')) ?></td>
                <td><?= h(isset($$pontuaco->audit_update) ? $pontuaco->audit_update->format('d/m/Y H:i:s') : null) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $pontuaco->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $pontuaco->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $pontuaco->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pontuaco->id)]) ?>
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
