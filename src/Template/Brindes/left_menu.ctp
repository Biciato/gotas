<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/left_menu.ctp
 * @date     09/08/2017
 */


$mode = isset($mode) ? $mode : 'view';

$clientes_id = isset($clientes_id) ? $clientes_id : null;

$show_reports_admin_rti = isset($show_reports_admin_rti) ? $show_reports_admin_rti : false;
$show_reports_admin = isset($show_reports_admin) ? $show_reports_admin : false;


?>

<nav class="col-lg-3 col-md-4" id="actions-sidebar">
	<ul class="nav nav-pills nav-stacked">
		<li class="active"><a><?= __('Menu') ?></a></li>

		<?php if ($mode == 'add' || $mode == 'edit') : ?>

		<li><?= $this->Html->link(__('Novo {0}', ['Brinde']), ['action' => 'adicionar_brinde_rede', $clientes_id]) ?></li>

		<?php endif; ?>

    <li class="active">
        <?= $this->Html->link(__('Relatórios'), []) ?>
    </li>

    <?= $this->element(
        '../Brindes/atalhos_relatorios_comuns_brindes',
        [
            'show_reports_admin_rti' => $show_reports_admin_rti,
            'show_reports_admin' => $show_reports_admin
        ]
    ) ?>
    </ul>
</nav>

