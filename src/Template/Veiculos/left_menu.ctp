<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Veiculos/left_menu.ctp
 * @date     27/09/2017
 */

use Cake\Core\Configure;

$usuarios_id = isset($usuarios_id) ? $usuarios_id : null;

$show_reports_admin_rti = isset($show_reports_admin_rti) ? $show_reports_admin_rti : null;

?>
<nav class="col-lg-3 col-md-4" id="actions-sidebar">
	<ul class="nav nav-pills nav-stacked">
		<li class="active">
				<?= $this->Html->link(__('Menu'), []) ?>
		</li>

		<li class="active">
			<?= $this->Html->Link(__('Ações'), []) ?>
		</li>

		<?php if ((isset($mode)) && ($mode == 'view')) : ?>
		<li>
			<?= $this->Html->link(
			__('Cadastrar Veiculo'),
			[
				'action' => 'adicionar_veiculo', $usuarios_id
			]
		) ?>
		</li>
		<?php endif; ?>

		<?php if ($user_logged['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']): ?>

		<li class="active">
			<?= $this->Html->Link(__('Relatórios'), []) ?>
		</li>

		<?php endif; ?>

		<?php if ($show_reports_admin_rti) : ?>

			<li>
				<?= $this->Html->link(__("Veiculos Cadastrados de Clientes das Redes"), ['controller' => 'Veiculos', 'action' => 'relatorio_veiculos_usuarios_redes']) ?>
			</li>
		<?php endif; ?>
	</ul>
</nav>
