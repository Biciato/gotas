<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src\Template\Brindes\index.ctp
 *
 * @since     2019-04-20
 *
 * Arquivo que exibe brindes do cliente
 */
use Cake\Core\Configure;

// @todo menu
?>

<div class="col-lg-12">


</div>


<div class="row">
<?= $this->element("../Brindes/left_menu", array("mode" => "add")) ?>
<div class="brindes index col-lg-9 columns content">
    <h3>Brindes</h3>

    <?php echo $this->element("../Brindes/brindes_filtro", array("controller" => "brindes", "action" => "index", $clientesId)) ?>
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
