<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/left_menu.ctp
 * @date     09/08/2017
 */


$mode = isset($mode) ? $mode : 'view';

// @todo gustavosg ver quem ta usando essa parada
$clientesId = isset($clientesId) ? $clientesId : null;

$show_reports_admin_rti = isset($show_reports_admin_rti) ? $show_reports_admin_rti : false;
$show_reports_admin = isset($show_reports_admin) ? $show_reports_admin : false;

$manageStock = !empty($manageStock) ? $manageStock : false;
$managePrice = !empty($managePrice) ? $managePrice : false;

$textoBrinde = $usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER ? "IHM" : "Brinde";
?>

<nav class="col-lg-3 col-md-4" id="actions-sidebar">
	<ul class="nav nav-pills nav-stacked">
		<li class="active"><a><?= __('Menu') ?></a></li>

		<?php if ($mode == 'add' || $mode == 'edit') : ?>

		<li><?= $this->Html->link(__('Novo {0}', [$textoBrinde]), ['action' => 'adicionar', $clientesId]) ?></li>

        <?php endif; ?>

        <?php if ($manageStock): ?>

		<li>
            <a href="<?php echo sprintf("/brindesEstoque/adicionarEstoque/%s", $brinde['id'])?>">
                <i class="fa fa-shopping-cart"></i>
                Adicionar Estoque
            </a>
		</li>

        <?php endif;?>
        <?php if ($managePrice): ?>

		<li>
            <a href="<?php echo sprintf("/brindesPrecos/atualizarPreco/%s", $brinde['id'])?>">
                <i class="fa fa-money"></i>
                Atualizar Preço
            </a>
		</li>

        <?php endif;?>

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

