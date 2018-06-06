<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesEstoque/filtro_relatorio_estoque_brindes_detalhado.ctp
 * @date     10/03/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$qteRegistros = [
    null => '<Todos>',
    '10' => 10,
    '100' => 100,
    '500' => 500,
    '1000' => 1000
];

$tiposOperacao = Configure::read('stockOperationTypesTranslated');

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
                        'action' => $action,
                        $id
                    ]
                ]) ?>

                <div class='form-group row'>

                    <div class="col-lg-2">
                        <?= $this->Form->input(
                            'qteRegistros',
                            [
                                'type' => "select",
                                'id' => "qteRegistros",
                                'label' => "Qte. Registros",
                                "class" => "form-control",
                                'options' => $qteRegistros,
                                'default' => '10',
                            ]
                        ) ?>
                    </div>
                    
                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'tipoOperacao',
                            [
                                'type' => "select",
                                'id' => "tipoOperacao",
                                'label' => "Tipos de Operação",
                                "class" => "form-control",
                                'empty' => '<Todos>',
                                'options' => $tiposOperacao,

                            ]
                        ) ?>
                    </div>

                    <div class="col-lg-2">

                        <?= $this->Form->input(
                            'auditInsertInicio',
                            [
                                'type' => 'text',
                                'id' => 'auditInsertInicio',
                                'label' => 'Início Criação',
                                'class' => 'form-control col-lg-2'
                            ]
                        ) ?>
                    </div>
                    <div class="col-lg-2">

                        <?= $this->Form->input(
                            'auditInsertFim',
                            [
                                'type' => 'text',
                                'id' => 'auditInsertFim',
                                'label' => 'Fim Criação',
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

    <?= $this->Html->script('scripts/clientes_has_brindes_habilitados_estoque/filtro_relatorio_estoque_brindes_detalhado') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/clientes_has_brindes_habilitados_estoque/filtro_relatorio_estoque_brindes_detalhado.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
