<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/filtro_redes_relatorio.ctp
 * @date     27/02/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$listaOpcoes = [
    'nome_rede' => 'Nome da Rede',
];
$listaBooleanGenerica = [
    null => '<Todos>',
    true => 'Sim',
    false => 'Não'
];

$qteRegistros = [
    null => '<Todos>',
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
                        'action' => $action
                    ]
                ]) ?>

                <div class='form-group row'>
                    <div class="col-lg-5">
                        <?= $this->Form->input(
                            'nome_rede',
                            [
                                'id' => 'nome_rede',
                                'label' => 'Nome da Rede',
                                'placeholder' => 'Todos',
                                'class' => 'form-control col-lg-5'
                            ]
                        ) ?>
                    </div>

                    <div class='col-lg-3'>
                        <?= $this->Form->input(
                            'ativado',
                            [
                                'id' => 'ativado',
                                'label' => 'Registros Ativos no Sistema?',
                                'type' => 'select',
                                'options' => $listaBooleanGenerica
                            ]
                        ) ?>
                    </div>
                    <!-- @todo gustavosg: Conferir layout -->
                </div>


                <div class="form-group row">
                    <div class="col-lg-2">

                        <?= $this->Form->input(
                            'qteRegistros',
                            [
                                'type' => 'select',
                                'id' => 'qteRegistros',
                                'label' => 'Qte. de Registros',
                                'options' => $qteRegistros,
                                'default' => '10',
                                'class' => 'form-control col-lg-2'
                            ]
                        ) ?>
                    </div>
                    <div class="col-lg-4">

                        <?= $this->Form->input(
                            'auditInsertInicio',
                            [
                                'type' => 'text',
                                'id' => 'auditInsertInicio',
                                'label' => 'Data de Criação Início',
                                'class' => 'form-control col-lg-2'
                            ]
                        ) ?>
                    </div>
                    <div class="col-lg-4">

                        <?= $this->Form->input(
                            'auditInsertFim',
                            [
                                'type' => 'text',
                                'id' => 'auditInsertFim',
                                'label' => 'Data de Criação Fim',
                                'class' => 'form-control col-lg-2'
                            ]
                        ) ?>
                    </div>

                    <div class="col-lg-2 vertical-align">

                        <?= $this->Form->button(
                            __("{0} Pesquisar", '<i class="fa fa-search" aria-hidden="true"></i>'),
                            [
                                'class' => 'btn btn-primary btn-block',
                                'type' => 'submit'
                            ]
                        ) ?>

                    </div>
                </div>


                <?= $this->Form->end() ?>

            </div>
        </div>
    </div>

</div>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/redes/filtro_redes_relatorio') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/redes/filtro_redes_relatorio.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
