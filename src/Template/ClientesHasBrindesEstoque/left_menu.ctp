<?php
/**
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/ClientesHasBrindesEstoque/left_menu.ctp
  * @date     09/08/2017
  */

if (!isset($mode)) {
    $mode = 'view';
}

?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">

    <ul class="nav nav-pills nav-stacked">
        <li class="active">
            <?= $this->Html->link(__('Menu'), []) ?>
        </li>

        
        <?php if ($mode != 'editStock') : ?>
        <li>
            <?= $this->Html->link(__('Entrada de Estoque'), ['controller' => 'ClientesHasBrindesEstoque', 'action' => 'adicionar_estoque', $brindes_id]) ?>
        </li>
        <li>
            <?= $this->Html->link(__("Emissão Manual"), ['controller' => 'ClientesHasBrindesEstoque', 'action' => 'venda_manual_estoque', $brindes_id]);?>
        </li>
        <?php endif; ?>

    </ul>

</nav>

