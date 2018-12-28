<?php 

/**
 * @deprecated 1.0
 */

?> 

<div class="row">
<nav class="col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
        <li><?= $this->Html->link(__('Novo {0}', ['Brinde']), ['action' => 'add']) ?></li>
        
    </ul>
</nav>
<div class="brindes index col-md-10 columns content">
    <h3>Brindes</h3>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('clientes_id') ?></th>
                <th><?= $this->Paginator->sort('nome') ?></th>
                <th><?= $this->Paginator->sort('ilimitado') ?></th>
                <th><?= $this->Paginator->sort('preco_padrao') ?></th>
                
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($brindes as $brinde) : ?>
            <tr>
                <td><?= $brinde->has('cliente') ? $this->Html->link($brinde->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $brinde->cliente->id]) : '' ?></td>
                <td><?= h($brinde->nome) ?></td>
                <td><?= $this->Number->format($brinde->ilimitado) ?></td>
                <td><?= $this->Number->format($brinde->preco_padrao) ?></td>
                
                <td class="actions" style="white-space:nowrap">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $brinde->id], ['class' => 'btn btn-default btn-xs']) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $brinde->id], ['class' => 'btn btn-primary btn-xs']) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $brinde->id], ['confirm' => __('Are you sure you want to delete # {0}?', $brinde->id), 'class' => 'btn btn-danger btn-xs']) ?>
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