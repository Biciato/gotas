<nav class="col-lg-2 col-md-3">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a href=""><?= __('Actions') ?></a></li>
        <li><?= $this->Html->link(__('Edit {0}', ['Brinde']), ['action' => 'edit', $brinde->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete {0}', ['Brinde']), ['action' => 'delete', $brinde->id], ['confirm' => __('Are you sure you want to delete # {0}?', $brinde->id)]) ?> </li>
        <li><?= $this->Html->link(__('List {0}', ['Brindes']), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New {0}', ['Brinde']), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List {0}', ['Clientes']), ['controller' => 'Clientes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New {0}', ['Cliente']), ['controller' => 'Clientes', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="brindes view col-lg-10 col-md-9">
    <h3><?= h($brinde->nome) ?></h3>
    <table class="table table-striped table-hover">
        <tr>
            <th>Nome</th>
            <td><?= h($brinde->nome) ?></td>
        </tr>
       
        <tr>
            <th>Estoque Ilimitado</th>
            <td><?= $this->Boolean->convertBooleanToString($brinde->ilimitado) ?></td>
        </tr>
        <tr>
            <th>Preco (em gotas):</th>
            <td><?= $this->Number->precision($brinde->preco_padrao, 2) ?></td>
        </tr>
        
    </table>
</div>
