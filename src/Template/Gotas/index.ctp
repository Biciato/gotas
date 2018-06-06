<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Gota[]|\Cake\Collection\CollectionInterface $gotas
 */
?>
<nav class="col-lg-3 col-md-4 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="heading"><?= __('Ações') ?></li>
        <li><?= $this->Html->link(__('New Gota'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="gotas index col-lg-9 col-md-8 columns content">
    <h3><?= __('Gotas') ?></h3>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('clientes_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('multiplicador_gota') ?></th>
                <th scope="col" class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gotas as $gota) : ?>
                <tr>
                    <td><?= $gota->has('cliente') ? $this->Html->link($gota->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $gota->cliente->id]) : '' ?></td>
                    <td><?= $this->Number->format($gota->multiplicador_gota) ?></td>

                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $gota->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $gota->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $gota->id], ['confirm' => __('Are you sure you want to delete # {0}?', $gota->id)]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <center>
            <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('primeiro')) ?>
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape' => false]) ?>
                <?= $this->Paginator->numbers(['escape' => false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape' => false]) ?>
            <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
</div>
