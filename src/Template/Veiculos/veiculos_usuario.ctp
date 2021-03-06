<?php

/**
 * @description Lista os veículos de um usuário (Interface Admin)
 * @author 	    Gustavo Souza Gonçalves
 * @file 	    Template\Veiculos\veiculos_usuario.php
 * @date 	    24/10/2017
 *
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

// $this->Breadcrumbs->add('Veículos do Usuário', array(), array('class' => 'active'));
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'index']);

} else if ($usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminDeveloperProfileType']
    && $usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'usuarios_rede', $rede->id]);
}

$this->Breadcrumbs->add('Detalhes de Usuário', array("controller" => "usuarios", "action" => "view", $usuarios_id), []);

$this->Breadcrumbs->add('Veículos do Usuário', array(), ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../Veiculos/left_menu', ['mode' => 'view']) ?>
<div class="veiculos index col-lg-9 col-md-8 columns content">

    <div class="form-group">




    </div>
    <legend>        <?= __('Veiculos') ?></legend>

    <?php if (count($usuariosHasVeiculos) > 0) : ?>

    <div class="form-group">

        <div class="panel-group">
            <div class="panel panel-default">
                <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
                    <div>
                        <span class="fa fa-search"></span>
                        Exibir / Ocultar Filtros
                    </div>
                </div>
                <div id="filter-coupons" class="panel-collapse collapse in">
                    <div class="panel-body">

                        <?= $this->Form->create('Post', ['url' => ['controller' => 'veiculos', 'action' => 'veiculos_usuario', $usuarios_id]]) ?>

                        <div class="form-group row">

                            <div class="col-lg-10">
                                <?= $this->Form->label('placa', 'Procurar', ['class' => 'col-form-label']) ?>
                                <?= $this->Form->text(
                                    'placa',
                                    array(
                                        'class' => 'form-control',
                                        'placeholder' => 'Informe a placa do veículo',
                                        'Label' => "Placa"
                                    )
                                ); ?>
                            </div>

                            <div class="col-lg-2 vertical-align">
                                <?= $this->Form->button(
                                    __("{0} Pesquisar", '<i class="fa fa-search"></i>'),
                                    array(
                                        'class' => 'btn btn-primary btn-block',
                                        'type' => 'submit'
                                    )
                                ) ?>
                            </div>
                            <?= $this->Form->end() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>

        <table class="table table-striped table-hover table-condensed table-responsive">
            <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort('placa') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('modelo') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('fabricante') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('ano') ?></th>

                    <th scope="col" class="actions">
                        <?= __('Ações') ?>
                        <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes">
                            <span class=" fa fa-book"> Legendas</span>
                        </div>
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
                                '{0}',
                                $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                            ),
                            [
                                'action' => 'view', $usuario_has_veiculo->veiculos_id
                            ],
                            [
                                'title' => 'Ver',
                                'escape' => false,
                                'class' => 'btn btn-default btn-xs'
                            ]
                        ) ?>
                        <?php $this->Html->link(
                            __(
                                '{0}',
                                $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                            ),
                            [
                                'action' => 'edit', $usuario_has_veiculo->veiculos_id
                            ],
                            [
                                'title' => 'Editar',
                                'class' => 'btn btn-primary btn-xs',
                                'escape' => false
                            ]
                        ) ?>
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
                                        'action' => 'delete', $usuario_has_veiculo->id
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
                    <?= $this->Paginator->first('                    << ' . __('primeiro')) ?>
<?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape' => false]) ?>
<?= $this->Paginator->numbers(['escape' => false]) ?>
<?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape' => false]) ?>
<?= $this->Paginator->last(__('último') . ' >>') ?>
</ul>
<p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
</center>
</div>
</div>
