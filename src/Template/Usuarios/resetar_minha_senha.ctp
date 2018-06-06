<?php
/**
 * @description Arquivo para formulário de cadastro de usuários
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/resetar_minha_senha.ctp
 * @date        28/08/2017
 * 
 */
?>
<?php $this->assign('title', 'Resetar senha'); ?>
<div class="users content container">
    <?php echo $this->Form->create($usuario) ?>
    <fieldset>
        <legend><?php echo __('Resetar senha') ?>
    <?= $this->Form->input('senha', ['type' => 'password', 'required' => true, 'autofocus' => true]); ?>
      
    <?= $this->Form->input('confirm_senha', ['type' => 'password', 'required' => true]); ?>
    </fieldset>
 	<?php echo $this->Form->button(__('Resetar')); ?>
    <?php echo $this->Form->end(); ?>
</div>