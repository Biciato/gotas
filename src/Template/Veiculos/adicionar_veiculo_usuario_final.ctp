<?php

/**
 * @description View para adicionar veículo de um usuário
 * @author      Gustavo Souza Gonçalves
 * @file        Template\Veiculos\adicionar_veiculo_usuario_final.php
 * @date        25/07/2017
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'atualizar_cadastro_cliente', 'mode_selected' => 'atualizar_cadastro_cliente_veiculos']) ?>

<div class="veiculos form col-lg-9 col-md-8 columns content">
    <?= $this->element('../Veiculos/form_cadastrar_veiculo', ['veiculo' => $veiculo]) ?>
</div>

