<?php
/**
 * @description Editar detalhes de Usuário (view de administrador)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/editar_operador.ctp
 * @date        28/08/2017
 *
 */

use Cake\Core\Configure;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
    $this->Breadcrumbs->add(
        'Detalhes da Rede',
        ['controller' => 'Redes', 'action' => 'ver_detalhes', $redesId],
        ['class' => 'active']
    );
}

$this->Breadcrumbs->add('Usuários da Rede', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../Usuarios/left_menu', ['controller' => 'usuarios', 'action' => 'meus_clientes', 'mode' => 'back', 'update_password' => true]) ?>
<div class="usuarios view col-lg-9 col-md-10">
    <?= $this->Form->create($usuario) ?>
        <?= $this->element('../Usuarios/usuario_operador_form', ['title' => 'Editar', 'mode' => 'edit']) ?>

    <?= $this->Form->end() ?>
</div>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/usuarios/edit'); ?>
<?php else: ?>
    <?= $this->Html->script('scripts/usuarios/edit.min'); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>
