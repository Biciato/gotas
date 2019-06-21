<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
        <li><?= $this->Html->link(__('Novo {0}', ['Cliente']), ['action' => 'adicionar_rede']) ?></li>
    </ul>
</nav>
<div class="clientes index col-lg-9 col-md-10 columns content">
    <legend><?= __("Redes Cadastradas") ?></legend>

    <?= 
        $this->element(
            '../Clientes/filtro_clientes', 
            [
                'controller' => 'clientes',
                'action' => 'index'
            ]
        );
    ?>

<table class="table table-striped table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('tipo_unidade') ?></th>
                <th><?= $this->Paginator->sort('nome_rede', ['label' => 'Nome da Rede']) ?></th>
                <th><?= $this->Paginator->sort('razao_social', ['label' => 'Razão Social']) ?></th>
                <th><?= $this->Paginator->sort('nome_fantasia', ['label' => 'Nome Fantasia']) ?></th>
                
                <th><?= $this->Paginator->sort('cnpj', ['label' => 'CNPJ']) ?></th>
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?= $this->ClienteUtil->getTypeUnity($cliente->tipo_unidade) ?></td>
                <td><?= h($cliente->nome_rede) ?></td>
                <td><?= h($cliente->razao_social) ?></td>
                <td><?= h($cliente->nome_fantasia) ?></td>
                <td><?= h($this->NumberFormat->formatNumberToCNPJ($cliente->cnpj)) ?></td>
                
                <td class="actions" style="white-space:nowrap">
                    <?= 
                        $this->Html->link(
                            __('{0} Ver detalhes',
                                $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])),
                                [
                                    'action' => 'view',
                                    $cliente->id
                                ], 
                                [
                                    'class'=>'btn btn-default btn-xs',
                                    'escape' => false
                                ]
                            ) 
                    ?>
                    <?= 
                        $this->Html->link(
                            __('{0} Editar',
                            $this->Html->tag('i', '', ['class' => 'fa fa-edit'])),
                            [
                                'action' => 'editar_rede', 
                                $cliente->id
                            ], 
                            [
                                'class' => 'btn btn-primary btn-xs',
                                'escape' => false
                            ]
                        )
                    ?>
                    
                    <?= $this->Html->link(__('{0} Deletar',
                        $this->Html->tag('i', '', ['class' => 'fa fa-trash']) ),
                        '#',
                        [
                            'class'=>'btn btn-xs btn-danger btn-confirm',
                            'data-toggle'=> 'modal',
                            'data-target' => '#modal-delete-with-message',
                            'data-message' => __(Configure::read('messageDeleteQuestion'), $cliente->razao_social),
                            'data-action'=> Router::url(
                                [
                                    'action' => 'delete', $cliente->id,
                                    '?' => 
                                    [
                                        'cliente_id' => $cliente->id,
                                        'return_url' => 'index'
                                    ]
                                ]
                            ),
                                'escape' => false
                        ],
                        false
                        ); 
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
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape'=>false]) ?>
                <?= $this->Paginator->numbers(['escape'=>false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape'=>false]) ?>
                <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
</div>
