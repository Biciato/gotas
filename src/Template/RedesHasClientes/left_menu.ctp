<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/RedesHasClientes/left_menu.ctp
 * @date     05/08/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$mode = isset($mode) ? $mode : false;

?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active">
            <?= $this->Html->link(__('Menu'), []) ?>
        </li>

        <li class="active">
            <?= $this->Html->link(__('Ações'), []) ?>
        </li>
    </ul>
</nav>
