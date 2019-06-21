<nav class="col-lg-2 col-md-3">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a href=""><?= __('Actions') ?></a></li>
        <li><?= $this->Html->link(__('Edit {0}', ['Brindes Estoque']), ['action' => 'edit', $brindesEstoque->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete {0}', ['Brindes Estoque']), ['action' => 'delete', $brindesEstoque->id], ['confirm' => __('Are you sure you want to delete # {0}?', $brindesEstoque->id)]) ?> </li>
        <li><?= $this->Html->link(__('List {0}', ['Brindes Estoque']), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New {0}', ['Brindes Estoque']), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List {0}', ['Brindes']), ['controller' => 'Brindes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New {0}', ['Brinde']), ['controller' => 'Brindes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List {0}', ['Usuarios']), ['controller' => 'Usuarios', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New {0}', ['Usuario']), ['controller' => 'Usuarios', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="brindesEstoque view col-lg-10 col-md-9">
    <h3><?= h($brindesEstoque->id) ?></h3>
    <table class="table table-striped table-hover">
        <tr>
            <th>Brinde</th>
            <td><?= $brindesEstoque->has('brinde') ? $this->Html->link($brindesEstoque->brinde->id, ['controller' => 'Brindes', 'action' => 'view', $brindesEstoque->brinde->id]) : '' ?></td>
        </tr>
        <tr>
            <th>Usuario</th>
            <td><?= $brindesEstoque->has('usuario') ? $this->Html->link($brindesEstoque->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $brindesEstoque->usuario->id]) : '' ?></td>
        </tr>
        <tr>
            <th>'Id</th>
            <td><?= $this->Number->format($brindesEstoque->id) ?></td>
        </tr>
        <tr>
            <th>'Quantidade</th>
            <td><?= $this->Number->format($brindesEstoque->quantidade) ?></td>
        </tr>
        <tr>
            <th>'Tipo Operacao</th>
            <td><?= $this->Number->format($brindesEstoque->tipo_operacao) ?></td>
        </tr>
        <tr>
            <th>Data</th>
            <td><?= h($brindesEstoque->data) ?></tr>
        </tr>
        
        <tr>
            <th>Audit Insert</th>
            <td><?= h(($brindesEstoque->audit_insert->format('d/m/Y H:i:s'))) ?></tr>
        </tr>
        <tr>
            <th>Audit Update</th>
            <td><?= h(($brindesEstoque->audit_update->format('d/m/Y H:i:s'))) ?></tr>
        </tr>
    </table>
</div>
