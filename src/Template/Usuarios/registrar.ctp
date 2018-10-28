<?php

/**
 * @description Arquivo para formulário de cadastro de usuários (cliente final)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/add.ctp
 * @date        28/08/2017
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType'])
{
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'index']);
}

$this->Breadcrumbs->add('Registrar', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>
<?= $this->Form->create($usuario, ['url' => ['controller' => 'usuarios', 'action' => 'registrar']]); ?>

<?php if (isset($usuarioLogado)) : ?>

<?= $this->element('../Usuarios/left_menu', ['controller' => 'pages', 'action' => 'display', 'mode' => 'back', 'show_menu' => true]) ?>
<div class="usuarios view col-lg-9 col-md-10">
    <?= $this->Element('../Usuarios/usuario_form', ['show_menu' => false, 'show_tipo_perfil' => false]) ?>
</div>
<?php else : ?>

<div class="col-lg-12">
    <?= $this->Element('../Usuarios/usuario_form') ?>
</div>
    
<?php endif; ?>



<?= $this->Form->end() ?>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/usuarios/add'); ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/usuarios/add.min'); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>
