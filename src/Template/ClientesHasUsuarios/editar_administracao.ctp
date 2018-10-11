<?php

/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;
use Cake\Routing\Router;


$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'index']);

    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);

    $this->Breadcrumbs->add('Detalhes da Rede', ['controller' => 'redes', 'action' => 'ver_detalhes', $rede->id]);

} else if ($user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType']
    && $user_logged['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'usuarios_rede', $rede->id]);
}

$this->Breadcrumbs->add('Atribuir Administração Regional/Comum', ['controller' => 'usuarios', 'action' => 'atribuir_admin_regional_comum', $rede->id]);

$this->Breadcrumbs->add('Unidades do Administrador', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

$update_password = false;

if ($user_logged['tipo_perfil'] == 0) {
    $controller = 'usuarios';
    $action = 'index';
} else if ($user_logged['tipo_perfil'] >= 1 && $user_logged['tipo_perfil'] <= 3) {
    $controller = 'usuarios';
    $action = 'minha_equipe';
} else {
    $controller = 'pages';
    $action = 'display';
}

?>
<?= $this->element(
    '../Usuarios/left_menu',
    [
        'controller' => $controller,
        'action' => $action,
        'mode' => 'back'

    ]
) ?>
<div class="usuarios view col-lg-9 col-md-10">
    <?= $this->Form->create($usuario) ?>
        <legend><?= __('Unidades que o Administrador {0} Gerencia', $usuario->nome) ?></legend>

        <table class="table table-striped table-hover table-condensed table-responsive">
            <thead>

                <tr>
                    <th scope="col">
                        <?= $this->Paginator->sort('razao_social') ?>
                    </th>
                    <th scope="col">
                        <?= $this->Paginator->sort('nome_fantasia') ?>
                    </th>
                    <th scope="col">
                        <?= __("Status") ?>
                    </th>
                    <th scope="col">
                        <?= "Ações" ?>
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
                <?php foreach ($clientes as $key => $cliente) : ?>
                <tr>
                    <td>
                        <?= h($cliente->razao_social) ?>
                    </td>
                    <td>
                        <?= h($cliente->nome_fantasia) ?>
                    </td>

                        <?php

                        $record_found = false;
                        $cliente_has_usuario_id = null;

                        // procura pelo registro
                        foreach ($cliente->clientes_has_usuarios as $key => $cliente_has_usuario) {
                            // se o registro existe, a opção será desvincular
                            if ($usuario->id == $cliente_has_usuario->usuarios_id
                                && $usuario->tipo_perfil == $cliente_has_usuario->tipo_perfil) {
                                $record_found = true;
                                $cliente_has_usuario_id = $cliente_has_usuario->id;
                            }
                        }

                        if ($record_found) {
                            ?>

                            <td>
                                <?= __("Habilitado") ?>
                            </td>
                            <td>

                            <?php
                            echo $this->Html->link(
                                $this->Html->tag('i', '', array('class' => 'fa fa-power-off')),
                                '#',
                                array(
                                    'class' => 'btn btn-danger btn-confirm btn-xs',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-confirm-with-message',
                                    'data-message' => __(Configure::read('messageDisableAccessUserQuestion'), $usuario->nome),
                                    'data-action' => Router::url(
                                        [
                                            'controller' => 'clientes_has_usuarios',
                                            'action' => 'desatribuir_administracao',
                                            "?" =>
                                                [
                                                'id' => $cliente_has_usuario_id,
                                                'usuarios_id' => $usuario->id,
                                                'clientes_id' => $cliente->id
                                            ]
                                        ]
                                    ),
                                    'title' => 'Desatribuir',
                                    'escape' => false
                                ),
                                false
                            );
                        } else {
                            ?>

                            <td>
                                <?= __("Desabilitado") ?>
                            </td>

                            <td>
                            <?php
                            echo $this->Html->link(
                                $this->Html->tag('i', '', array('class' => 'fa fa-power-off')),
                                '#',
                                array(
                                    'class' => 'btn btn-primary btn-confirm btn-xs',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-confirm-with-message',
                                    'data-message' => __(Configure::read('messageEnableAccessUserQuestion'), $usuario->nome),
                                    'data-action' => Router::url(
                                        [
                                            'controller' => 'clientes_has_usuarios',
                                            'action' => 'atribuir_administracao',
                                            "?" =>
                                                [
                                                'clientes_id' => $cliente->id,
                                                'usuarios_id' => $usuario->id,
                                                'tipo_perfil' => $usuario->tipo_perfil
                                            ]
                                        ]
                                    ),
                                    'title' => 'Atribuir',
                                    'escape' => false
                                ),
                                false
                            );
                        }

                        ?>
                    </td>


                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
</div>
