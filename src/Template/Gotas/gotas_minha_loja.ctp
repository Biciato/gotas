<?php
/**
  * @var \App\View\AppView $this
  *
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Gotas/gotas_minha_loja.ctp
  * @date     23/10/2017
  */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Cadastro de Gotas de Minha Loja', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
    <?= $this->element('../Gotas/left_menu', ['mode' => 'view', 'go_back_url' => ['controller' => 'pages', 'action' => 'index']]) ?>
        <div class="gotas index col-lg-9 col-md-8 columns content">
            <legend>
                <?= __('Atribuição de Gotas de Por Consumo da Loja') ?>
            </legend>
            <table class="table table-striped table-hover">
                <thead>
                    <th>
                        <?= $this->Paginator->sort('', ['label' => 'Nome da Gota']) ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('', ['label' => 'Valor multiplicador']) ?>
                    </th>
                    <th class="actions">
                        <?= __('Ações') ?>
                        <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                    </th>
                </thead>
                <tbody>
                    <?php foreach ($gotas as $gota) : ?>
                    <tr>
                        <td scope="row">
                            <?= $gota->nome_parametro ?>
                        </td>
                        <td>
                            <?= $this->Number->precision($gota->multiplicador_gota, 2) ?>
                        </td>
                        <td class="actions" style="white-space:nowrap">
                        <?= $this->Html->link(
                                __(
                                    '{0}',
                                    $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                                ),
                                [
                                    'action' => 'editar_gota',
                                    $gota->id
                                ],
                                [
                                    'class' => 'btn btn-primary btn-xs',
                                    'title' => 'Editar',
                                    'escape' => false
                                ]
                            ) ?>

                            <?php if ($gota->habilitado) : ?>

                                <?= $this->Html->link(
                                    __(
                                        "{0}",
                                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                    ),
                                    '#',
                                    [
                                        'class' => 'btn btn-primary btn-danger btn-xs',
                                        'title' => 'Desabilitar',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-delete-with-message',
                                        'data-message' => __(Configure::read('messageDisableQuestion'), $gota->nome_parametro),
                                        'data-action' => Router::url(
                                            [
                                                'controller' => 'gotas',
                                                'action' => 'desabilitarGota', $gota->id
                                            ]
                                        ), 'escape' => false
                                    ],
                                    false
                                ) ?>
                            <?php else : ?>
                                <?= $this->Html->link(
                                    __(
                                        "{0}",
                                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                    ),
                                    '#',
                                    [
                                        'class' => 'btn btn-primary btn-primary btn-xs',
                                        'title' => 'Habilitar',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-confirm-with-message',
                                        'data-message' => __(Configure::read('messageEnableQuestion'), $gota->nome_parametro),
                                        'data-action' => Router::url(
                                            [
                                                'controller' => 'gotas',
                                                'action' => 'habilitarGota', $gota->id
                                            ]
                                        ), 'escape' => false
                                    ],
                                    false
                                ) ?>

                            <?php endif; ?>

                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
