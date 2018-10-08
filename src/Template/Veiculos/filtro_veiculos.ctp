<?php

use Cake\Core\Configure;

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/veiculos/filtro_veiculos.ctp
 * @date     04/01/2018
 */

$options = [
    'placa' => 'Placa',
    'modelo' => 'Modelo',
    'fabricante' => 'Fabricante',
    'ano' => 'Ano',
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

                    <?=
                    $this->Form->create(
                        'Post',
                        [
                            'url' =>
                                [
                                'controller' => $controller,
                                'action' => $action,
                                isset($id) ? $id : null
                            ]
                        ]
                    )
                    ?>

                    <div class="inline-block">

                        <div class="col-lg-2">
                            <?= $this->Form->input('placa', [
                                'type' => 'text',
                                'id' => 'placa',
                                'label' => 'Placa',
                                'class' => 'form-control col-lg-2'
                            ]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $this->Form->input('modelo', [
                                'type' => 'text',
                                'id' => 'modelo',
                                'label' => 'Modelo',
                                'class' => 'form-control col-lg-2'
                            ]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $this->Form->input('fabricante', [
                                'type' => 'text',
                                'id' => 'fabricante',
                                'label' => 'Fabricante',
                                'class' => 'form-control col-lg-2'
                            ]) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $this->Form->input('ano', [
                                'type' => 'text',
                                'id' => 'ano',
                                'label' => 'Ano',
                                'options' => $options,
                                'class' => 'form-control col-lg-2'
                            ]) ?>
                        </div>

                        <div class="col-lg-2 vertical-align">

                            <?= $this->Form->button("Pesquisar", ['class' => 'btn btn-primary btn-block']) ?>
                        </div>
                    </div>
                <?= $this->Form->end() ?>

            </div>
        </div>
    </div>

</div>


<?php

$extension = Configure::read("debug") ? "" : ".min";
echo $this->Html->script('scripts/veiculos/filtro_veiculos' . $extension);
$this->fetch('script');

?>
