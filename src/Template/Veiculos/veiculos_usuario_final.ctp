<?php

/**
 * @description Lista os veículos de um usuário (Interface Funcionário)
 * @author 	    Gustavo Souza Gonçalves
 * @file 	    Template\Veiculos\veiculos_usuario.php
 * @date 	    18/02/2018
 *
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'atualizar_cadastro_cliente', 'mode_selected' => 'atualizar_cadastro_cliente_veiculos']) ?>
<div class="veiculos index col-lg-9 col-md-8 columns content">


    <legend><?= __('Veiculos') ?> </legend>

    <?= $this->Form->create('Post', ['url' => ['controller' => 'veiculos', 'action' => 'veiculos_usuario_final', $usuarios_id]]) ?>

    <div class="form-group row">

        <?= $this->Form->label('placa', 'Procurar', ['class' => 'col-sm-1 col-form-label']) ?>
        
        <div class="col-sm-9">
            
            <?= $this->Form->text('placa', ['class' => 'form-control', 'placeholder' => 'Informe a placa do veículo']) ?>
        </div>

    <?= $this->Form->submit("Pesquisar") ?>

    <?= $this->Form->end() ?>
    </div>

    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= __('Placa') ?></th>
                <th scope="col"><?= __('Modelo') ?></th>
                <th scope="col"><?= __('Fabricante') ?></th>
                <th scope="col"><?= __('Ano') ?></th>
                
                <th scope="col" class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuariosHasVeiculos as $usuario_has_veiculo) : ?>
                
            <tr>
                <td><?= h($usuario_has_veiculo->veiculo->placa) ?></td>
                <td><?= h($usuario_has_veiculo->veiculo->modelo) ?></td>
                <td><?= h($usuario_has_veiculo->veiculo->fabricante) ?></td>
                <td><?= h($usuario_has_veiculo->veiculo->ano) ?></td>
                
                <td class="actions">
                    <?= $this->Html->link(
                        __(
                            '{0} ',
                            $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
                        ),
                        '#',
                        [
                            'title' => 'Deletar',
                            'class' => 'btn btn-primary btn-danger btn-xs',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-delete',
                            'data-action' => Router::url(
                                [
                                    'controller' => 'usuarios_has_veiculos',
                                    'action' => 'delete_veiculo_usuario_final', $usuario_has_veiculo->id
                                ]
                            ), 'escape' => false
                        ],
                        false
                    ) ?>
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
