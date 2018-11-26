<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rede[]|\Cake\Collection\CollectionInterface $redes
 */
use Cake\Routing\Router;
use Cake\Core\Configure;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Redes', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);
?>

<?= $this->element(
    '../Redes/left_menu',
    [
        'mode' => 'view',
        'show_reports' => true
    ]
) ?>
<div class="redes index col-lg-9 col-md-10 columns content">
    <legend><?= __('Redes') ?></legend>

    <?= $this->element(
        '../Redes/filtro_redes',
        [
            'controller' => 'redes',
            'action' => 'index'
        ]
    ) ?>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('nome_rede') ?></th>
                <th scope="col"><?= $this->Paginator->sort('ativado', ['label' => 'Rede ativada']) ?></th>
                <th scope="col" class="actions"><?= __('Ações') ?>
                <?= $this->Html->tag(
                    'button',
                    __(
                        "{0} Legendas",
                        $this->Html->tag('i', '', ['class' => 'fa fa-book'])
                    ),
                    [
                        'class' => 'btn btn-xs btn-default right-align modal-legend-icons-save',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalLegendIconsSave'
                    ]
                ) ?>

            </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($redes as $rede) : ?>
            <tr>
                <td><?= h($rede->nome_rede) ?></td>
                <td><?= h($this->Boolean->convertBooleanToString($rede->ativado)) ?></td>
                <td class="actions">
                    <?= $this->Html->link(
                        __(
                            "{0}",
                            $this->Html->tag('i', '', ['class' => 'fa fa-cogs'])
                        ),
                        array(
                            'controller' => 'redes',
                            'action' => 'ver_detalhes', $rede->id
                        ),
                        array(
                            'class' => 'btn btn-xs btn-primary',
                            'title' => 'Configurar Parâmetros de Rede e Postos',
                            'escape' => false
                        )
                    )
                    ?>

                    <?= $this->Html->link(
                        __(
                            "{0}",
                            $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                        ),
                        [
                            'controller' => 'redes',
                            'action' => 'editar', $rede->id

                        ],
                        [
                            'class' => 'btn btn-xs btn-primary',
                            'title' => 'Editar',
                            'escape' => false
                        ]
                    ) ?>

                    <?php if ($rede->ativado) : ?>

                    <?= $this->Html->link(
                        __(
                            '{0} ',
                            $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                        ),
                        '#',
                        [
                            'title' => 'Desativar',
                            'class' => 'btn btn-xs btn-danger btn-confirm',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-delete-with-message',
                            'data-message' => __(Configure::read('messageDisableQuestion'), $rede->nome_rede),
                            'data-action' => Router::url(
                                [
                                    'controller' => 'redes',
                                    'action' => 'desativar', $rede->id,
                                    '?' =>
                                        [
                                        'rede_id' => $rede->id,
                                        'return_url' =>
                                            [
                                            'controller' => 'redes',
                                            'action' => 'index'
                                        ]
                                    ]
                                ]
                            ),
                            'escape' => false
                        ],
                        false
                    );
                    ?>

                    <?php else : ?>
                    <?= $this->Html->link(
                        __(
                            '{0} ',
                            $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                        ),
                        '#',
                        [
                            'title' => 'Ativar',
                            'class' => 'btn btn-xs btn-primary btn-confirm',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-delete-with-message',
                            'data-message' => __(Configure::read('messageEnableQuestion'), $rede->nome_rede),
                            'data-action' => Router::url(
                                [
                                    'controller' => 'redes',
                                    'action' => 'ativar', $rede->id,
                                    '?' =>
                                        [
                                        'rede_id' => $rede->id,
                                        'return_url' =>
                                            [
                                            'controller' => 'redes',
                                            'action' => 'index'
                                        ]
                                    ]
                                ]
                            ),
                            'escape' => false
                        ],
                        false
                    );
                    ?>
                    <?php endif; ?>

                    <?= $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
                        ),
                        '#',
                        [
                            'class' => 'btn btn-xs  btn-danger btn-confirm',
                            'title' => 'Remover',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-delete-with-message-confirmation',
                            'data-message' => __(Configure::read('messageDeleteQuestion'), $rede["nome_rede"]),
                            'data-action' => Router::url(
                                [
                                    'action' => 'delete', $rede->id,
                                    '?' =>
                                        [
                                        'redes_id' => $rede["id"]

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
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape' => false]) ?>
                <?= $this->Paginator->numbers(['escape' => false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape' => false]) ?>
            <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
</div>
