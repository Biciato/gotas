
<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Gotas/atalhos_relatorios_gotas.ctp
 * @date     07/03/2018
 */


$show_reports_admin_rti = isset($show_reports_admin_rti) ? $show_reports_admin_rti : false;

?>
<?php if ($show_reports_admin_rti) : ?>

    <li><?= $this->Html->link(__("Gotas por Redes"), ['controller' => 'gotas', 'action' => 'relatorio_gotas_redes']) ?></li>
    <li><?= $this->Html->link(__("Consumo de Gotas por Usuários"), ['controller' => 'gotas', 'action' => 'relatorio_consumo_gotas_usuarios']) ?></li>

<?php else : ?>

<?php endif; ?>
