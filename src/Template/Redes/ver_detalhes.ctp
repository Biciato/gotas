<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rede $rede
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);

$this->Breadcrumbs->add('Detalhes da Rede', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->element('../Redes/left_menu', [
    'mode' => 'details',
    'redes_id' => $rede->id,
    'go_back_url' => [
        'controller' => 'redes',
        'action' => 'index'
    ]
]) ?>


<div class="redes view col-lg-9 col-md-10 columns content">
    <legend><?= h($rede->nome_rede) ?></legend>
    <?= $this->element('../Redes/tabela_info_redes') ?>

    <?= $this->element('../Clientes/filtro_clientes', ['controller' => 'redes', 'action' => 'ver_detalhes', "id" => $rede->id, 'usuarioLogado' => $usuarioLogado]) ?>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('matriz', ['label' => 'Matriz/Filial']) ?></th>
                <th><?= $this->Paginator->sort('tipo_unidade') ?></th>
                <th><?= $this->Paginator->sort('nome_fantasia', ['label' => 'Nome Fantasia']) ?></th>
                <th><?= $this->Paginator->sort('razao_social', ['label' => 'Razão Social']) ?></th>
                <th><?= $this->Paginator->sort('cnpj', ['label' => 'CNPJ']) ?></th>

                <th><?= __("Status") ?></th>
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($redes_has_clientes as $key => $rede_has_cliente) : ?>

            <tr>
                <td><?= h($rede_has_cliente->cliente->matriz ? __("Matriz") : __("Filial")) ?></td>
                <td><?= $this->ClienteUtil->getTypeUnity($rede_has_cliente->cliente->tipo_unidade) ?></td>
                <td><?= h($rede_has_cliente->cliente->nome_fantasia) ?></td>
                <td><?= h($rede_has_cliente->cliente->razao_social) ?></td>
                <td><?= h($this->NumberFormat->formatNumberToCNPJ($rede_has_cliente->cliente->cnpj)) ?></td>
                <td><?= h($this->Boolean->convertEnabledToString($rede_has_cliente->cliente->ativado)) ?></td>

                <td class="actions" style="white-space:nowrap">
                    <?=
                    $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                        ),
                        [
                            'controller' => 'clientes',
                            'action' => 'ver_detalhes',
                            $rede_has_cliente->cliente->id
                        ],
                        [
                            'title' => 'Ver detalhes',
                            'class' => 'btn btn-default btn-xs',
                            'escape' => false
                        ]
                    )
                    ?>
                    <?=
                    $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                        ),
                        [
                            'controller' => 'clientes',
                            'action' => 'editar',
                            $rede_has_cliente->cliente->id
                        ],
                        [
                            'title' => 'Editar',
                            'class' => 'btn btn-primary btn-xs',
                            'escape' => false
                        ]
                    )
                    ?>

                    <?php if ($rede_has_cliente->cliente->ativado) : ?>

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
                            'data-message' => __(Configure::read('messageDisableQuestion'), $rede_has_cliente->cliente->razao_social),
                            'data-action' => Router::url(
                                [
                                    'controller' => 'clientes',
                                    'action' => 'desativar', $rede_has_cliente->cliente->id,
                                    '?' =>
                                        [
                                        'clientes_id' => $rede_has_cliente->clientes_id,
                                        'return_url' =>
                                            [
                                            'controller' => 'redes',
                                            'action' => 'ver_detalhes', $rede_has_cliente->redes_id
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
                            'data-message' => __(Configure::read('messageEnableQuestion'), $rede_has_cliente->cliente->razao_social),
                            'data-action' => Router::url(
                                [
                                    'controller' => 'clientes',
                                    'action' => 'ativar', $rede_has_cliente->cliente->id,
                                    '?' =>
                                        [
                                        'clientes_id' => $rede_has_cliente->clientes_id,
                                        'return_url' =>
                                            [
                                            'controller' => 'redes',
                                            'action' => 'ver_detalhes', $rede_has_cliente->redes_id
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
                            '{0} ',
                            $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
                        ),
                        '#',
                        [
                            'title' => 'Deletar',
                            'class' => 'btn btn-xs btn-danger btn-confirm',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-delete-with-message',
                            'data-message' => __(Configure::read('messageDeleteQuestion'), $rede_has_cliente->cliente->razao_social),
                            'data-action' => Router::url(
                                [
                                    'controller' => 'redes_has_clientes',
                                    'action' => 'delete',
                                    '?' =>
                                        [
                                        'redes_has_clientes_id' => $rede_has_cliente->id,
                                        'return_url' =>
                                            [
                                            'controller' => 'redes',
                                            'action' => 'ver_detalhes', $rede_has_cliente->redes_id
                                        ]

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


<?php
if (Configure::read('debug')) {
    echo $this->Html->css("styles/redes/ver_detalhes");
} else {
    echo $this->Html->css("styles/redes/ver_detalhes.min");
}

echo $this->fetch('script');
echo $this->fetch('css');
?>
