<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\PontuacoesComprovante[]|\Cake\Collection\CollectionInterface $pontuacoesComprovantes
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Pontuacoes Comprovante'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="pontuacoesComprovantes index large-9 medium-8 columns content">
    <h3><?= __('Pontuacoes Comprovantes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('clientes_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('nome_download') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_insert') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_update') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pontuacoesComprovantes as $pontuacoesComprovante): ?>
            <tr>
                <td><?= $this->Number->format($pontuacoesComprovante->clientes_id) ?></td>
                <td><?= $this->Number->format($pontuacoesComprovante->usuarios_id) ?></td>
                <td><?= h($pontuacoesComprovante->nome_download) ?></td>
                <td><?= h($pontuacoesComprovante->audit_insert->format('d/m/Y H:i:s')) ?></td>
                <td><?= h(isset($pontuacoesComprovante->audit_update) ? $pontuacoesComprovante->audit_update->format('d/m/Y H:i:s') : null) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $pontuacoesComprovante->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $pontuacoesComprovante->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $pontuacoesComprovante->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pontuacoesComprovante->id)]) ?>
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
