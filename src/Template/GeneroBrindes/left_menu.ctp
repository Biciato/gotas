<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/GeneroBrindes/left_menu.ctp
 * @date     30/05/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$mode = isset($mode) ? $mode : false;

$show_reports = isset($show_reports) ? $show_reports : false;
?>

    <nav class="col-lg-3 col-md-2" id="actions-sidebar">
        <ul class="nav nav-pills nav-stacked">
            <li class="active">
                <?= $this->Html->link(__('Menu'), []) ?>
            </li>

            <?php if ($mode == 'details') : ?>

                <li>
                    <!-- <?= $this->Html->link(__("Usuários da Rede"), ['controller' => 'usuarios', 'action' => 'usuarios_rede', $redes_id]) ?> -->
                </li>

            <?php endif; ?>
            <li class="active">
                <?= $this->Html->link(__('Ações'), []) ?>
            </li>

            <?php if ($mode == 'view') : ?>

                <li>
                    <?= $this->Html->link(__("Novo Gênero"), ['controller' => 'genero_brindes', 'action' => 'adicionar_genero_brinde']) ?>
                </li>

            <?php endif; ?>

        </ul>
    </nav>
