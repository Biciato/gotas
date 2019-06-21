
<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/atalhos_relatorios_comuns_brindes.ctp
 * @date     07/03/2018
 */

?>
<?php if ($show_reports_admin_rti) : ?>
<li><?= $this->Html->link(__("Brindes Cadastrados por Rede"), ['controller' => 'Brindes', 'action' => 'relatorio_brindes_redes']) ?> </li>

<li><?= $this->Html->link(__("Brindes Habilitados de Unidades por Rede"), ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'relatorio_brindes_habilitados_redes']) ?> </li>

<li><?= $this->Html->link(__("Estoque de Brindes por Unidade de Rede"), ['controller' => 'ClientesHasBrindesEstoque', 'action' => 'relatorio_estoque_brindes_redes']) ?> </li>
<li><?= $this->Html->link(__("Histórico de Preços de Brinde "), ['controller' => 'ClientesHasBrindesHabilitadosPreco', 'action' => 'relatorio_historico_preco_brindes_redes']) ?> </li>

<?php else : ?>

<?php endif; ?>
