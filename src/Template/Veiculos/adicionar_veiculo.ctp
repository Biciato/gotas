<?php

/**
 * @description View para adicionar veículo de um usuário
 * @author      Gustavo Souza Gonçalves
 * @file        Template\Veiculos\adicionar_veiculo.php
 * @date        25/07/2017
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] == (int)Configure::read('profileTypes')['UserProfileType']) {

    $this->Breadcrumbs->add(
        'Meus Veículos',
        ['controller' => 'veiculos', 'action' => 'meus_veiculos']
    );

} else {
    $this->Breadcrumbs->add('Veículos', ['controller' => 'veiculos', 'action' => 'index']);
}

$this->Breadcrumbs->add('Adicionar Veículo', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);


?>

<?= $this->element('../Veiculos/left_menu', ['usuario' => $usuario]) ?>
<div class="veiculos form col-lg-9 col-md-8 columns content">
    <?= $this->element('../Veiculos/form_cadastrar_veiculo', ['veiculo' => $veiculo, "title" => "Adicionar Veículo"]) ?>
</div>

