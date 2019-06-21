<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Redes/left_menu.ctp
 * @date     21/11/2017
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
                    <?= $this->Html->link(__("Funcionários da Rede"), ['controller' => 'usuarios', 'action' => 'usuarios_rede', $redes_id]) ?>
                </li>

                <li>
                    <?= $this->Html->link(__("Atribuir Administração Regional/Comum"), ['controller' => 'usuarios', 'action' => 'atribuir_admin_regional_comum', $redes_id]) ?>
                </li>

            <?php endif; ?>
            <li class="active">
                <?= $this->Html->link(__('Ações'), []) ?>
            </li>

            <?php if ($mode == 'view') : ?>

                <li>
                    <?= $this->Html->link(__("Nova Rede"), ['controller' => 'redes', 'action' => 'adicionar_rede']) ?>
                </li>

            <?php elseif ($mode == 'details') : ?>

                <li>
                    <?= $this->Html->link(__("Nova Unidade de Rede"), ['controller' => 'clientes', 'action' => 'adicionar', $redes_id]) ?>
                </li>

            <?php endif; ?>

            <?php if ($show_reports) : ?>

            <li class="active">
                <?= $this->Html->link(__('Relatórios'), []) ?>
            </li>

            <li>
                <?= $this->Html->link(__("Redes"), ['controller' => 'redes', 'action' => 'relatorio_redes']) ?>
            </li>
            <li>
                <?= $this->Html->link(__("Unidades de Redes"), ['controller' => 'redes_has_clientes', 'action' => 'relatorio_unidades_redes']) ?>
            </li>
            <li>
                <?= $this->Html->link(__("Equipe de uma Rede"), ['controller' => 'usuarios', 'action' => 'relatorio_equipe_redes']) ?>
            </li>


            <?php endif; ?>

        </ul>
    </nav>
