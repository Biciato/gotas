<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/RedesUsuariosExcecaoAbastecimentos/left_menu.ctp
 * @date     2019-06-16
 */


use Cake\Core\Configure;
use Cake\Routing\Router;

?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active">
            <?= $this->Html->link(__('Menu'), []) ?>
        </li>
        <li class="active">
            <?= $this->Html->link(__('Ações'), []) ?>
        </li>

        <li>
            <a href="/redesUsuariosExcecaoAbastecimentos/add">Adicionar Usuário</a>

        </li>
    </ul>
</nav>
