<?php

use Cake\Core\Configure;
/**
 * @description Ver detalhes de Usuário (view de administrador)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/detalhes_usuario.ctp
 * @date        28/08/2017
 *
 */

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Meus Clientes', array("controller"=> "usuarios", "action" => "meusClientes"));
$this->Breadcrumbs->add('Detalhes de Usuário', array(), ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

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

    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <a href="/usuarios/meus_clientes" class="btn btn-primary botao-cancelar"> <i class="fa fa-arrow-left"></i> Voltar</a>
        </div>
    </div>

</div>
