<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Veiculos/filtro_relatorio_veiculos_usuarios_redes.ctp
 * @date     20/03/2018
 */

use Cake\Core\Configure;

$options = [
    'placa' => 'Placa',
    'modelo' => 'Modelo',
    'fabricante' => 'Fabricante',
    'ano' => 'Ano'
];

$qteRegistros = [
    10 => 10,
    100 => 100,
    500 => 500,
    1000 => 1000
]

?>

<div class="form-group">

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading panel-heading-sm text-center"     
                data-toggle="collapse" 
                href="#collapse1"   
                data-target="#filter-coupons">
                    <div>
                        <span class="fa fa-search"></span>
                            Exibir / Ocultar Filtros
                    </div>
            
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
                        <div class="form-group row">

                            <div class="col-lg-4">
                                <?= $this->Form->input(
                                    'redes_id',
                                    [
                                        'id' => 'redes_id',
                                        'label' => 'Selecione uma Rede para Filtar',
                                        'type' => 'select',
                                        'options' => $redesList,
                                        'empty' => '<Todas>',
                                        'class' => 'form-control col-lg-8'
                                    ]
                                ) ?>
                            </div>

                            <div class="col-lg-5">
                                <?= $this->Form->input(
                                    'parametro',
                                    [
                                        'id' => 'parametro',
                                        'label' => 'Parâmetro',
                                        'class' => 'form-control col-lg-6',
                                        'placeHolder' => 'Filtrar por parâmetro'
                                    ]
                                ) ?> 
                            </div>

                            <div class="col-lg-3">
                                <?= $this->Form->input('opcoes', [
                                    'type' => 'select',
                                    'id' => 'opcoes',
                                    'label' => 'Opções',
                                    'options' => $options,
                                    'class' => 'form-control col-lg-2'
                                ]) ?>
                            </div>  
                        </div>  

                        <div class="form-group row">

                            <div class="col-lg-4">

                                <?= $this->Form->input(
                                    'auditInsertInicio',
                                    [
                                        'type' => 'text',
                                        'id' => 'auditInsertInicio',
                                        'label' => 'Data Criação Início',
                                        'class' => 'form-control col-lg-2',
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-4">

                                <?= $this->Form->input(
                                    'auditInsertFim',
                                    [
                                        'type' => 'text',
                                        'id' => 'auditInsertFim',
                                        'label' => 'Data Criação Fim',
                                        'class' => 'form-control col-lg-2',
                                    ]
                                ) ?>
                            </div>

                            <div class="col-lg-2">
                                <?= $this->Form->input(
                                    'qte_registros',
                                    [
                                        'type' => 'select',
                                        'id' => 'qte_registros',
                                        'label' => "Qte. Registros",
                                        'empty' => '<Todos>',
                                        'options' => $qteRegistros,
                                        'default' => 10

                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-2 vertical-align">

                                <?= $this->Form->button("Pesquisar", ['class' => 'btn btn-primary btn-block']) ?>
                            </div>
                      
                        </div>
                    </div>
                <?= $this->Form->end() ?>

            </div>
        </div>
    </div>
    
</div>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/veiculos/filtro_relatorio_veiculos_usuarios_redes') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/veiculos/filtro_relatorio_veiculos_usuarios_redes.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
