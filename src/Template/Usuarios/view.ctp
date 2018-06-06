<?php

/**
 * @description Ver detalhes de Usuário
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/view.ctp
 * @date        28/08/2017
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

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

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'index']);
    
} else if ($user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminDeveloperProfileType']
&& $user_logged['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'usuarios_rede', $rede->id]);
}

$this->Breadcrumbs->add('Detalhes de Usuário', [], ['class' =>'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element(
    '../Usuarios/left_menu',
    [
        'controller' => $controller,
        'action' => $action,
        'update_password' => true,
        'mode' => 'view',
        'usuario' => $usuario,
        'cadastrar_veiculos' => true
    ]
) ?>
<div class="usuarios view col-lg-9 col-md-10">
    <legend><?= h($usuario->nome) ?></legend>

    <?= $this->element('../Usuarios/tabela_info_usuarios', ['usuario' => $usuario]); ?>
</div>
