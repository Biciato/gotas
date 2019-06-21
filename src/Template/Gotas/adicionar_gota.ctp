<?php

/**
 * @var \App\View\AppView $this
 *
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Gotas/adicionar_gota.ctp
 * @date     06/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);


if ($usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {

	$this->Breadcrumbs->add('Cadastro de Gotas de Minha Rede', ['controller' => 'gotas', 'action' => 'gotas_minha_rede']);
} else {
	$this->Breadcrumbs->add('Cadastro de Gotas de Minha Loja',
	['controller' => 'gotas', 'action' => 'gotas_minha_loja']);
}

$this->Breadcrumbs->add('Configurar Métrica de Gotas', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
	['class' => 'breadcrumb']
);
?>

<?= $this->element('../Gotas/left_menu', ['mode' => 'edit']) ?>

<div class="gotas form col-lg-9 col-md-8 columns content">
		<?= $this->element('../Gotas/gotas_config_input_form', ['gota' => $gota]) ?>
</div>

