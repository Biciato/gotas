<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/filtro_usuarios.ctp
 * @date     13/08/2017
 */

$show_filiais = isset($show_filiais) ? $show_filiais : true;

$options = [
    'nome' => 'nome',
    'cpf' => 'cpf',
    'doc_estrangeiro' => 'documento estrangeiro',
    'email' => 'e-mail'
];

if (isset($filter_redes) && $filter_redes) {
    $options = [
        'nome' => 'nome',
        'cpf' => 'cpf',
        'doc_estrangeiro' => 'documento estrangeiro',
        'email' => 'e-mail',
        'nome_rede' => 'redes'
    ];
}
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
                        <?php if ($show_filiais) : ?>

                            <div class="col-lg-5">
                                <?= $this->Form->input('parametro', ['id' => 'parametro', 'label' => 'Parâmetro', 'class' => 'form-control col-lg-6 parametro']) ?>
                            </div>

                            <div class="col-lg-3">
                                <?= $this->Form->input(
                                    'opcoes',
                                    [
                                        'type' => 'select',
                                        'id' => 'opcoes',
                                        'class' => 'opcoes',
                                        'label' => 'Opções',
                                        'options' => $options,
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-2">
                                <?= $this->Form->input(
                                    'incluir_filiais',
                                    [
                                        'type' => 'select',
                                        'id' => 'incluir_filiais',
                                        'label' => "Incluir filiais?",
                                        'options' =>
                                            [
                                            false => 'Não',
                                            true => 'Sim'
                                        ]
                                    ]
                                ) ?>
                            </div>

                            <div class="col-lg-2 vertical-align">

                                <?= $this->Form->button("Pesquisar", ['class' => 'btn btn-primary btn-block']) ?>
                            </div>
                        <?php else : ?>
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
                        <?php endif; ?>
                    </div>
                <?= $this->Form->end() ?>

            </div>
        </div>
    </div>

</div>
