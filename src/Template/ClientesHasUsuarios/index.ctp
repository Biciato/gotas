<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\ClientesHasUsuario[]|\Cake\Collection\CollectionInterface $clientesHasUsuarios
  */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<nav class="col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
        <li><?= $this->Html->link(__('New Clientes Has Usuario'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="clientesHasUsuarios index col-lg-9 col-md-8 columns content">
    <h3><?= __('Clientes Has Usuarios') ?></h3>
    <table class="table table-striped table-hover">
    
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('clientes_id') ?></th>
                <th><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientesHasUsuarios as $clientesHasUsuario): ?>
            <tr>
                <td><?= $clientesHasUsuario->has('cliente') ? $this->Html->link($clientesHasUsuario->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $clientesHasUsuario->cliente->id]) : '' ?></td>
                <td><?= $clientesHasUsuario->has('usuario') ? $this->Html->link($clientesHasUsuario->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $clientesHasUsuario->usuario->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $clientesHasUsuario->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $clientesHasUsuario->id]) ?>
                    
                    <?= $this->Html->link(__('{0} Deletar',
                        $this->Html->tag('i', '', ['class' => 'fa fa-trash']) ),
                        '#',
                        [
                            'class'=>'btn btn-xs btn-danger btn-confirm',
                            'data-toggle'=> 'modal',
                            'data-target' => '#modal-delete-with-message',
                            'data-message' => __(Configure::read('messageUnlinkQuestion'), $clientesHasUsuario->usuario->nome),
                            'data-action'=> Router::url(
                                [
                                    'action' => 'delete', $clientesHasUsuario->id,
                                    '?' => 
                                    [
                                        'clientes_has_usuario' => $clientesHasUsuario->id,
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
