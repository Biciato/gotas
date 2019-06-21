<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\UsuariosHasBrinde[]|\Cake\Collection\CollectionInterface $usuariosHasBrindes
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Usuarios Has Brinde'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Clientes Has Brindes Habilitados'), ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Clientes Has Brindes Habilitado'), ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="usuariosHasBrindes index large-9 medium-8 columns content">
    <h3><?= __('Usuarios Has Brindes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('brindes_habilitados_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('preco') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_insert') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_update') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuariosHasBrindes as $usuariosHasBrinde): ?>
            <tr>
                <td><?= $usuariosHasBrinde->has('usuario') ? $this->Html->link($usuariosHasBrinde->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $usuariosHasBrinde->usuario->id]) : '' ?></td>
                <td><?= $usuariosHasBrinde->has('clientes_has_brindes_habilitado') ? $this->Html->link($usuariosHasBrinde->clientes_has_brindes_habilitado->id, ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'view', $usuariosHasBrinde->clientes_has_brindes_habilitado->id]) : '' ?></td>
                <td><?= $this->Number->format($usuariosHasBrinde->preco) ?></td>
                <td><?= h($usuariosHasBrinde->audit_insert->format('d/m/Y H:i:s')) ?></td>
                <td><?= h(isset($usuariosHasBrinde->audit_update) ? $usuariosHasBrinde->audit_update->format('d/m/Y H:i:s') : null) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $usuariosHasBrinde->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $usuariosHasBrinde->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $usuariosHasBrinde->id], ['confirm' => __('Are you sure you want to delete # {0}?', $usuariosHasBrinde->id)]) ?>
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
