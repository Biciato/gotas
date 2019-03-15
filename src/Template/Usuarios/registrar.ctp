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

if (!empty($usuarioLogado) && $usuarioLogado['tipo_perfil'] == PROFILE_TYPE_ADMIN_DEVELOPER)
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
    <?= $this->Form->create($usuario); ?>
    <?= $this->Element('../Usuarios/usuario_form') ?>
    <?= $this->Form->end(); ?>
</div>

<?php endif; ?>



<?= $this->Form->end() ?>

