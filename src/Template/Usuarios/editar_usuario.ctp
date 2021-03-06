<?php

/**
 * @description Editar detalhes de Usuário (view de administrador)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/editar_usuario.ctp
 * @date        28/08/2017
 *
 */

use Cake\Core\Configure;

?>

<?= $this->element('../Usuarios/left_menu', ['controller' => 'usuarios', 'action' => 'meus_clientes', 'mode' => 'back', 'update_password' => true]) ?>
<div class="usuarios view col-lg-9 col-md-10">
    <?= $this->Form->create($usuario) ?>
        
    <?= $this->element('../Usuarios/form_editar_cliente') ?>
    
    <?= $this->Form->end() ?>
</div>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/usuarios/edit'); ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/usuarios/edit.min'); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>
