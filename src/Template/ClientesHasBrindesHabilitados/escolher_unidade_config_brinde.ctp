<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Escolher Unidade para Configurar os Brindes', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>

    </ul>
</nav>
<div class="clientes index col-lg-9 col-md-10 columns content">
    <legend><?= __("Escolher Unidade para Configurar os Brindes") ?></legend>

<table class="table table-striped table-hover">
    <thead>
        <tr>

            <th><?= __('Tipo de Unidade') ?></th>
            <th><?= __('Nome da Rede') ?></th>
            <th><?= __('Razão Social') ?></th>
            <th><?= __('Nome Fantasia') ?></th>
            <th><?= __('CNPJ') ?></th>


            <!-- <th><?= $this->Paginator->sort('id', ['label' => 'id']) ?></th>
            <th><?= $this->Paginator->sort('Clientes.tipo_unidade', ['label' => 'Tipo de Unidade']) ?></th>
            <th><?= $this->Paginator->sort('rede.nome_rede', ['label' => 'Nome da Rede']) ?></th>
            <th><?= $this->Paginator->sort('Clientes.razao_social', ['label' => 'Razão Social']) ?></th>
            <th><?= $this->Paginator->sort('Clientes.nome_fantasia', ['label' => 'Nome Fantasia']) ?></th>

            <th><?= $this->Paginator->sort('Clientes.cnpj', ['label' => 'CNPJ']) ?></th> -->
            <th class="actions">
                <?= __('Ações') ?>
                <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($redes_has_clientes as $redes_has_cliente) : ?>
        <tr>
            <!-- <td><?= ($redes_has_cliente->id) ?></td> -->
            <td><?= $this->ClienteUtil->getTypeUnity($redes_has_cliente->cliente->tipo_unidade) ?></td>
            <td><?= h($redes_has_cliente->rede->nome_rede) ?></td>
            <td><?= h($redes_has_cliente->cliente->razao_social) ?></td>
            <td><?= h($redes_has_cliente->cliente->nome_fantasia) ?></td>
            <td><?= h($this->NumberFormat->formatNumberToCNPJ($redes_has_cliente->cliente->cnpj)) ?></td>

            <td class="actions" style="white-space:nowrap">
                <?=
                $this->Html->link(
                    __(
                        '{0} Configurar',
                        $this->Html->tag('i', '', ['class' => 'fa fa-cogs'])
                    ),
                    [
                        'action' => 'configurar_brindes_unidade',
                        $redes_has_cliente->cliente->id
                    ],
                    [
                        'class' => 'btn btn-default btn-xs',
                        'escape' => false
                    ]
                )
                ?>

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
