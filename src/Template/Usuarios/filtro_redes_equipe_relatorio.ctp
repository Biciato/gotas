<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/filtro_redes_equipe_relatorio.ctp
 * @date     05/03/2018
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
                    <div class="col-lg-5">
                        <?= $this->Form->input(
                            'redes_id',
                            [
                                'id' => 'redes_id',
                                'label' => 'Selecione uma Rede para Filtar',
                                'type' => 'select',
                                'options' => $redesList,
                                'empty' => '<Todos>',
                                'class' => 'form-control col-lg-8'
                            ]
                        ) ?>
                    </div>

                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'nome',
                            [
                                'id' => 'nome',
                                'label' => 'Nome',
                                'type' => 'text',
                                'class' => 'form-control',
                                'placeholder' => 'Nome para filtrar'
                            ]
                        ) ?>

                    </div>

                    <div class="col-lg-3">
                        <?= $this->Form->input(
                            'tipo_perfil',
                            [
                                'id' => 'tipo_perfil',
                                'label' => 'Tipo do Perfil',
                                'type' => 'select',
                                'empty' => '<Todos>',
                                'options' => Configure::read('profileTypesTeam'),
                                'class' => 'form-control',
                                'placeholder' => 'Nome para filtrar'
                            ]
                        ) ?>

                    </div>

                </div>

                <div class='form-group row'>
                    <div class="col-lg-2">
                        <?= $this->Form->input(
                            'sexo',
                            [
                                'id' => 'sexo',
                                'label' => 'Sexo',
                                'type' => 'select',
                                'empty' => '<Todos>',
                                'options' => [
                                    true => 'Masculino',
                                    false => 'Feminino'
                                ],
                                'class' => 'form-control',
                                'placeholder' => 'Nome para filtrar'
                            ]
                        ) ?>

                    </div>

                    <div class="col-lg-2">
                        <?= $this->Form->input(
                            'conta_ativa',
                            [
                                'id' => 'conta_ativa',
                                'label' => 'Conta Ativa',
                                'type' => 'select',
                                'empty' => '<Todos>',
                                'options' => [
                                    true => 'Sim',
                                    false => 'Não'
                                ],
                                'class' => 'form-control',
                            ]
                        ) ?>

                    </div>
                    <div class="col-lg-2">
                        <?= $this->Form->input(
                            'conta_bloqueada',
                            [
                                'id' => 'conta_bloqueada',
                                'label' => 'Conta Bloqueada',
                                'type' => 'select',
                                'empty' => '<Todos>',
                                'options' => [
                                    true => 'Sim',
                                    false => 'Não'
                                ],
                                'class' => 'form-control'
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
                                'label' => 'Data Criação Fim',
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
