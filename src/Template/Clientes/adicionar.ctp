<?php 

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/adicionar.ctp
 * @date     27/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);

$this->Breadcrumbs->add('Detalhes da Rede', ['controller' => 'Redes', 'action' => 'ver_detalhes', $rede->id]);

$this->Breadcrumbs->add('Adicionar Unidade', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->element('../Clientes/left_menu', 
    [
    'controller' => 'redes', 
    'action'=> 'ver_detalhes', 
    'id' => $rede->id
    ]
) ?>
<div class="clientes form col-lg-9 col-md-10 columns content">
    <?= $this->element('../Clientes/clientes_form', ['cliente' => $cliente, 'title' => __('Adicionar Unidade para Rede {0}', $rede->nome_rede)])?>
</div>