<?php
use Cake\Core\Configure;
use Cake\Routing\Router;
use App\Custom\RTI\DebugUtil;

$redesId = isset($redesId) ? $redesId : null;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
    $this->Breadcrumbs->add(
        'Detalhes da Rede',
        ['controller' => 'Redes', 'action' => 'ver_detalhes', $redesId],
        ['class' => 'active']
    );
}

$title = "";

if ($usuarioLogado["tipo_perfil"] <= Configure::read("profileTypes")["AdminRegionalProfileType"]) {
    $title = "Usuários da Rede";
} else {
    $title = "Usuários da Loja/Posto";
}

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

$userIsAdmin = in_array($usuarioLogado['tipo_perfil'], array(PROFILE_TYPE_ADMIN_DEVELOPER, PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL));
?>

<?= $this->element(
    '../Usuarios/left_menu',
    [
        'add_user' => true,
        'mode' => 'management',
        'controller' => 'pages',
        'action' => 'display',
        "redes_id" => $redesId,
        "usuarioLogado" => $usuarioLogado
    ]
) ?>

    <div class="usuarios index col-lg-9 col-md-10 columns content">
        <legend>
            <?= __($title) ?>
        </legend>

        <?php if ($userIsAdmin) : ?>
            <?= $this->element(
                '../Usuarios/filtro_usuarios_redes',
                array('controller' => 'usuarios', 'action' => 'usuarios_rede', 'id' => $redesId, 'show_filiais' => false, 'filter_redes' => true, 'unidades_ids' => $unidadesIds, "unidadesId" => $unidadesId)
            ) ?>
        <?php else : ?>
            <?= $this->element('../Usuarios/filtro_usuarios', ['controller' => 'usuarios', 'action' => 'usuarios_rede', 'id' => $redesId, 'show_filiais' => false, 'filter_redes' => true]) ?>
        <?php endif; ?>
            <table class="table table-striped table-hover table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>
                            <?= $this->Paginator->sort('tipo_perfil', ['label' => 'Tipo de Perfil']) ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('nome') ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('cpf', ['label' => 'CPF']) ?>
                        </th>

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
                            <?= h($this->UserUtil->getProfileType($usuario["tipo_perfil"])) ?>
                        </td>
                        <td>
                            <?= h($usuario->nome) ?>
                        </td>
                        <td>
                            <?= h($this->NumberFormat->formatNumberToCpf($usuario->cpf)) ?>
                        </td>
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
                                    'class' => 'btn btn-xs btn-default botao-navegacao-tabela',
                                    'escape' => false,
                                    'title' => 'Ver detalhes'

                                ]
                            )
                            ?>
                            <?php

                            // se é administrador da rede ou é regional e o tipo de perfil tem maior permissão
                            if (($usuarioLogado['tipo_perfil'] <= PROFILE_TYPE_ADMIN_NETWORK)
                                || ($usuarioLogado['tipo_perfil'] <= PROFILE_TYPE_ADMIN_REGIONAL
                                && $usuarioLogado['tipo_perfil'] < $usuario->tipo_perfil)) {

                                echo $this->Html->link(
                                    __('{0}', $this->Html->tag('i', '', ['class' => 'fa fa-edit'])),
                                    [
                                        'action' => 'editar_operador',
                                        $usuario["id"]
                                    ],
                                    [
                                        'class' => 'btn btn-xs btn-primary botao-navegacao-tabela ',
                                        'escape' => false,
                                        'title' => 'Editar'
                                    ]
                                );

                                // só permite remover e desabilitar se o id do usuário logado não é o mesmo da tabela

                                if ($usuario['id'] != $usuarioLogado['id']) {
                                    if ($usuario["tipo_perfil"] < PROFILE_TYPE_USER) {

                                        if ($usuario["cliente_has_usuario"]['conta_ativa'] == true) {
                                            echo $this->Html->link(
                                                __(
                                                    '{0} ',
                                                    $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                                ),
                                                '#',
                                                [
                                                    'class' => 'btn btn-xs btn-danger btn-confirm',
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
                                                                        'action' => 'usuarios_rede', $redesId
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
                                                                        'action' => 'usuarios_rede', $redesId
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
                                                            'action' => 'usuarios_rede', $redesId
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
                                                            'action' => 'usuarios_rede', $redesId
                                                        ])
                                                    ]]),
                                                    'escape' => false
                                                ],
                                                false
                                            );
                                        }

                                    }


                                    echo $this->Html->link(
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
                                                                'action' => 'usuarios_rede', $redesId
                                                            ]
                                                        )
                                                    ]
                                                ]
                                            ),
                                            'escape' => false
                                        ],
                                        false
                                    );
                                }

                            }
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
