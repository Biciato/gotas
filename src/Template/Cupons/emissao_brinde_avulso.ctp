<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/emissao_brinde_avulso.ctp
 * @date     21/06/2018
 */

?>

<?= $this->element('../Pages/left_menu', ['mode' => 'escolher_brinde', 'controller' => 'cupons', 'action' => 'print_gift', 'item_selected' => 'emissao_brinde_avulso']) ?>

<div class="col-lg-9 col-md-10 columns">

    <input type="hidden" id="restrict_query" class="restrict_query" value="<?php $restrict_query?>" />

    <?= $this->element("../Cupons/form_emissao_brinde", ["showMenu" => false, "show_breadcrumbs" => false]) ?>
</div>



