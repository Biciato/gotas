<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesHabilitados/left_menu.ctp
 * @date     09/08/2017
 */

if (!isset($mode)) {
    $mode = 999;
}

$cliente_session = $this->request->session()->read('Network.Unit');

$show_reports_admin_rti = isset($show_reports_admin_rti) ? $show_reports_admin_rti : false;
$show_reports_admin = isset($show_reports_admin) ? $show_reports_admin : false;

?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active">
            <?= $this->Html->link(__('Menu'), []) ?>
        </li>

         <li class="active">
            <?= $this->Html->link(__('Ações'), []) ?>
        </li>
    <?php if ($mode == 'updateGiftPrice') : ?>

        <li>
            <?= $this->Html->link(__('Atualizar Preço de Brinde'), ['controller' => 'ClientesHasBrindesHabilitadosPreco', 'action' => 'novo_preco_brinde', $brinde->id]) ?>
        </li>
        <li>
            <?= $this->Html->link(__('Configurar Tipo de Emissão'), ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'configurar_tipo_emissao', $brinde->id]) ?>
        </li>
        <?php if (!$brinde->brinde->ilimitado) : ?>
        <li>
            <?= $this->Html->link(__("Gerenciar Estoque do Brinde"), ['controller' => 'ClientesHasBrindesEstoque', 'action' => 'gerenciar_estoque', $brinde->id]); ?>
        </li>
        <?php endif; ?>
        <?php if (!$brinde->brinde->equipamento_rti_shower) : ?>
        <li>
            <?= $this->Html->link(__("Emissão Manual"), ['controller' => 'ClientesHasBrindesEstoque', 'action' => 'venda_manual_estoque', $brinde->id]); ?>
        </li>
        <?php endif; ?>



    <?php elseif ($mode == 'activatedGifts') : ?>


        <?php if (!isset($cliente_session->matriz_id)) : ?>
        <li>
            <?= $this->Html->link(__("Ativar Brinde em Unidade"), ['controller' => 'clientesHasBrindesHabilitados', 'action' => 'ativar_brindes']) ?>
        </li>
        <?php endif; ?>


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
