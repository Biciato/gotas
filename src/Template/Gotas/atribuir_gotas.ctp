<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Gotas/atribuir_gotas_form.ctp
 * @date     06/08/2017
 * 
 * Arquivo para atribuir gotas de cliente na view de funcionário
 */

 use Cake\Core\Configure;

?> 

<?= $this->element('../Pages/left_menu', ['item_selected' => 'atribuir_gotas']) ?>

<?php 
    echo $this->element('../Gotas/atribuir_gotas_form')
?>

