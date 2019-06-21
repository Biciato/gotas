<?php

/**
 * @description Editar detalhes de Usuário (view de administrador)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/editar_cadastro_usuario_final.ctp
 * @date        17/02/2018
 *
 */

use Cake\Core\Configure;

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'atualizar_cadastro_cliente']); ?> 
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
