<div class="row">
<nav class="col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Actions') ?></a></li>
        <li><?= $this->Html->link(__('New {0}', ['Brindes Estoque']), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List {0}', ['Brindes']), ['controller' => 'Brindes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New {0}', ['Brinde']), ['controller' => 'Brindes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List {0}', ['Usuarios']), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New {0}', ['Usuario']), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="brindesEstoque index col-md-10 columns content">
    <h3>Brindes Estoque</h3>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('brindes_id') ?></th>
                <th><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th><?= $this->Paginator->sort('quantidade') ?></th>
                <th><?= $this->Paginator->sort('tipo_operacao') ?></th>
                <th><?= $this->Paginator->sort('data') ?></th>
                <th><?= $this->Paginator->sort('audit_insert') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($brindesEstoque as $brindesEstoque): ?>
            <tr>
                <td><?= $brindesEstoque->has('brinde') ? $this->Html->link($brindesEstoque->brinde->id, ['controller' => 'Brindes', 'action' => 'view', $brindesEstoque->brinde->id]) : '' ?></td>
                <td><?= $brindesEstoque->has('usuario') ? $this->Html->link($brindesEstoque->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $brindesEstoque->usuario->id]) : '' ?></td>
                <td><?= $this->Number->format($brindesEstoque->quantidade) ?></td>
                <td><?= $this->Number->format($brindesEstoque->tipo_operacao) ?></td>
                <td><?= h($brindesEstoque->data) ?></td>
                <td><?= h($brindesEstoque->audit_insert->format('d/m/Y H:i:s')) ?></td>
                <td class="actions" style="white-space:nowrap">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $brindesEstoque->id], ['class'=>'btn btn-default btn-xs']) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $brindesEstoque->id], ['class'=>'btn btn-primary btn-xs']) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $brindesEstoque->id], ['confirm' => __('Are you sure you want to delete # {0}?', $brindesEstoque->id), 'class'=>'btn btn-danger btn-xs']) ?>
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