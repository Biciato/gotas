<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] <= (int)Configure::read('profileTypes')['AdminRegionalProfileType']) {

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
                <th><?= __('Brinde') ?></th>
                <th><?= __('Qtde. Ilimitada?') ?></th>
                <th><?= __('Habilitado?') ?></th>
                <th><?= __('Pendente Configuracao?') ?></th>
                <th><?= __('Atribuído à unidade?') ?></th>
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($brindesConfigurar as $brinde) : ?>
                <tr>
                    <td><?= h($brinde['nome']) ?></td>
                    <td><?= h($this->Boolean->convertBooleanToString($brinde['ilimitado'])) ?></td>
                    <td><?= h($this->Boolean->convertEnabledToString(is_null($brinde["brinde_vinculado"]) ? false : $brinde["brinde_vinculado"]["habilitado"])) ?></td>
                    <!-- Campo calculado -->
                    <td><?= h($this->Boolean->convertBooleanToString($brinde['pendente_configuracao'])) ?></td>
                    <td><?= h($this->Boolean->convertBooleanToString($brinde["atribuido"])) ?></td>
                    <td class="actions" style="white-space:nowrap">

                        <?php if ($brinde["atribuido"] == 0) : ?>
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
                                    'data-message' => __(Configure::read('messageEnableQuestion'), $brinde["nome"]),
                                    'data-action' => Router::url(
                                        [
                                            'action' => 'habilitar_brinde',
                                            "?" => [
                                                'brindes_id' => $brinde["id"],
                                                'clientes_id' => $clientesId,
                                            ]
                                        ]
                                    ),
                                    'escape' => false
                                ],
                                false
                            )
                            ?>
                        <?php else : ?>
                            <?php if ($brinde["brinde_vinculado"]["habilitado"] == 0) : ?>

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
                                        'data-message' => __(Configure::read('messageEnableQuestion'), $brinde['nome']),
                                        'data-action' => Router::url(
                                            [
                                                'action' => 'habilitar_brinde',
                                                "?" => [
                                                    'brindes_id' => $brinde["id"],
                                                    'clientes_id' => $clientesId,
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
                                        'data-message' => __(Configure::read('messageDisableQuestion'), $brinde["nome"]),
                                        'data-action' => Router::url(
                                            [
                                                'action' => 'desabilitar_brinde',
                                                "?" => [
                                                    'brindes_id' => $brinde["id"],
                                                    'clientes_id' => $clientesId,
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
                                        'action' => 'configurar_brinde', $brinde["brinde_vinculado"]["id"]
                                    ],
                                    [
                                        'title' => 'Configurar',
                                        'class' => 'btn btn-primary btn-xs',
                                        'escape' => false
                                    ]
                                )
                                ?>
                            <?php endif; ?>
                        <?php endif; ?>

                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>
