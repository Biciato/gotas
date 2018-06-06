<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($user_logged['tipo_perfil'] <= (int)Configure::read('profileTypes')['AdminRegionalProfileType']) {

    $this->Breadcrumbs->add(
        'Escolher Unidade para Configurar os Brindes',
        [
            'controller' => 'clientes_has_brindes_habilitados',
            'action' => 'escolher_unidade_config_brinde'
        ]
    );
}

$this->Breadcrumbs->add('Configurar um Brinde de Unidade', [], ['class' => 'active']);

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
    <legend><?= __("Configurar um Brinde de Unidade") ?></legend>

<table class="table table-striped table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('Brindes.nome', ['label' => 'Brinde']) ?></th>
                <!-- TODO: parei aqui -->
                <th><?= $this->Paginator->sort('Brindes.ilimitado', ['label' => 'Qtde. Ilimitada?']) ?></th>
                <th><?= $this->Paginator->sort('Brindes.equipamento_rti_shower', ['label' => 'Equip. Smart Shower?']) ?></th>
                <th><?= $this->Paginator->sort('habilitado', ['label' => 'Habilitado?']) ?></th>
                <th><?= $this->Paginator->sort('status', ['label' => 'Atribuído à unidade?']) ?></th>
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($brindes_configurar as $brinde_configurar) : ?>

                <?php if ($brinde_configurar->Brindes['habilitado']) : ?>
                    <?php $brinde_configurar->status = $brinde_configurar->id == null ? false : true; ?>
                    <tr>
                        <td><?= h($brinde_configurar->Brindes['nome']) ?></td>
                        <td><?= h($this->Boolean->convertBooleanToString($brinde_configurar->Brindes['ilimitado'])) ?></td>
                        <td><?= h($this->Boolean->convertBooleanToString($brinde_configurar->Brindes['equipamento_rti_shower'])) ?></td>
                        <td><?= h($this->Boolean->convertEnabledToString(is_nulL($brinde_configurar->habilitado) ? false : $brinde_configurar->habilitado)) ?></td>
                        <td><?= h($this->Boolean->convertBooleanToString($brinde_configurar->status)) ?></td>
                        <td class="actions" style="white-space:nowrap">

                            <?php if (is_null($brinde_configurar->id)) : ?>
                                <?=
                                $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-plus'])
                                    ),
                                    '#',
                                    [
                                        'class' => 'btn btn-xs btn-primary btn-confirm',
                                        'title' => 'Adicionar',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-confirm-with-message',
                                        'data-message' => __(Configure::read('messageEnableQuestion'), $brinde_configurar->Brindes['nome']),
                                        'data-action' => Router::url(
                                            [
                                                'action' => 'habilitar_brinde',
                                                "?" => [
                                                    'brindes_id' => $brinde_configurar->Brindes['id'],
                                                    'clientes_id' => $clientes_id,
                                                ]
                                            ]
                                        ),
                                        'escape' => false
                                    ],
                                    false
                                )
                                ?>
                            <?php elseif (!$brinde_configurar->habilitado) : ?>

                                <?=
                                $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                    ),
                                    '#',
                                    [
                                        'class' => 'btn btn-xs btn-primary btn-confirm',
                                        'title' => 'Habilitar',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-confirm-with-message',
                                        'data-message' => __(Configure::read('messageEnableQuestion'), $brinde_configurar->Brindes['nome']),
                                        'data-action' => Router::url(
                                            [
                                                'action' => 'habilitar_brinde',
                                                "?" => [
                                                    'brindes_id' => $brinde_configurar->Brindes['id'],
                                                    'clientes_id' => $clientes_id,
                                                ]
                                            ]
                                        ),
                                        'escape' => false
                                    ],
                                    false
                                )
                                ?>

                            <?php else : ?>

                                <?= $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                    ),
                                    '#',
                                    [
                                        'class' => 'btn btn-xs btn-danger btn-confirm',
                                        'title' => 'Desabilitar',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-confirm-with-message',
                                        'data-message' => __(Configure::read('messageDisableQuestion'), $brinde_configurar->Brindes['nome']),
                                        'data-action' => Router::url(
                                            [
                                                'action' => 'desabilitar_brinde',
                                                "?" => [
                                                    'brindes_id' => $brinde_configurar->Brindes['id'],
                                                    'clientes_id' => $clientes_id,
                                                ]
                                            ]
                                        ),
                                        'escape' => false
                                    ],
                                    false
                                )
                                ?>

                                <?= $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-cogs'])
                                    ),
                                    [
                                        'action' => 'configurar_brinde',
                                        $brinde_configurar->id
                                    ],
                                    [
                                        'title' => 'Configurar',
                                        'class' => 'btn btn-primary btn-xs',
                                        'escape' => false
                                    ]
                                )
                                ?>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>
