<?php
use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

//$this->Breadcrumbs->add('Usuários', ['controller' => 'pages' , 'action' => 'display']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

$user_is_admin = $usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType'];

$list_users_pending_approval = $user_is_admin;
?>



<?= $this->element(
    '../Usuarios/left_menu',
    [
        'add_user' => true,
        'list_users_pending_approval' => $user_is_admin,
        'mode' => 'management_admin',
        'usuarioLogado' => $usuarioLogado
    ]
) ?>
    <div class="usuarios index col-lg-9 col-md-10 columns content">
        <legend>
            <?= __("Usuários") ?>
        </legend>

        <?php // echo $this->element('../Usuarios/filtro_usuarios', ['controller' => 'usuarios', 'action' => 'index', 'show_filiais' => false, 'filter_redes' => true]) ?>
        <?= $this->element('../Usuarios/filtro_usuarios', ['controller' => 'usuarios', 'action' => 'index', 'show_filiais' => false, 'filter_redes' => false]) ?>
            <table class="table table-striped table-hover table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>
                            <?= $this->Paginator->sort('cliente_has_usuario.tipo_perfil', ['label' => 'Tipo de Perfil']) ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('nome') ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('cpf', ['label' => 'CPF']) ?>
                        </th>
                        <!-- <th>
                            <?= $this->Paginator->sort('ClienteHasUsuario.Cliente.RedesHasCliente.Rede.nome_rede', ['label' => 'Rede']) ?>
                        </th> -->
                        <th>
                            <?= $this->Paginator->sort('doc_estrangeiro', ['label' => 'Doc. Estrangeiro']) ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('sexo') ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('data_nasc', ['label' => 'Data de Nascimento']) ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('email', ['label' => 'E-mail']) ?>
                        </th>
                        <th class="actions">
                            <?= __('Ações') ?>
                            <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario) : ?>

                    <?php
                    if (isset($usuario['usuario'])) {
                        $usuario = $usuario['usuario'];
                    }
                    ?>
                    <tr>
                        <td>
                            <?= h($this->UserUtil->getProfileType($usuario["cliente_has_usuario"]["tipo_perfil"])) ?>
                        </td>
                        <td>
                            <?= h($usuario->nome) ?>
                        </td>
                        <td>
                            <?= h($this->NumberFormat->formatNumberToCpf($usuario->cpf)) ?>
                        </td>
                        <!-- <td>
                            <?= h($usuario["cliente_has_usuario"]["cliente"]["redes_has_cliente"]["rede"]["nome_rede"]) ?>
                        </td> -->
                        <td>
                            <?= h($usuario->doc_estrangeiro) ?>
                        </td>
                        <td>
                            <?= h($this->UserUtil->getGenderType($usuario->sexo)) ?>
                        </td>
                        <td>
                            <?= h($this->DateUtil->dateToFormat($usuario->data_nasc, 'd/m/Y')) ?>
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
                                    'class' => 'btn btn-xs btn-default ',
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
                                        'action' => 'editar',
                                        $usuario->id
                                    ],
                                    [
                                        'class' => 'btn btn-xs btn-primary ',
                                        'escape' => false,
                                        'title' => 'Editar'
                                    ]
                                )
                                ?>

                                    <?php if ($usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminNetworkProfileType']) {
                                        if ($usuario["cliente_has_usuario"]["tipo_perfil"] < Configure::read("profileTypes")["UserProfileType"]) {

                                            if ($usuario["cliente_has_usuario"]['conta_ativa'] == true) {
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
                                                        'data-action' => Router::url(
                                                            array(

                                                                "controller" => "clientes_has_usuarios", 'action' => 'alteraContaAtivaUsuario', "?" =>
                                                                    array(
                                                                    "id" => $usuario["cliente_has_usuario"]["id"],
                                                                    "usuarios_id" => $usuario->id,
                                                                    "clientes_id" => $usuario["cliente_has_usuario"]["clientes_id"],
                                                                    "conta_ativa" => 0,
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
                                                        'data-action' => Router::url(
                                                            array(

                                                                "controller" => "clientes_has_usuarios", 'action' => 'alteraContaAtivaUsuario', "?" =>
                                                                    array(
                                                                    "id" => $usuario["cliente_has_usuario"]["id"],
                                                                    'usuarios_id' => $usuario->id,
                                                                    "clientes_id" => $usuario["cliente_has_usuario"]["clientes_id"],
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
