<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\PontuacoesComprovantes
 * @file     src/Template/PontuacoesComprovantes/filtro_relatorio_pontuacoes_comprovantes_redes.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     12/03/2018
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

$habilitadosSelect = Configure::read('enabledDisabledArray');

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

                <div class='form-group row'>
                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'redes_id',
                            [
                                'id' => 'redes_id',
                                'label' => 'Selecione uma Rede para Filtar',
                                'type' => 'select',
                                'options' => $redesList,
                                'empty' => '<Selecionar>',
                                'class' => 'form-control col-lg-8'
                            ]
                        ) ?>
                    </div>

                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'clientes_id',
                            [
                                'id' => 'clientes_id',
                                'label' => 'Selecione uma Unidade para Filtar',
                                'type' => 'select',
                                'options' => $clientesList,
                                'empty' => '<Todos>',
                                'class' => 'form-control col-lg-8'
                            ]
                        ) ?>
                    </div>
                    
                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'funcionarios_id',
                            [
                                'id' => 'funcionarios_id',
                                'label' => 'Selecione um Funcionário para Filtar',
                                'type' => 'select',
                                'options' => $funcionariosList,
                                'empty' => '<Todos>',
                                'class' => 'form-control col-lg-8'
                            ]
                        ) ?>
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-lg-5">

                        <?= $this->Form->input(
                            'auditInsertInicio',
                            [
                                'type' => 'text',
                                'id' => 'auditInsertInicio',
                                'label' => 'Data Início',
                                'class' => 'form-control col-lg-2',
                                'value' => $dataInicial
                                ]
                                ) ?>
                    </div>
                    <div class="col-lg-5">

                        <?= $this->Form->input(
                            'auditInsertFim',
                            [
                                'type' => 'text',
                                'id' => 'auditInsertFim',
                                'label' => 'Data Fim',
                                'class' => 'form-control col-lg-2',
                                'value' => $dataFinal
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
    <?= $this->Html->script('scripts/pontuacoes_comprovantes/filtro_relatorio_pontuacoes_comprovantes_redes') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/pontuacoes_comprovantes/filtro_relatorio_pontuacoes_comprovantes_redes.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
