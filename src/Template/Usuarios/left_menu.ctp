<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/left_menu.ctp
 * @date     27/08/2017
 */


use Cake\Core\Configure;
use Cake\Routing\Router;

$cadastrar_veiculos = isset($cadastrar_veiculos) ? $cadastrar_veiculos : false;
$update_password = isset($update_password) ? $update_password : false;
$mode = isset($mode) ? $mode : false;
$listUsersPendingApproval = isset($listUsersPendingApproval) ? $listUsersPendingApproval : false;
$show_reports_admin_rti = isset($show_reports_admin_rti) ? $show_reports_admin_rti : false;
$redes_id = isset($redes_id) ? $redes_id : null;

?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active">
            <?= $this->Html->link(__('Menu'), []) ?>
        </li>
        <?php if ($cadastrar_veiculos) : ?>

            <li>
                <?= $this->Html->link(__("Transportadoras do Usuário"), ['controller' => 'transportadoras', 'action' => 'transportadorasUsuario', $usuario->id]) ?>
            </li>
            <li>
                <?= $this->Html->link(__("Veículos do Usuário"), ['controller' => 'veiculos', 'action' => 'veiculosUsuario', $usuario->id]) ?>
            </li>
        <?php endif; ?>

        <li class="active">
            <?= $this->Html->link(__('Ações'), []) ?>
        </li>

        <?php if ($mode == 'updatePasswordOnly') : ?>
            <li>
                <?= $this->Html->link(
                    __("Alterar senha"),
                    [
                        'controller' => 'usuarios',
                        'action' => 'alterar_senha',
                        $usuario->id
                    ]
                ) ?>
            </li>

        <?php elseif ($mode == 'management_admin') : ?>

            <li>
                <?= $this->Html->link(__('Novo Usuário'), ['action' => 'registrar']) ?>
            </li>

            <li>
                <?= $this->Html->link(__('Novo Operador/Funcionário'), ['action' => 'adicionar_operador', $redes_id]) ?>
            </li>

            <?php if (isset($redes_id)) : ?>

            <li>
                <?= $this->Html->link(__("Administradores Regionais e Comuns"), ['controller' => 'usuarios', 'action' => 'administradores_regionais_comuns', $redes_id]) ?>
            </li>

            <?php endif; ?>

        <?php elseif ($mode == 'management') : ?>

            <li>
                <?= $this->Html->link(__('Novo Funcionário'), ['action' => 'adicionar_operador', $redes_id]) ?>
            </li>

            <?php if ($usuarioLogado['tipo_perfil'] <= (int)Configure::read('profileTypes')['AdminNetworkProfileType']) : ?>
                <?php if (isset($redes_id)) : ?>

                <li>
                    <?= $this->Html->link(__("Atribuir Administração Regional/Comum"), ['controller' => 'usuarios', 'action' => 'atribuir_admin_regional_comum', $redes_id]) ?>
                </li>

                <?php endif; ?>
            <?php endif; ?>

        <?php endif; ?>

        <?php if ($update_password) : ?>

            <li>
                <?= $this->Html->link(
                    __("Alterar senha"),
                    [
                        'controller' => 'usuarios',
                        'action' => 'alterar_senha',
                        $usuario->id
                    ]
                ) ?>
            </li>

        <?php endif; ?>

        <?php if ($listUsersPendingApproval) : ?>
            <li><?= $this->Html->link(__('Usuários Aguardando Aprovação'), ['action' => 'usuarios_aguardando_aprovacao']) ?></li>
        <?php endif; ?>

        <?php if ($usuarioLogado["tipo_perfil"] < Configure::read("profileTypes")["UserProfileType"]): ?>
            <li class="active">
                <?= $this->Html->link(__('Relatórios'), []) ?>
            </li>
        <?php endif; ?>
        <?php if ($show_reports_admin_rti) : ?>

            <li><?= $this->Html->link(__("Usuários Cadastrados"), ['controller' => 'Usuarios', 'action' => 'relatorio_usuarios_cadastrados']) ?> </li>
            <li><?= $this->Html->link(__("Usuários Por Redes"), ['controller' => 'Usuarios', 'action' => 'relatorio_usuarios_redes']) ?> </li>
            <li><?= $this->Html->link(__("Brindes Adquiridos pelos Usuários "), ['controller' => 'UsuariosHasBrindes', 'action' => 'relatorio_brindes_usuarios_redes']) ?> </li>
        <?php endif; ?>
    </ul>
</nav>
