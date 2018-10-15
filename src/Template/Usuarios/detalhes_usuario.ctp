<?php

use Cake\Core\Configure;
/**
 * @description Ver detalhes de Usuário (view de administrador)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/detalhes_usuario.ctp
 * @date        28/08/2017
 *
 */

?>

<?= $this->element('../Usuarios/left_menu',
    [
        'controller' => 'usuarios',
        'action' => 'meus_clientes',
        'update_password' => true,
        'mode' => 'view',
        'usuario' => $usuario,
        'cadastrar_veiculos' => true
    ]
) ?>
<div class="usuarios view col-lg-9 col-md-10">
    <h3><?= h($usuario->nome) ?></h3>
    <?= $this->element('../Usuarios/tabela_info_usuarios', ['usuario' => $usuario]);?>
</div>
