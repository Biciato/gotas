<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/emissao_cupom.ctp
 * @date     13/06/2018
 */

?>

<?= $this->element('../Cupons/left_menu', ['mode' => 'escolher_brinde', 'controller' => 'cupons', 'action' => 'print_gift']) ?>

<div class="col-lg-9 col-md-10 columns">
    <?= $this->element("../Cupons/brinde_shower", ["showMenu" => false, "show_breadcrumbs" => false]) ?>
</div>



