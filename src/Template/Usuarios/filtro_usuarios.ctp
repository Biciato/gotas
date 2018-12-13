<?php

use Cake\Core\Configure;

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/filtro_usuarios.ctp
 * @date     13/08/2017
 */

$show_filiais = isset($show_filiais) ? $show_filiais : true;
$fixarTipoPerfil = isset($fixarTipoPerfil) ? $fixarTipoPerfil : false;
$tipoPerfilFixo = isset($tipoPerfilFixo) ? $tipoPerfilFixo : null;
$options = [
    'nome' => 'nome',
    'cpf' => 'cpf',
    'doc_estrangeiro' => 'documento estrangeiro',
    'email' => 'e-mail'
];

$perfisUsuariosList = Configure::read("profileTypesTranslatedAdminNetwork");

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

                        <div class="form-group row">
                            <?php if ($fixarTipoPerfil): ?>
                            <div class="col-lg-4">
                                <?= $this->Form->input(
                                    'tipo_perfil',
                                    [
                                        'type' => 'select',
                                        'id' => 'tipo_perfil',
                                        'label' => 'Tipo de Perfil',
                                        "empty" => "<Todos>",
                                        'options' => Configure::read("profileTypesTranslatedAdminNetwork"),
                                        "value" => $tipoPerfilFixo,
                                        "disabled" => true,
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>
                            <?php else : ?>
                            <div class="col-lg-4">
                                <?= $this->Form->input(
                                    'tipo_perfil',
                                    [
                                        'type' => 'select',
                                        'id' => 'tipo_perfil',
                                        'label' => 'Tipo de Perfil',
                                        "empty" => "<Todos>",
                                        // 'options' => Configure::read("profileTypesTranslatedAdminNetwork"),
                                        'options' => $perfisUsuariosList,
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>
                            <?php endif; ?>
                            <div class="col-lg-4">
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
                            <div class="col-lg-4">
                                <?= $this->Form->input(
                                    'email',
                                    [
                                        'type' => 'text',
                                        'id' => 'email',
                                        'label' => 'Email',
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-2">
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
                                        'label' => 'Documento Estrangeiro',
                                        'class' => 'form-control col-lg-2',
                                        'placeHolder' => "Documento Estrangeiro"
                                    ]
                                ) ?>
                            </div>

                            <div class="col-lg-7">

                                <?php

                                if (isset($unidades_ids) && sizeof($unidades_ids) > 0) {

                                    echo $this->Form->input(
                                        'filtrar_unidade',
                                        [
                                            'type' => 'select',
                                            'id' => 'filtrar_unidade',
                                            'label' => "Filtrar por unidade?",
                                            'empty' => 'Todas',
                                            'options' => $unidades_ids
                                        ]
                                    );
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-12 text-right">
                                <button type="submit" 
                                    class="btn btn-primary save-button botao-pesquisar">
                                    <i class="fa fa-search"></i>
                                    Pesquisar
                                </button>
                            </div>
                        </div>

                    </div>

                <?= $this->Form->end() ?>

            </div>
        </div>
    </div>

</div>

<?php

$extension = Configure::read("debug") ? "" : ".min";
echo $this->Html->script("scripts/usuarios/filtro_usuarios" . $extension);
echo $this->fetch('script');
?>
