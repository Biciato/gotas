<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/emissao_brinde_superiores.ctp
 * @date     13/06/2018
 */

?>

<?= $this->element('../Cupons/left_menu', ['mode' => 'escolher_brinde', 'controller' => 'cupons', 'action' => 'print_gift']) ?>

<div class="col-lg-9 col-md-10 columns">
    <?= $this->element("../Cupons/form_emissao_brinde", ["showMenu" => false, "show_breadcrumbs" => false]) ?>
</div>



