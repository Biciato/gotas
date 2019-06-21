<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/filtro_redes.ctp
 * @date     24/11/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$listOptions = array(
    'nome_rede' => 'Nome da Rede'
);
?>

<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
                <div>
                    <span class="fa fa-search"></span>
                        Exibir / Ocultar Filtros
                </div>
        </div>
        <div id="filter-coupons" class="panel-collapse collapse in">
            <div class="panel-body">
                <?= $this->Form->create('Post', [
                    'url' =>
                        [
                        'controller' => $controller,
                        'action' => $action
                    ]
                ]) ?>
                <div class="form-group row">
                    <div class="col-lg-3">

                    <?= $this->Form->input('opcoes', [
                        'type' => 'select',
                        'id' => 'opcoes',
                        'label' => 'Pesquisar por',
                        'options' => $listOptions,
                        'class' => 'form-control col-lg-2'
                    ]) ?>
                    </div>

                    <div class="col-lg-9">
                    <?= $this->Form->input(
                        'parametro',
                        [
                            'id' => 'parametro',
                            'label' => 'Parâmetro',
                            "placeholder" => "Parâmetro...",
                            'class' => 'form-control col-lg-5'
                        ]
                    ) ?>
                    </div>
                </div>

                <div class="form-group row ">

                    <div class="col-lg-2 pull-right">
                        <button type="submit"
                            class="btn btn-primary btn-block botao-pesquisar">
                            <span class="fa fa-search"></span>
                            Pesquisar
                        </button>
                    </div>
                </div>
                <?= $this->Form->end() ?>


            </div>
        </div>
    </div>

</div>
