<?php

use Cake\Core\Configure;

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Transportadoras/filtro_Transportadoras.ctp
 * @date     04/01/2018
 */

$options = [
    'razao_social' => 'Razão Social',
    'nome_fantasia' => 'Nome Fantasia',
    'cnpj' => 'CNPJ'
];

?>

<div class="form-group">

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading panel-heading-sm text-center"
                data-toggle="collapse"
                href="#collapse1"
                data-target="#filter-coupons">
                <!-- <h4 class="panel-title"> -->
                    <div>
                        <span class="fa fa-search"></span>
                            Exibir / Ocultar Filtros
                    </div>

                <!-- </h4> -->
            </div>
            <div id="filter-coupons" class="panel-collapse collapse in">
                <div class="panel-body">

                    <?= $this->Form->create('Post', array('url' => array('controller' => $controller, 'action' => $action, isset($id) ? $id : null))) ?>

                    <div class="inline-block">
                        <div class="col-lg-4">
                            <?= $this->Form->input('nome_fantasia', [
                                'type' => 'text',
                                'id' => 'nome_fantasia',
                                'label' => 'Nome Fantasia',
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $this->Form->input('razao_social', [
                                'type' => 'text',
                                'id' => 'razao_social',
                                'label' => 'Razão Social',
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $this->Form->input('cnpj', [
                                'type' => 'text',
                                'id' => 'cnpj',
                                'label' => 'CNPJ',
                                'class' => 'form-control'
                            ]) ?>
                        </div>

                        <div class="col-lg-12 text-right">
                            <button type="submit" 
                                class="btn btn-primary botao-pesquisar">
                                    <i class="fa fa-search"></i>
                                    Pesquisar
                            </button>
                        </div>

                    </div>
                <?= $this->Form->end() ?>

            </div>
        </div>
    </div>

</div>


<?php

$extension = Configure::read("debug") ? "" : ".min";
echo $this->Html->script('scripts/transportadoras/filtro_transportadoras' . $extension);
$this->fetch('script');

?>
