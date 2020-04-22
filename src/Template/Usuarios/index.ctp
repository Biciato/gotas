<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
?>
        <div class="row">
            <?php echo $this->Form->create('filtro', ['id' => 'filtro-usuarios', 'class' => 'form-inline']); ?>
                <div class="form-group">
                    <div></div>
                </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <div class="row">
                <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Lista de usuários</h5>
                    </div>
                    <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Tipo de perfil</th>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>E-mail</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($usuarios as $usuario) : ?>
                                <tr>
                                    <td>
                                        <?= h($this->UserUtil->getProfileType($usuario["tipo_perfil"])) ?>
                                    </td>
                                    <td>
                                        <?= h($usuario->nome) ?>
                                    </td>
                                    <td>
                                        <?= h($this->NumberFormat->formatNumberToCpf($usuario->cpf)) ?>
                                    </td>

                                    <td>
                                        <?= h($usuario->email) ?>
                                    </td>
                                    <td class="actions" style="white-space:nowrap">

                                        <?=
                                            $this->Html->link(
                                                __(
                                                    '{0} ',
                                                    $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                                                ),
                                                [
                                                    'action' => 'view',
                                                    $usuario->id
                                                ],
                                                [
                                                    'class' => 'btn btn-xs btn-default botao-navegacao-tabela',
                                                    'escape' => false,
                                                    'title' => 'Ver detalhes'

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
                                                    'action' => $usuario["tipo_perfil"] < Configure::read("profileTypes")["UserProfileType"] ? "editarOperador" : "editar",
                                                    $usuario->id
                                                ],
                                                [
                                                    'class' => 'btn btn-xs btn-primary botao-navegacao-tabela',
                                                    'escape' => false,
                                                    'title' => 'Editar'
                                                ]
                                            )
                                        ?>

                                        <?php if ($usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminNetworkProfileType']) {

                                            if ($usuario["tipo_perfil"] < PROFILE_TYPE_USER) {

                                                if (!empty($usuario->clientes_has_usuario)) {

                                                    if ($usuario->clientes_has_usuario->conta_ativa == true) {
                                                        echo $this->Html->link(
                                                            __(
                                                                '{0} ',
                                                                $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                                            ),
                                                            '#',
                                                            [
                                                                'class' => 'btn btn-xs  btn-danger btn-confirm',
                                                                'title' => 'Desativar',
                                                                'data-toggle' => 'modal',
                                                                'data-target' => '#modal-delete-with-message',
                                                                'data-message' => __(Configure::read('messageDisableAccessUserQuestion'), $usuario->nome),
                                                                'data-action' =>
                                                                empty($usuario->clientes_has_usuario) ?
                                                                    Router::url(
                                                                        [
                                                                            'action' => 'desabilitar_usuario', "?" =>
                                                                            [
                                                                                'usuarios_id' => $usuario->id,
                                                                                'return_url' => Router::url([
                                                                                    'controller' => 'usuarios',
                                                                                    'action' => 'index'
                                                                                ])
                                                                            ]
                                                                        ]
                                                                    )
                                                                    : Router::url(array(
                                                                        "controller" => "clientes_has_usuarios", 'action' => 'alteraContaAtivaUsuario', "?" =>
                                                                        array(
                                                                            "id" => $usuario->clientes_has_usuario->id,
                                                                            "usuarios_id" => $usuario->id,
                                                                            "clientes_id" => $usuario->clientes_has_usuario->clientes_id,
                                                                            "conta_ativa" => 0,
                                                                            'return_url' => Router::url(
                                                                                array(
                                                                                    'controller' => 'usuarios',
                                                                                    'action' => 'index'
                                                                                )
                                                                            )
                                                                        )
                                                                    )),
                                                                'escape' => false
                                                            ],
                                                            false
                                                        );
                                                    } else {
                                                        echo $this->Html->link(
                                                            __(
                                                                '{0}',
                                                                $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                                            ),
                                                            '#',
                                                            [
                                                                'class' => 'btn btn-xs  btn-primary btn-confirm',
                                                                'title' => 'Ativar',
                                                                'data-toggle' => 'modal',
                                                                'data-target' => '#modal-delete-with-message',
                                                                'data-message' => __(Configure::read('messageEnableAccessUserQuestion'), $usuario->nome),
                                                                'data-action' =>
                                                                empty($usuario->clientes_has_usuarios) ?
                                                                    Router::url(
                                                                        ['action' => 'habilitar_usuario', "?" =>
                                                                        [
                                                                            'usuarios_id' => $usuario->id,
                                                                            'return_url' => Router::url([
                                                                                'controller' => 'usuarios',
                                                                                'action' => 'index'
                                                                            ])
                                                                        ]]
                                                                    )
                                                                    : Router::url(
                                                                        array(

                                                                            "controller" => "clientes_has_usuarios", 'action' => 'alteraContaAtivaUsuario', "?" =>
                                                                            array(
                                                                                "id" => $usuario->clientes_has_usuario->id,
                                                                                'usuarios_id' => $usuario->id,
                                                                                "clientes_id" => $usuario->clientes_has_usuario->clientes_id,
                                                                                "conta_ativa" => 1,
                                                                                'return_url' => Router::url(
                                                                                    array(
                                                                                        'controller' => 'usuarios',
                                                                                        'action' => 'index'
                                                                                    )
                                                                                )
                                                                            )
                                                                        )
                                                                    ),
                                                                'escape' => false
                                                            ],
                                                            false
                                                        );
                                                    }
                                                } else {

                                                    if ($usuario["conta_ativa"] == true) {
                                                        echo $this->Html->link(
                                                            __(
                                                                '{0} ',
                                                                $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                                            ),
                                                            '#',
                                                            [
                                                                'class' => 'btn btn-xs  btn-danger btn-confirm',
                                                                'title' => 'Desativar',
                                                                'data-toggle' => 'modal',
                                                                'data-target' => '#modal-delete-with-message',
                                                                'data-message' => __(Configure::read('messageDisableAccessUserQuestion'), $usuario->nome),
                                                                'data-action' => Router::url(['action' => 'desabilitar_usuario', "?" =>
                                                                [
                                                                    'usuarios_id' => $usuario->id,
                                                                    'return_url' => Router::url([
                                                                        'controller' => 'usuarios',
                                                                        'action' => 'index'
                                                                    ])
                                                                ]]),
                                                                'escape' => false
                                                            ],
                                                            false
                                                        );
                                                    } else {
                                                        echo $this->Html->link(
                                                            __(
                                                                '{0}',
                                                                $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                                            ),
                                                            '#',
                                                            [
                                                                'class' => 'btn btn-xs  btn-primary btn-confirm',
                                                                'title' => 'Ativar',
                                                                'data-toggle' => 'modal',
                                                                'data-target' => '#modal-delete-with-message',
                                                                'data-message' => __(Configure::read('messageEnableAccessUserQuestion'), $usuario->nome),
                                                                'data-action' => Router::url(['action' => 'habilitar_usuario', "?" =>
                                                                [
                                                                    'usuarios_id' => $usuario->id,
                                                                    'return_url' => Router::url([
                                                                        'controller' => 'usuarios',
                                                                        'action' => 'index'
                                                                    ])
                                                                ]]),
                                                                'escape' => false
                                                            ],
                                                            false
                                                        );
                                                    }
                                                }
                                            } else {

                                                if ($usuario["conta_ativa"] == true) {
                                                    echo $this->Html->link(
                                                        __(
                                                            '{0} ',
                                                            $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                                        ),
                                                        '#',
                                                        [
                                                            'class' => 'btn btn-xs  btn-danger btn-confirm',
                                                            'title' => 'Desativar',
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#modal-delete-with-message',
                                                            'data-message' => __(Configure::read('messageDisableAccessUserQuestion'), $usuario->nome),
                                                            'data-action' => Router::url(['action' => 'desabilitar_usuario', "?" =>
                                                            [
                                                                'usuarios_id' => $usuario->id,
                                                                'return_url' => Router::url([
                                                                    'controller' => 'usuarios',
                                                                    'action' => 'index'
                                                                ])
                                                            ]]),
                                                            'escape' => false
                                                        ],
                                                        false
                                                    );
                                                } else {
                                                    echo $this->Html->link(
                                                        __(
                                                            '{0}',
                                                            $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                                        ),
                                                        '#',
                                                        [
                                                            'class' => 'btn btn-xs  btn-primary btn-confirm',
                                                            'title' => 'Ativar',
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#modal-delete-with-message',
                                                            'data-message' => __(Configure::read('messageEnableAccessUserQuestion'), $usuario->nome),
                                                            'data-action' => Router::url(['action' => 'habilitar_usuario', "?" =>
                                                            [
                                                                'usuarios_id' => $usuario->id,
                                                                'return_url' => Router::url([
                                                                    'controller' => 'usuarios',
                                                                    'action' => 'index'
                                                                ])
                                                            ]]),
                                                            'escape' => false
                                                        ],
                                                        false
                                                    );
                                                }
                                            }
                                        }
                                        ?>


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
                                                'data-target' => '#modal-delete-with-message',
                                                'data-message' => __(Configure::read('messageDeleteQuestion'), $usuario->nome),
                                                'data-action' => Router::url(
                                                    [
                                                        'action' => 'delete', $usuario->id,
                                                        '?' =>
                                                        [
                                                            'usuario_id' => $usuario->id,
                                                            'return_url' => Router::url(
                                                                [
                                                                    'controller' => 'usuarios',
                                                                    'action' => 'index'
                                                                ]
                                                            )
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
                                <p>
                                    <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?>
                                </p>
                            </center>
                        </div>
                    </div>

                    </div>
                </div>
            </div>
    </div>