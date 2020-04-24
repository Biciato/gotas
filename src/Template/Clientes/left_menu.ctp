<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/left_menu.ctp
 * @date     27/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$usuarioLogado = isset($usuarioLogado) ? $usuarioLogado : false;

$usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
$usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

if ($usuarioAdministrador) {
    $usuarioLogado = $usuarioAdministrar;
}

$showAdministratorsNetwork = isset($showAdministratorsNetwork) ? $showAdministratorsNetwork : false;

$id = isset($id) ? $id : null;
$view = isset($view) ? $view : false;
$configurations = isset($configurations) ? $configurations : false;
$dados_minha_rede = isset($dados_minha_rede) ? $dados_minha_rede : false;
?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active">
        <li class="active">
            <?= $this->Html->link(__('Menu'), []) ?>
        </li>

        <?php if ($view) : ?>

            <li class="active">
                <a href="">
                    <?= __('Ações') ?>
                </a>
            </li>

            <?php if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) : ?>

                <li>
                    <?= $this->Html->link(__('Editar {0}', ['Cliente']), ['action' => 'editar', $cliente->id]) ?>
                </li>

                <!-- Unidade desativada  -->

                <?php if ($cliente->ativado) : ?>
                    <li>

                        <?= $this->Html->link(
                            __(
                                'Desativar ',
                                $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                            ),
                            '#',
                            [
                                'title' => 'Desativar',
                                'class' => 'text-danger bg-danger',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-delete-with-message',
                                'data-message' => __(Configure::read('messageDisableQuestion'), $cliente->razao_social),
                                'data-action' => Router::url(
                                    [
                                        'controller' => 'clientes',
                                        'action' => 'desativar',
                                        '?' =>
                                        [
                                            'clientes_id' => $cliente->id,
                                            'return_url' =>
                                            [
                                                'controller' => 'clientes',
                                                'action' => 'ver_detalhes', $cliente->id
                                            ]
                                        ]
                                    ]
                                ),
                                'escape' => false
                            ],
                            false
                        );
                        ?>
                    </li>


                <?php else : ?>
                    <li>

                        <?= $this->Html->link(
                            __(
                                'Ativar ',
                                $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                            ),
                            '#',
                            [
                                'title' => 'Ativar',
                                'class' => 'text-info bg-info',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-delete-with-message',
                                'data-message' => __(Configure::read('messageEnableQuestion'), $cliente->razao_social),
                                'data-action' => Router::url(
                                    [
                                        'controller' => 'clientes',
                                        'action' => 'ativar',
                                        '?' =>
                                        [
                                            'clientes_id' => $cliente->id,
                                            'return_url' =>
                                            [
                                                'controller' => 'clientes',
                                                'action' => 'ver_detalhes', $cliente->id
                                            ]
                                        ]
                                    ]
                                ),
                                'escape' => false
                            ],
                            false
                        );
                        ?>
                    </li>

                <?php endif; ?>

                <li>
                    <?= $this->Html->link(
                        __(
                            'Remover Unidade ',
                            $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
                        ),
                        '#',
                        [
                            'title' => 'Deletar',
                            'class' => 'text-danger bg-danger',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-delete-with-message',
                            'data-message' => __(Configure::read('messageDeleteQuestion'), $cliente->razao_social),
                            'data-action' => Router::url(
                                [
                                    'controller' => 'redes_has_clientes',
                                    'action' => 'delete',
                                    '?' =>
                                    [
                                        'redes_has_clientes_id' => $cliente->redes_has_cliente->id,
                                        'return_url' =>
                                        [
                                            'controller' => 'redes',
                                            'action' => 'ver_detalhes', $cliente->redes_has_cliente->redes_id
                                        ]
                                    ]
                                ]
                            ),
                            'escape' => false
                        ],
                        false
                    );
                    ?>

                </li>

            <?php endif; ?>

        <?php endif; ?>

        <?php if ($configurations) : ?>

            <li class="active">
                <a href="">
                    <?= __('Configurações') ?>
                </a>
            </li>

            <li>
                <?= $this->Html->link("Configurações IHM", ['controller' => 'brindes', 'action' => 'index', $cliente["id"]]); ?>
            </li>
        <?php endif; ?>
        </li>
    </ul>
</nav>
