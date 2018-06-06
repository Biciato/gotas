<?php

/**
 * @description Lista os veículos de um usuário
 * @author 	    Gustavo Souza Gonçalves
 * @file 	    Template\Veiculos\meus_veiculos.php
 * @date 	    25/07/2017
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Meus Veículos', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../Veiculos/left_menu', ['mode' => 'view']) ?>
<div class="veiculos index col-lg-9 col-md-8 columns content">

    <legend><?= __('Veiculos') ?></legend>

    <div class="form-group">

        <?= $this->Form->create('Post', ['url' => 'Veiculos/meus_veiculos/']) ?>

        <div class="form-group row">
            <?= $this->Form->label('placa', 'Procurar', ['class' => 'col-sm-1 col-form-label']) ?>
            
            <div class="col-sm-9">
                <?= $this->Form->text('placa', ['class' => 'form-control', 'placeholder' => 'Informe a placa do veículo']) ?>
            </div>

            <?= $this->Form->submit("Pesquisar") ?>
        </div>

        <?= $this->Form->end() ?>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort('placa') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('modelo') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('fabricante') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('ano') ?></th>
                    
                    <th scope="col" class="actions">
                        <?= __('Ações') ?>
                        <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuariosHasVeiculos as $usuarioHasVeiculo) : ?>
                    
                <tr>
                    <td><?= h($usuarioHasVeiculo->veiculo->placa) ?></td>
                    <td><?= h($usuarioHasVeiculo->veiculo->modelo) ?></td>
                    <td><?= h($usuarioHasVeiculo->veiculo->fabricante) ?></td>
                    <td><?= h($usuarioHasVeiculo->veiculo->ano) ?></td>
                    
                    <td class="actions">
                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $usuarioHasVeiculo->id], ['class' => 'btn btn-primary btn-xs']) ?>
                        <?= $this->Html->link(__('Editar'), ['action' => 'edit', $usuarioHasVeiculo->id], ['class' => 'btn btn-default btn-xs']) ?>
                        <?= $this->Form->postLink(
                            __('Remover Vínculo'),
                            [
                                'action' => 'delete', $usuarioHasVeiculo->id
                            ],
                            ['confirm' => __('Deseja mesmo remover o vínculo do veículo {0}?', $usuarioHasVeiculo->veiculo->placa), 'class' => 'btn btn-danger btn-xs']
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
</div>


