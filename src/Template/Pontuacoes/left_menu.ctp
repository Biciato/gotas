<?php
/**
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Pontuacoes/left_menu.ctp
  * @date     27/09/2017
  */

?>
<nav class="col-lg-3 col-md-2 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active">
            <?= $this->Html->link(__('Menu'), []) ?>
        </li>

        <?php if ($mode == 'report'):?>
        <?php if (false):
        
        // TODO: Será melhor elaborado quando for implementar os relatórios
        ?>


        <li>
            <?= $this->Html->link(__("Relatórios por Dia"), []) ?>
        </li>
        <li>
            <?= $this->Html->link(__("Relatórios por Cliente"), []) ?>
        </li>
        <li>
            <?= $this->Html->link(__("Relatório Analítico"), []) ?>
        </li>
        <?php endif;?>
        <?php endif; ?>


    </ul>
</nav>
