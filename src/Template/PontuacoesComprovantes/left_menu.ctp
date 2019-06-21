<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/PontuacoesComprovantes/left_menu.ctp
 * @date     27/09/2017
 */

$show_reports_admin_rti = isset($show_reports_admin_rti) ? $show_reports_admin_rti : false;
?>
<nav class="col-lg-3 col-md-2 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active">
            <?= $this->Html->link(__('Menu'), []) ?>
        </li>
        
        <li class="active">
            <?= $this->Html->link(__('Ações'), []) ?>
        </li>
        
        <li class="active">
            <?= $this->Html->link(__('Relatórios'), []) ?>
        </li>

        <?php if ($show_reports_admin_rti) : ?> 
        <li>
            <?= $this->Html->link(
                __("Pontuações por Rede/Unidades"),
                ['controller' => 'PontuacoesComprovantes', 'action' => 'relatorio_pontuacoes_comprovantes_redes']
            ) ?> 
        </li>
        <li>
            <?= $this->Html->link(
                __("Pontuações por Usuários de Redes"),
                ['controller' => 'PontuacoesComprovantes', 'action' => 'relatorio_pontuacoes_comprovantes_usuarios_redes']
            ) ?>
        </li>
        
        <?php endif; ?> 
    </ul>
</nav>
