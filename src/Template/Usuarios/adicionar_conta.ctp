<?php

/**
 * @description Arquivo para formulário de cadastro de usuários (Dashboard de  Funcionarios)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/adicionar_conta.ctp
 * @date        28/08/2017
 *
 */
use Cake\Core\Configure;
use Cake\Routing\Router;

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Adicionar Conta', [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'cadastrar_cliente']) ?>

<div class="usuarios view col-lg-9 col-md-10">

     <?= $this->Form->create($usuario, ['url' => ['controller' => 'usuarios', 'action' => 'adicionar_conta'], ['id' => 'usuario_form']]); ?>

        <?= $this->element('../Usuarios/usuario_form', ['show_menu' => false]) ?>

    <?= $this->Form->end() ?>
</div>



<?php if (Configure::read('debug') == true) : ?>
    <?php // echo $this->Html->script('scripts/usuarios/add'); ?>
<?php else : ?>
    <?php //echo $this->Html->script('scripts/usuarios/add.min'); ?>
<?php endif; ?>

<?php //echo $this->fetch('script'); ?>
