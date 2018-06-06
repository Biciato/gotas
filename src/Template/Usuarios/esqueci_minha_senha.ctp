<?php
/**
 * @description Arquivo para formulário de cadastro de usuários
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/esqueci_minha_senha.ctp
 * @date        28/08/2017
 * 
 */

?>

<?php $this->assign('title', 'Solicitar reset da senha'); ?>
<div class="users content container">
	<h3><?php echo __('Esqueci minha senha') ?></h3>
	<?php
    	echo $this->Form->create();
        echo $this->Form->input('email', ['autofocus' => true, 'label' => 'Endereço de Email', 'required' => true]);
		echo $this->Form->button('Solicitar reset de senha');
    	echo $this->Form->end();
	?>
</div>