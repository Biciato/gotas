<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\UsuariosHasVeiculo[]|\Cake\Collection\CollectionInterface $usuariosHasVeiculos
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Usuarios Has Veiculo'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Veiculos'), ['controller' => 'Veiculos', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Veiculo'), ['controller' => 'Veiculos', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="usuariosHasVeiculos index large-9 medium-8 columns content">
    <h3><?= __('Usuarios Has Veiculos') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('veiculos_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuariosHasVeiculos as $usuariosHasVeiculo): ?>
            <tr>
                <td><?= $usuariosHasVeiculo->has('usuario') ? $this->Html->link($usuariosHasVeiculo->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $usuariosHasVeiculo->usuario->id]) : '' ?></td>
                <td><?= $usuariosHasVeiculo->has('veiculo') ? $this->Html->link($usuariosHasVeiculo->veiculo->id, ['controller' => 'Veiculos', 'action' => 'view', $usuariosHasVeiculo->veiculo->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $usuariosHasVeiculo->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $usuariosHasVeiculo->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $usuariosHasVeiculo->id], ['confirm' => __('Are you sure you want to delete # {0}?', $usuariosHasVeiculo->id)]) ?>
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
