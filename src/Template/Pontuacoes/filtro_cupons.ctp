<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/filtro_cupons
 * @date     01/10/2017
 */

use Cake\Core\Configure;
?>

<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading  panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
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

                <div class="inline-block">

                    <div class="form-group row">
                        <div class="col-lg-4">

                            <?= $this->Form->input(
                                'filtrar_unidade',
                                [
                                    'type' => 'select',
                                    'id' => 'filtrar_unidade',
                                    'label' => "Filtrar por unidade?",
                                    'empty' => 'Todas',
                                    'options' => $unidades_ids
                                ]
                            ) ?>
                        </div>

                        <div class="col-lg-4">
                            <?= $this->Form->input(
                                'funcionarios_id',
                                array(
                                    "type" => 'select',
                                    "label" => 'Funcionário',
                                    "id" => 'funcionarios_id',
                                    "empty" => '<Todos>',
                                    "options" => $funcionarios
                                )
                            ); ?>
                        </div>

                        <div class="col-lg-2">
                            <?= $this->Form->input(
                                'data_inicio',
                                [
                                    'type' => 'text',
                                    'id' => 'data_inicio',
                                    'label' => 'Data de Início',
                                    'format' => 'd/m/Y',
                                    'default' => date('d/m/Y', strtotime('-7 day')),
                                    "value" => $start,
                                    'class' => 'datepicker-input',
                                    'div' =>
                                    [
                                        'class' => 'form-inline',
                                    ],
                                ]
                            ) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $this->Form->input(
                                'data_fim',
                                [
                                    'type' => 'text',
                                    'id' => 'data_fim',
                                    'label' => 'Data de Fim',
                                    'format' => 'd/m/Y',
                                    'default' => date('d/m/Y'),
                                    "value" => $end,
                                    'class' => 'datepicker-input',
                                    'div' =>
                                    [
                                        'class' => 'form-inline',
                                    ],
                                ]
                            ) ?>
                        </div>

                        <div class="col-lg-12">

                            <div class=" vertical-align pull-right">

                                <?= $this->Form->button(
                                    __(
                                        '{0} Pesquisar',
                                        '<i class="fa fa-search" aria-hidden="true"></i>'
                                    ),
                                    [
                                        'id' => 'search_button',
                                        'class' => 'btn btn-primary'
                                    ]
                                ) ?>

                            </div>
                        </div>
                    </div>


                    <?= $this->Form->text('restrict_query', ['id' => 'restrict_query', 'value' => true, 'style' => 'display: none;']); ?>

                </div>
                <?= $this->Form->end(); ?>

            </div>

        </div>
    </div>
</div>

<?php
$debugExtension = (Configure::read('debug') == true) ? "" : "";
?>
<script src="/webroot/js/scripts/pontuacoes/filtro_cupons<?= $debugExtension ?>.js"></script>
