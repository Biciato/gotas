<?php

/**
 * tipos_brindes_cliente.ctp
 *
 * View para tipo_brindes_clientes/tipos_brindes_cliente
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TiposBrindesCliente[]|\Cake\Collection\CollectionInterface $tiposBrindesClientes
 *
 * @category View
 * @package App\Template\TiposBrindes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date 30/05/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.0.0
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
$this->Breadcrumbs->add('Detalhes da Rede', ['controller' => 'Redes', 'action' => 'ver_detalhes', $cliente->rede_has_cliente->redes_id]);
$this->Breadcrumbs->add('Detalhes da Unidade', ['controller' => 'clientes', 'action' => 'ver_detalhes', $cliente->id]);
$this->Breadcrumbs->add('Tipos de Brindes Habilitados', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->element("../TiposBrindesClientes/left_menu", ["mode" => "add", "clientesId" => $cliente->id]) ?>
<div class="tiposBrindesClientes view col-lg-9 col-mg-8 columns content">

    <legend><?= __("Gênero de Brindes Habilitados para cliente [{0}] / Nome Fantasia: {1}", $cliente->id, $cliente->nome_fantasia) ?> </legend>

    <?php if (sizeof($tiposBrindesClientes->toArray()) == 0) : ?>
        <?= __("Dados não encontrados para o cliente {0} ! ", $cliente->nome_fantasia) ?>

    <?php else : ?>

        <table class="table table-striped table-hover table-responsive">
            <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort("tipo_brindes_id") ?> </th>
                    <th scope="col"><?= $this->Paginator->sort("tipo_principal_codigo_brinde", ["label" => "Cód. Principal"]) ?> </th>
                    <th scope="col"><?= $this->Paginator->sort("tipo_secundario_codigo_brinde", ["label" => "Cód. Secundário"]) ?> </th>
                    <th scope="col"><?= __("Vinculado?") ?> </th>
                    <th scope="col"><?= $this->Paginator->sort("habilitado", ["label" => "Estado"]) ?> </th>
                    <th scope="col" class="actions">
                        <?= __('Ações') ?>
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
                <?php foreach ($tiposBrindesClientes as $key => $tipoBrindeItem) : ?>

                    <?php
                    $vinculado = count($tipoBrindeItem->clientes_has_brindes_habilitados) > 0;
                    $banhoSmart = $tipoBrindeItem->tipo_brinde["id"] <= 4;
                    ?>
                    <tr>
                        <td><?= $tipoBrindeItem["tipos_brindes_rede"]["nome"] . ($tipoBrindeItem["tipo_brinde"]["brinde_necessidades_especiais"] == 1 ? " (PNE)" : null) ?> </td>
                        <td><?= $tipoBrindeItem->tipo_principal_codigo_brinde ?> </td>
                        <td><?= strlen($tipoBrindeItem->tipo_secundario_codigo_brinde) == 1 ? "0" . $tipoBrindeItem->tipo_secundario_codigo_brinde : $tipoBrindeItem->tipo_secundario_codigo_brinde ?> </td>
                        <td><?= $this->Boolean->convertBooleanToString(count($tipoBrindeItem->clientes_has_brindes_habilitados) > 0) ?> </td>
                        <td><?= $this->Boolean->convertEnabledToString($tipoBrindeItem->habilitado) ?> </td>
                        <td class="actions" style="white-space:nowrap">
                            <!-- Info -->

                            <?= $this->Html->link(
                                __(
                                    '{0}',
                                    $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                                ),
                                [
                                    'action' => 'ver_detalhes',
                                    $tipoBrindeItem->id
                                ],
                                [
                                    'class' => 'btn btn-default btn-xs',
                                    'escape' => false,
                                    "title" => "Ver detalhes"
                                ]
                            ) ?>
                            <!-- Editar -->

                            <?php if (!$vinculado && !$banhoSmart) : ?>
                                <?= $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                                    ),
                                    [
                                        'action' => 'editar_tipo_brindes_cliente',
                                        $tipoBrindeItem->id
                                    ],
                                    [
                                        'class' => 'btn btn-primary btn-xs',
                                        'escape' => false,
                                        "title" => "Editar"
                                    ]
                                ) ?>

                            <?php endif; ?>
                            <?php if ($tipoBrindeItem->habilitado) : ?>
                                <!-- Desabilitar -->
                                <?= $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                    ),
                                    "#",
                                    array(
                                        'class' => 'btn btn-danger btn-xs',
                                        'escape' => false,
                                        "title" => "Habilitar",
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-delete-with-message',
                                        'data-message' => __(Configure::read('messageEnableQuestion'), $tipoBrindeItem["tipos_brindes_rede"]["nome"]),
                                        'data-action' => Router::url(
                                            array(
                                                'action' => 'alteraEstadoTiposBrindesCliente',
                                                '?' =>
                                                    array(
                                                    'tipos_brindes_cliente_id' => $tipoBrindeItem->id,
                                                    "clientes_id" => $cliente["id"],
                                                    "estado" => false,
                                                    'return_url' => array(
                                                        "controller" => "tipos_brindes_clientes",
                                                        "action" => 'tipos_brindes_cliente', $cliente["id"]
                                                    )
                                                )
                                            )
                                        ),
                                        "escape" => false
                                    ),
                                    [
                                        'class' => 'btn btn-danger btn-xs',
                                        'escape' => false,
                                        "title" => "Desabilitar"
                                    ]
                                ) ?>
                            <?php else : ?>
                                <!-- Habilitar -->
                                <?= $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                    ),
                                    "#",
                                    array(
                                        'class' => 'btn btn-primary btn-xs',
                                        'escape' => false,
                                        "title" => "Habilitar",
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-delete-with-message',
                                        'data-message' => __(Configure::read('messageDisableQuestion'), $tipoBrindeItem["tipos_brindes_rede"]["nome"]),
                                        'data-action' => Router::url(
                                            array(
                                                'action' => 'alteraEstadoTiposBrindesCliente',
                                                '?' =>
                                                    array(
                                                    'tipos_brindes_cliente_id' => $tipoBrindeItem->id,
                                                    "clientes_id" => $cliente["id"],
                                                    "estado" => true,
                                                    'return_url' => array(
                                                        "controller" => "tipos_brindes_clientes",
                                                        "action" => 'tipos_brindes_cliente', $cliente["id"]
                                                    )
                                                )
                                            )
                                        ),
                                        "escape" => false
                                    )
                                ) ?>

                            <?php endif; ?>
                            <!-- Delete -->
                            <?php if (!$vinculado && !$banhoSmart) : ?>

                                <?= $this->Html->link(
                                    __(
                                        '{0} ',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
                                    ),
                                    '#',
                                    [
                                        'class' => 'btn btn-xs btn-danger btn-confirm',
                                        "title" => "Deletar",
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-delete-with-message',
                                        'data-message' => __(Configure::read('messageDeleteQuestion'), $tipoBrindeItem["tipos_brindes_rede"]["nome"]),
                                        'data-action' => Router::url(
                                            [
                                                'action' => 'delete', $tipoBrindeItem->id,
                                                '?' =>
                                                    [
                                                    'tipo_brindes_cliente_id' => $tipoBrindeItem->id,
                                                    'return_url' => array(
                                                        "controller" => "tipos_brindes_clientes",
                                                        "action" => 'tipos_brindes_cliente', $cliente["id"]
                                                    )
                                                ]
                                            ]
                                        ),
                                        'escape' => false
                                    ],
                                    false
                                );
                                ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
