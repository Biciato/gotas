<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/filtro_usuarios.ctp
 * @date     13/08/2017
 */

 use Cake\Core\Configure;

$options = [
    'nome' => 'nome',
    'cpf' => 'cpf',
    'doc_estrangeiro' => 'documento estrangeiro',
    'email' => 'e-mail'
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
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <?= $this->Form->input(
                                    'parametro',
                                    [
                                        'id' => 'parametro',
                                        'label' => 'Parâmetro',
                                        'class' => 'form-control col-lg-6'
                                    ]
                                ) ?> 
                            </div>

                            <div class="col-lg-3">
                                <?= $this->Form->input(
                                    'opcoes',
                                    [
                                        'type' => 'select',
                                        'id' => 'opcoes',
                                        'label' => 'Opções',
                                        'options' => $options,
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>  

                            <div class="col-lg-2">
                                <?= $this->Form->input(
                                    'sexo',
                                    [
                                        'type' => 'select',
                                        'id' => 'sexo',
                                        'label' => 'Sexo',
                                        'options' => [
                                            true => 'Masculino',
                                            false => 'Feminino'
                                        ],
                                        'class' => 'form-control',
                                        'empty' => '<Todos>'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-2">
                                <?= $this->Form->input(
                                    'conta_ativa',
                                    [
                                        'type' => 'select',
                                        'id' => 'conta_ativa',
                                        'label' => 'Conta Ativa',
                                        'options' => [
                                            true => 'Sim',
                                            false => 'Não'
                                        ],
                                        'class' => 'form-control',
                                        'empty' => '<Todos>'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-2">
                                <?= $this->Form->input(
                                    'conta_bloqueada',
                                    [
                                        'type' => 'select',
                                        'id' => 'conta_bloqueada',
                                        'label' => 'Conta Bloqueada',
                                        'options' => [
                                            true => 'Sim',
                                            false => 'Não'
                                        ],
                                        'class' => 'form-control',
                                        'empty' => '<Todos>'
                                    ]
                                ) ?>
                            </div>

                        </div>
                        <div class="form-group row">

                            <div class="col-lg-2">

                                <?= $this->Form->input(
                                    'dataNascimentoInicio',
                                    [
                                        'type' => 'text',
                                        'id' => 'dataNascimentoInicio',
                                        'label' => 'Data Nasc. Inicio',
                                        'class' => 'form-control col-lg-2',
                                        
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-2">

                                <?= $this->Form->input(
                                    'dataNascimentoFim',
                                    [
                                        'type' => 'text',
                                        'id' => 'dataNascimentoFim',
                                        'label' => 'Data Nasc. Fim',
                                        'class' => 'form-control col-lg-2',
                                    ]
                                ) ?>
                            </div>

                            <div class="col-lg-2">

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
                            <div class="col-lg-2">

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
    <?= $this->Html->script('scripts/usuarios/filtro_relatorio_usuarios_cadastrados') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/usuarios/filtro_relatorio_usuarios_cadastrados.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
