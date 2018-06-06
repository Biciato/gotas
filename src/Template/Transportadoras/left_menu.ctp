<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Transportadoras/left_menu.ctp
 * @date     07/11/2017
 */

$mode = isset($mode) ? $mode : false;
$show_reports_admin_rti = isset($show_reports_admin_rti) ? $show_reports_admin_rti : false;

?>
    <nav class="col-lg-3 col-md-2 columns" id="actions-sidebar">
        <ul class="nav nav-pills nav-stacked">
            <li class="active">
                <?= $this->Html->link(__('Menu'), []) ?>
            </li>

            <li class="active">
                <?= $this->Html->link(__("Ações"), []) ?>
            </li>

            <?php if ($mode == 'view') : ?>
                <li>
                    <?= $this->Html->link(__('Nova Transportadora'), ['action' => 'add']) ?>
                </li>
            <?php elseif ($mode == 'edit') : ?>
                <li>
                    <?= $this->Html->link(__('Editar Transportadora'), ['action' => 'edit', $transportadora->id]) ?> 
                </li>
            <?php endif; ?>

             <li class="active">
                <?= $this->Html->link(__("Relatórios"), []) ?>
            </li>

            <?php if ($show_reports_admin_rti) : ?> 
            <li>
                <?= $this->Html->link(__("Transportadoras Cadastradas de Clientes das Redes"), ['controller' => 'Transportadoras', 'action' => 'relatorio_transportadoras_usuarios_redes']) ?>
             </li>

             <?php endif; ?> 
    </ul>
</nav>