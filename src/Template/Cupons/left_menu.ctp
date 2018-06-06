<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/left_menu.ctp
 * @date     09/08/2017
 */

?>

    <nav class="col-lg-3 col-md-2 columns" id="actions-sidebar">
        <ul class="nav nav-pills nav-stacked">
            <li class="active">
                <?= $this->Html->link(__('Menu'), []) ?>
            </li>

            <?php if ($mode == 'report') : ?>
            
            <!-- // TODO: Será melhor elaborado quando for implementar os relatórios -->

                <?php if (false): ?> 

                <li>
                    <?= $this->Html->link(__("Relatórios por Dia"), []) ?>
                </li>
                <li>
                    <?= $this->Html->link(__("Relatórios por Cliente"), []) ?>
                </li>
                <li>
                    <?= $this->Html->link(__("Relatório Analítico"), []) ?>
                </li>
                <?php endif; ?>
            <?php endif; ?>


        </ul>
    </nav>
