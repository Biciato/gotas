<?php

/**
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Usuarios/login.ctp
  * @date     08/07/2017
  */

?>

<div class="users form container ">

<?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Por favor informe seu e-mail e senha') ?></legend>
        <?= $this->Form->input('email', ['placeholder' => 'Informe e-mail para logar', 'autofocus' => true]) ?>
        <?= $this->Form->label('senha') ?>
        <?= $this->Form->password('senha', ['placeholder' => 'Informe sua senha de acesso']) ?>
    </fieldset>


    <?= $this->Html->link(
		__("Esqueci minha senha"),
			[
            	'controller' => 'Usuarios' ,
                'action' => 'esqueci_minha_senha'
			]
		); ?>
	<br />

	<?php if (!is_null($recoverAccount) && $recoverAccount == 1) : ?>
		<?= $this->Html->link(
			__("Reativar conta"),
			[
				'controller' => 'Usuarios',
				'action' => 'reativar_conta',
				$email
			],
			[
				'class' => 'btn btn-primary'
			]
		); ?>
	<?php endif; ?>

                      <br />
<?= $this->Form->button(__('Login')); ?>

<?= $this->Form->end() ?>

<?php
    echo $this->Form->postLink(
        'Login with Facebook',
        [
            'prefix' => false,
            'plugin' => 'ADmad/SocialAuth',
            'controller' => 'Auth',

            'action' => 'login',
            'provider' => 'facebook',
            '?' => ['redirect' => $this->request->getQuery('redirect')]
        ]
    );
?>
</div>

