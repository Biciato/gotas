<?php

/**
 * index.ctp
 *
 * Menu esquerdo para tipos_brindes_clientes
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TiposBrindesRede[]|\Cake\Collection\CollectionInterface $tipos_brindes
 *
 * @file     src/Template/TiposBrindesClientes/left_menu.ctp
 * @category View
 * @package App\Template\TiposBrindesClientes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 02/06/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.0.0
 *
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

            <li class="active">
                <?= $this->Html->link(__('Ações'), []) ?>
            </li>

            <?php if ($mode == 'add') : ?>

                <li>
                    <?= $this->Html->link(__("Atribuir Tipo de Brinde"), ['controller' => 'tipos_brindes_clientes', 'action' => 'adicionar_tipos_brindes_cliente', $clientesId]) ?>
                </li>

            <?php endif; ?>

        </ul>
    </nav>
