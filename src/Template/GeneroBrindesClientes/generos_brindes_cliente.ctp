<?php

/**
 * generos_brindes_cliente.ctp
 *
 * View para genero_brindes_clientes/generos_brindes_cliente
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\GeneroBrindesCliente[]|\Cake\Collection\CollectionInterface $generoBrindesClientes
 *
 * @category View
 * @package App\Template\GeneroBrindes
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
$this->Breadcrumbs->add('Gênero de Brindes Habilitados', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->element("../GeneroBrindesClientes/left_menu", ["mode" => "view", "clientesId" => $cliente->id]) ?>
<div class="generoBrindesClientes view col-lg-9 col-mg-8 columns content">

    <legend><?= __("Gênero de Brindes Habilitados para cliente [{0}] / Nome Fantasia: {1}", $cliente->id, $cliente->nome_fantasia) ?> </legend>

    <?php if (sizeof($generoBrindesClientes->toArray()) == 0) : ?>
        <?= __("Dados não encontrados para o cliente {0} ! ", $cliente->nome_fantasia) ?>

    <?php else : ?>

        <table class="table table-striped table-hover table-responsive">
            <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort("genero_brindes_id") ?> </th>
                    <th scope="col"><?= $this->Paginator->sort("tipo_principal_codigo_brinde", ["label" => "Cód. Principal"]) ?> </th>
                    <th scope="col"><?= $this->Paginator->sort("tipo_secundario_codigo_brinde", ["label" => "Cód. Secundário"]) ?> </th>
                    <th scope="col"><?= __("Vinculado?") ?> </th>
                    <th scope="col"><?= $this->Paginator->sort("habilitado") ?> </th>
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
                <?php foreach ($generoBrindesClientes as $key => $generoBrindeItem) : ?>

                    <?php
                        $vinculado = count($generoBrindeItem->clientes_has_brindes_habilitados) > 0;
                        $banhoSmart = $generoBrindeItem->genero_brinde["id"] <= 4;
                    ?>
                    <tr>
                        <td><?= $generoBrindeItem->genero_brinde->nome . ($generoBrindeItem->genero_brinde->brinde_necessidades_especiais == 1 ? " (PNE)" : null) ?> </td>
                        <td><?= $generoBrindeItem->tipo_principal_codigo_brinde ?> </td>
                        <td><?= strlen($generoBrindeItem->tipo_secundario_codigo_brinde) == 1 ? "0" . $generoBrindeItem->tipo_secundario_codigo_brinde : $generoBrindeItem->tipo_secundario_codigo_brinde ?> </td>
                        <td><?= $this->Boolean->convertBooleanToString(count($generoBrindeItem->clientes_has_brindes_habilitados) > 0) ?> </td>
                        <td><?= $this->Boolean->convertEnabledToString($generoBrindeItem->habilitado) ?> </td>
                        <td class="actions" style="white-space:nowrap">
                            <!-- Info -->

                            <?= $this->Html->link(
                                __(
                                    '{0}',
                                    $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                                ),
                                [
                                    'action' => 'ver_detalhes',
                                    $generoBrindeItem->id
                                ],
                                [
                                    'class' => 'btn btn-default btn-xs',
                                    'escape' => false,
                                    "title" => "Ver detalhes"
                                ]
                            ) ?>
                            <!-- Editar -->

                            <?php if (!$vinculado && !$banhoSmart):  ?>
                                <?= $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                                    ),
                                    [
                                        'action' => 'editar_genero_brindes_cliente',
                                        $generoBrindeItem->id
                                    ],
                                    [
                                        'class' => 'btn btn-primary btn-xs',
                                        'escape' => false,
                                        "title" => "Editar"
                                    ]
                                ) ?>

                            <?php endif; ?>
                            <?php if ($generoBrindeItem->habilitado) : ?>
                                <!-- Desabilitar -->
                                <?= $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                    ),
                                    [
                                        'action' => 'editar_genero_brinde',
                                        $generoBrindeItem->id
                                    ],
                                    [
                                        'class' => 'btn btn-danger btn-xs',
                                        'escape' => false,
                                        "title" => "Editar"
                                    ]
                                ) ?>
                            <?php else : ?>
                                <!-- Habilitar -->
                                <?= $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                    ),
                                    [
                                        'action' => 'editar_genero_brinde',
                                        $generoBrindeItem->id
                                    ],
                                    [
                                        'class' => 'btn btn-primary btn-xs',
                                        'escape' => false,
                                        "title" => "Editar"
                                    ]
                                ) ?>

                            <?php endif; ?>
                            <!-- Delete -->
                            <?php if (!$vinculado && !$banhoSmart):  ?>

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
                                        'data-message' => __(Configure::read('messageDeleteQuestion'), $generoBrindeItem->genero_brinde->nome),
                                        'data-action' => Router::url(
                                            [
                                                'action' => 'delete', $generoBrindeItem->id,
                                                '?' =>
                                                    [
                                                    'genero_brindes_cliente_id' => $generoBrindeItem->id,
                                                    'return_url' => array("controller" => "genero_brindes_clientes",
                                                    "action" => 'generos_brindes_cliente', $cliente["id"])
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
