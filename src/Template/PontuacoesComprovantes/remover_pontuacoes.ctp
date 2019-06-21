<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/PontuacoesComprovantes/remover_pontuacoes.ctp
 * @date     08/01/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Remover Pontuações do Sistema', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../PontuacoesComprovantes/left_menu') ?> 

<div class="redes form col-lg-9 col-md-8 columns content">
    <legend>Remover Pontuações do Sistema</legend>

    <h4>Atenção!</h4>

    <span class="col-lg-12">Executando a operação a seguir, irá remover todas as pontuações de clientes inseridas no sistema. Deseja continuar?</span>

    <div class="col-lg-12">

    <?= $this->Form->create('POST', ['url' => ['controller' => 'pontuacoes_comprovantes', 'action' => 'executar_remover_pontuacoes']]) ?>
        <?= $this->Form->button(__('{0} Executar',
            $this->Html->tag('i', '', ['class' => 'fa fa-trash'])),
            [
                'class' => 'btn btn-danger',
                'escape' => false
                ]
                
                ) ?>
    <?= $this->Form->end() ?>
    </div>
<div/>