<?php

use Cake\Routing\Router;

$filial_administrar= $this->request->session()->read('ClientToManage');

$user_admin = $this->request->session()->read('User.RootLogged');
$user_managed = $this->request->session()->read('User.ToManage');

?>

<?php if (isset($filial_administrar) && isset($user_logged)): ?>
<div class="header">

<div id="header-management">
	<div class="branch-management">

		
		<?= $this->Html->tag('span', __("Usuário [{0}] administrando unidade [{1} / {2}]. Clique no botão para encerrar:", $user_logged['nome'], $filial_administrar['razao_social'], $filial_administrar['nome_fantasia'] ))?>
		
		<?= $this->Html->link(
			__('{0} Encerrar gerenciamento',
			$this->Html->tag('i', '', ['class' => 'fa fa-sign-out'])), 
			'#',
			array(
				'class'=>'btn btn-primary btn-quit-manage-unit',
				'data-toggle'=> 'modal',
				'data-target' => '#modal-quit-manage-unit',
				'data-action'=> Router::url(
					['controller' => 'clientes', 'action' => 'encerrar_administracao_unidades']
				),
				'escape' => false),
		false); ?>
	</div>
	
</div>

</div>
<?php endif; ?>


<?php if (isset($user_managed) && isset($user_admin)): ?>
<div class="header">

<div id="header-management">
	<div class="user-management">

		
		<?= $this->Html->tag('span', __("Administrador [{0}] administrando usuário {1}. Clique no botão para encerrar:", $user_admin['nome'], $user_managed['nome']))?>
		
		<?= $this->Html->link(
			__('{0} Encerrar gerenciamento',
			$this->Html->tag('i', '', ['class' => 'fa fa-sign-out'])), 
			'#',
			array(
				'class'=>'btn btn-primary btn-quit-manage-unit',
				'data-toggle'=> 'modal',
				'data-target' => '#modal-confirm-with-message',
				'data-message' => __('Deseja encerrar o gerenciamento do usuário {0} ?', $user_managed['nome']),
				'data-action'=> Router::url(
					['controller' => 'usuarios', 'action' => 'finalizar_administracao_usuario']
				),
				'escape' => false),
		false); ?>
	</div>
	
</div>

</div>
<?php endif; ?>


