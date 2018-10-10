<?php
use Cake\Core\Configure;
use Cake\Routing\Router;

$redes_id = isset($redes_id) ? $redes_id : null;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
    $this->Breadcrumbs->add(
        'Detalhes da Rede',
        ['controller' => 'Redes', 'action' => 'ver_detalhes', $redes_id],
        ['class' => 'active']
    );
}

$this->Breadcrumbs->add('Usuários da Rede', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

$userIsAdmin = $user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']
    || Configure::read('profileTypes')['AdminNetworkProfileType']
    || Configure::read('profileTypes')['AdminRegionalProfileType'];
?>

<?= $this->element(
    '../Usuarios/left_menu',
    [
        'add_user' => true,
        'mode' => 'management',
        'controller' => 'pages',
        'action' => 'display',
        "redes_id" => $redes_id
    ]
) ?>

    <div class="usuarios index col-lg-9 col-md-10 columns content">
        <legend>
            <?= __("Usuários da Rede") ?>
        </legend>

        <?php if ($userIsAdmin) : ?>
            <?= $this->element('../Usuarios/filtro_usuarios_redes', ['controller' => 'usuarios', 'action' => 'usuarios_rede', 'id' => $redes_id, 'show_filiais' => false, 'filter_redes' => true, 'unidades_ids' => $unidades_ids]) ?>
        <?php else : ?>
            <?= $this->element('../Usuarios/filtro_usuarios', ['controller' => 'usuarios', 'action' => 'usuarios_rede', 'id' => $redes_id, 'show_filiais' => false, 'filter_redes' => true]) ?>
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
                            <?= $this->Paginator->sort('Clientes.razao_social', ['label' => 'Unidade']) ?>
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
                            <?= h($this->UserUtil->getProfileType($usuario->tipo_perfil)) ?>
                        </td>
                        <td>
                            <?= h($usuario->nome) ?>
                        </td>
                        <td>
                            <?= h($this->NumberFormat->formatNumberToCpf($usuario->cpf)) ?>
                        </td>
                        <td>
                            <?=
                            isset($usuario->clientes_has_usuarios[0]) ?
                                h($usuario->clientes_has_usuarios[0]->cliente->razao_social)
                                : ""
                            ?>
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
                                    'class' => 'btn btn-xs btn-default ',
                                    'escape' => false,
                                    'title' => 'Ver detalhes'

                                ]
                            )
                            ?>
                            <?php

                            // se é administrador da rede ou é regional e o tipo de perfil tem maior permissão
                            if (($user_logged['tipo_perfil'] <= Configure::read('profileTypes')['AdminNetworkProfileType']) || ($user_logged['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType'] || $user_logged['tipo_perfil'] < $usuario->tipo_perfil)) {

                                echo $this->Html->link(
                                    __('{0}', $this->Html->tag('i', '', ['class' => 'fa fa-edit'])),
                                    [
                                        'action' => 'editar_operador',
                                        $usuario->id
                                    ],
                                    [
                                        'class' => 'btn btn-xs btn-primary ',
                                        'escape' => false,
                                        'title' => 'Editar'
                                    ]
                                );

                                // só permite remover e desabilitar se o id do usuário logado não é o mesmo da tabela

                                if ($usuario['id'] != $user_logged['id']) {

                                    if ($usuario['conta_ativa'] == true) {
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
                                                    ['action' => 'desabilitar_usuario', "?" =>
                                                        [
                                                        'usuarios_id' => $usuario->id,
                                                        'return_url' => Router::url(
                                                            [
                                                                'controller' => 'usuarios',
                                                                'action' => 'usuarios_rede', $redes_id
                                                            ]
                                                        )
                                                    ]]
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
                                                'data-action' => Router::url(['action' => 'habilitar_usuario', "?" =>
                                                    [
                                                    'usuarios_id' => $usuario->id,
                                                    'return_url' => Router::url([
                                                        'controller' => 'usuarios',
                                                        'action' => 'usuarios_rede', $redes_id
                                                    ])
                                                ]]),
                                                'escape' => false
                                            ],
                                            false
                                        );

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
                                                                'action' => 'usuarios_rede', $redes_id
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
