<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/filtro_redes.ctp
 * @since    24/11/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$qteRegistros = [
    '10' => 10,
    '100' => 100,
    '500' => 500,
    '1000' => 1000
];

?>

<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
            <!-- <h4 class="panel-title"> -->
                <div>
                    <span class="fa fa-search"></span>
                        Exibir / Ocultar Filtros
                </div>

            <!-- </h4> -->
        </div>
        <div id="filter-coupons" class="panel-collapse collapse in">
            <div class="panel-body">
                <?= $this->Form->create('Post', [
                    'url' =>
                        [
                        'controller' => $controller,
                        'action' => $action,
                        !empty($id) ? $id : null
                    ]
                ]) ?>

                <div class="form-group row">
                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'nome',
                            [
                                "id" => "nome",
                                "label" => "Nome",
                                "class" => "form-control col-lg-5",
                                "placeholder" => "Nome para filtro"

                            ]
                        ) ?>
                    </div>

                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'brinde_necessidades_especiais',
                            [
                                "type" => "select",
                                "id" => "brinde_necessidades_especiais",
                                "title" => "Brindes para Pessoas com Necessidades Especiais",
                                "title" => "Brindes Necessidades Especiais",
                                "class" => "form-control col-lg-5",
                                "empty" => "<Todos>",
                                "options" => Configure::read("yesNoArray")
                            ]
                        ) ?>
                    </div>
                  
                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'atribuir_automatico',
                            [
                                "type" => "select",
                                "id" => "atribuir_automatico",
                                "label" => "Atribuir Automaticamente",
                                "label" => "Atribuir Automaticamente",
                                "class" => "form-control col-lg-5",
                                "empty" => "<Todos>",
                                "options" => Configure::read("yesNoArray")
                            ]
                        ) ?>
                    </div>
                    <div class="col-lg-5">
                        <?= $this->Form->input(
                            'habilitado',
                            [
                                "type" => 'select',
                                "id" => 'habilitado',
                                "label" => 'Habilitado',
                                "options" => Configure::read("enabledDisabledArray"),
                                "empty" => "<Todos>",
                                "default" => '10',
                                "class" => 'form-control col-lg-2'
                            ]
                        ) ?>
                    </div>
                    <div class="col-lg-5">
                        <?= $this->Form->input(
                            'qteRegistros',
                            [
                                "type" => 'select',
                                "id" => 'qteRegistros',
                                "label" => 'Qte. de Registros',
                                "options" => $qteRegistros,
                                "empty" => "<Todos>",
                                "default" => '10',
                                "class" => 'form-control col-lg-2'
                            ]
                        ) ?>
                    </div>

                    <div class="col-lg-2 vertical-align">
                        <button type="submit" 
                                class="btn btn-primary btn-block botao-confirmar">
                                <span class="fa fa-search"></span> 
                                Pesquisar
                        </button>
                    </div>
                    <?= $this->Form->end() ?>
                </div>

            </div>
        </div>
    </div>

</div>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/clientes/filtro_clientes') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/clientes/filtro_clientes.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
