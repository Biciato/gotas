<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rede $rede
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

?>

<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-4">
        <h2>Lista de Redes</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <?= $this->Html->link("Início", ['controller' => "Pages", "action" => "index"]); ?>
            </li>
            <li class="breadcrumb-item">
                <?= $this->Html->link("Redes", ["controller" => "Redes", "action" => "index"]); ?>
            </li>
            <li class="breadcrumb-item active">
                <strong><?= sprintf("Rede: %s", $rede->nome_rede); ?></strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-8">
        <div class="title-action">
            <?php
            echo $this->Html->link(
                $this->Html->tag("span", " Novo", ["class" => "fas fa-plus", "escape" => false]),
                ["controller" => "Clientes", "action" => "add", $rede->id],
                ["class" => "btn btn-primary", "escape" => false]
            );
            ?>
        </div>
    </div>
</div>

<div class="content">
    <div class="row rede-details col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= h($rede->nome_rede) ?></h5>
            </div>
            <div class="ibox-content">
                <?= $this->element('../Redes/tabela_info_redes') ?>
            </div>
        </div>

    </div>

    <div class="form-group row clientes-filter col-lg-12">
        <div class="ibox">
            <div class="ibox-content">
                <?= $this->element('../Clientes/filtro_clientes', ['controller' => 'redes', 'action' => 'ver_detalhes', "id" => $rede->id, 'usuarioLogado' => $usuarioLogado]) ?>

                <table class="table table-striped table-hover">
                    <thead></thead>
                    <tr>
                        <th><?= $this->Paginator->sort('matriz', ['label' => 'Matriz/Filial']) ?></th>
                        <th><?= $this->Paginator->sort('tipo_unidade') ?></th>
                        <th><?= $this->Paginator->sort('nome_fantasia', ['label' => 'Nome Fantasia']) ?></th>
                        <th><?= $this->Paginator->sort('razao_social', ['label' => 'Razão Social']) ?></th>
                        <th><?= $this->Paginator->sort('cnpj', ['label' => 'CNPJ']) ?></th>

                        <th><?= __("Status") ?></th>
                        <th class="actions">
                            <?= __('Ações') ?>
                            <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes"><span class=" fa fa-book"> Legendas</span></div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($redesHasClientes as $key => $clienteRede) : ?>

                            <tr>
                                <td><?= h($clienteRede->cliente->matriz ? __("Matriz") : __("Filial")) ?></td>
                                <td><?= $this->ClienteUtil->getTypeUnity($clienteRede->cliente->tipo_unidade) ?></td>
                                <td><?= h($clienteRede->cliente->nome_fantasia) ?></td>
                                <td><?= h($clienteRede->cliente->razao_social) ?></td>
                                <td><?= h($this->NumberFormat->formatNumberToCNPJ($clienteRede->cliente->cnpj)) ?></td>
                                <td><?= h($this->Boolean->convertEnabledToString($clienteRede->cliente->ativado)) ?></td>

                                <td class="actions" style="white-space:nowrap">
                                    <?=
                                        $this->Html->link(
                                            __(
                                                "{0}",
                                                $this->Html->tag('i', '', ['class' => 'fa fa-cogs'])
                                            ),
                                            [
                                                'controller' => 'clientes',
                                                'action' => 'ver_detalhes',
                                                $clienteRede->cliente->id
                                            ],
                                            [
                                                'class' => 'btn btn-primary btn-xs botao-navegacao-tabela',
                                                'escape' => false,
                                                'title' => 'Configurar Parâmetros de Loja/Postos',
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
                                                $clienteRede->cliente->id
                                            ],
                                            [
                                                'title' => 'Editar',
                                                'class' => 'btn btn-primary btn-xs botao-navegacao-tabela',
                                                'escape' => false
                                            ]
                                        )
                                    ?>

                                    <?php if ($clienteRede->cliente->ativado) : ?>

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
                                                'data-message' => __(Configure::read('messageDisableQuestion'), $clienteRede->cliente->razao_social),
                                                'data-action' => Router::url(
                                                    [
                                                        'controller' => 'clientes',
                                                        'action' => 'desativar', $clienteRede->cliente->id,
                                                        '?' =>
                                                        [
                                                            'clientes_id' => $clienteRede->clientes_id,
                                                            'return_url' =>
                                                            [
                                                                'controller' => 'redes',
                                                                'action' => 'ver_detalhes', $clienteRede->redes_id
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
                                                'data-message' => __(Configure::read('messageEnableQuestion'), $clienteRede->cliente->razao_social),
                                                'data-action' => Router::url(
                                                    [
                                                        'controller' => 'clientes',
                                                        'action' => 'ativar', $clienteRede->cliente->id,
                                                        '?' =>
                                                        [
                                                            'clientes_id' => $clienteRede->clientes_id,
                                                            'return_url' =>
                                                            [
                                                                'controller' => 'redes',
                                                                'action' => 'ver_detalhes', $clienteRede->redes_id
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
                                            'data-message' => __(Configure::read('messageDeleteQuestion'), $clienteRede->cliente->razao_social),
                                            'data-action' => Router::url(
                                                [
                                                    'controller' => 'redes_has_clientes',
                                                    'action' => 'delete',
                                                    '?' =>
                                                    [
                                                        'redes_has_clientes_id' => $clienteRede->id,
                                                        'return_url' =>
                                                        [
                                                            'controller' => 'redes',
                                                            'action' => 'ver_detalhes', $clienteRede->redes_id
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
            </div>

        </div>
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
