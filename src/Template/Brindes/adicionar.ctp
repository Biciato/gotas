<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/adicionar_brinde_rede.ctp
 * @date     09/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Brindes da Minha Rede', ['controller' => 'brindes', 'action' => 'brindes_minha_rede']);
$this->Breadcrumbs->add('Cadastrar Brinde', [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);
?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
    </ul>
</nav>
<div class="brindes form col-lg-9 col-md-10 columns content">
    <legend><?= 'Cadastrar Brinde' ?></legend>
    <?= $this->Form->create($brinde) ?>
    <fieldset>
        <?= $this->element('../Brindes/brindes_form', ['brinde' => $brinde, 'clientesId' => $clientesId]); ?>
    </fieldset>
    
    <?= $this->Form->end() ?>
</div>