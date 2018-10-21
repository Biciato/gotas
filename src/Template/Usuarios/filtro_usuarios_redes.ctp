<?php

use Cake\Core\Configure;

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/filtro_usuarios.ctp
 * @date     13/08/2017
 */

$options = [
    'nome' => 'nome',
    'cpf' => 'cpf',
    'doc_estrangeiro' => 'documento estrangeiro',
    'email' => 'e-mail'
];

if (isset($filter_redes)) {
    $options = [
        'nome' => 'nome',
        'cpf' => 'cpf',
        'doc_estrangeiro' => 'documento estrangeiro',
        'email' => 'e-mail'
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
                            <div class="col-lg-3">
                                <?= $this->Form->input(
                                    'tipo_perfil',
                                    [
                                        'type' => 'select',
                                        'id' => 'tipo_perfil',
                                        'label' => 'Tipo de Perfil',
                                        "empty" => "<Todos>",
                                        'options' => Configure::read("profileTypesWorkersTranslated"),
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-6">
                                <?= $this->Form->input(
                                    'nome',
                                    [
                                        'type' => 'text',
                                        'id' => 'nome',
                                        'label' => 'Nome',
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= $this->Form->input(
                                    'cpf',
                                    [
                                        'type' => 'text',
                                        'id' => 'cpf',
                                        'label' => 'CPF',
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= $this->Form->input(
                                    'doc_estrangeiro',
                                    [
                                        'type' => 'text',
                                        'id' => 'doc_estrangeiro',
                                        'label' => 'Doc Estrangeiro',
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>

                            <div class="col-lg-7 ">
                                <?= $this->Form->input(
                                    'filtrar_unidade',
                                    [
                                        'type' => 'select',
                                        'id' => 'filtrar_unidade',
                                        'label' => "Filtrar por unidade?",
                                        'empty' => '<Todas>',
                                        'options' => $unidades_ids
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

<?php

$extension = Configure::read("debug") ? "" : ".min";
echo $this->Html->script('scripts/usuarios/filtro_usuarios_redes' . $extension);
$this->fetch('script');

?>
