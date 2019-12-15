<?php

/**
 * @description Arquivo para formulário de cadastro de usuários (Administradores, Gerentes, Funcionarios )
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/adicionar_operador.ctp
 * @date        28/08/2017
 *
 */
use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'index']);
    $this->Breadcrumbs->add('Adicionar Conta', [], ['class' => 'active']);

} else if ($usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminDeveloperProfileType']
    && $usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']) {
    $this->Breadcrumbs->add('Usuários da rede', ['controller' => 'usuarios', 'action' => 'usuarios_rede']);
    $this->Breadcrumbs->add('Adicionar Conta', [], ['class' => 'active']);
}

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Usuarios/left_menu', ['controller' => 'usuarios', 'mode' => 'back']) ?>

<div class="usuarios form col-lg-9 col-md-8 columns content">
   <?= $this->element('../Usuarios/usuario_operador_form', ['title' => 'Adicionar', 'mode' => 'add']) ?>
</div>



<?php
$extension = Configure::read("debug") ? ""  : ".min";
// $this->fetch('script');
?>

<script src="/webroot/js/scripts/usuarios/add<?= $extension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/css/styles/usuarios/usuario_form<?= $extension ?>.css?<?php SYSTEM_VERSION ?>">
