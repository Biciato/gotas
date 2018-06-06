<?php

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
            <div id="filter-coupons" class="panel-collapse collapse">
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
                        
                        <div class="col-lg-7">
                            <?= $this->Form->input('parametro', ['id' => 'parametro', 'label' => 'Parâmetro', 'class' => 'form-control col-lg-6']) ?> 
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

                        <div class="col-lg-2 vertical-align">

                            <?= $this->Form->button("Pesquisar", ['class' => 'btn btn-primary btn-block']) ?>
                        </div>
                    </div>
                <?= $this->Form->end() ?>

            </div>
        </div>
    </div>
    
</div>
