<?php

use Cake\Core\Configure;

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/filtro_usuarios.ctp
 * @date     13/08/2017
 */

$debugExtension = Configure::read("debug") ? "" : ".min";

$show_filiais = isset($show_filiais) ? $show_filiais : true;
$fixarTipoPerfil = isset($fixarTipoPerfil) ? $fixarTipoPerfil : false;
$tipoPerfilFixo = isset($tipoPerfilFixo) ? $tipoPerfilFixo : null;
$options = [
    'nome' => 'nome',
    'cpf' => 'cpf',
    'doc_estrangeiro' => 'documento estrangeiro',
    'email' => 'e-mail'
];

if (empty($perfisUsuariosList)) {
    $perfisUsuariosList = [
        PROFILE_TYPE_ADMIN_NETWORK => PROFILE_TYPE_ADMIN_NETWORK_TRANSLATE,
        PROFILE_TYPE_ADMIN_REGIONAL => PROFILE_TYPE_ADMIN_REGIONAL_TRANSLATE,
        PROFILE_TYPE_ADMIN_LOCAL => PROFILE_TYPE_ADMIN_LOCAL_TRANSLATE,
        PROFILE_TYPE_MANAGER => PROFILE_TYPE_MANAGER_TRANSLATE,
        PROFILE_TYPE_WORKER => PROFILE_TYPE_WORKER_TRANSLATE,
        PROFILE_TYPE_USER => PROFILE_TYPE_USER_TRANSLATE
    ];
}

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

                    <?=
                        $this->Form->create(
                            'Post',
                            [
                                'url' => false,
                                'id' => 'filtro_usuarios_form'
                            ]
                        )
                    ?>

                    <div class="form-group row">
                        <?php if ($fixarTipoPerfil) : ?>
                            <div class="col-lg-3">
                                <?= $this->Form->input(
                                    'tipo_perfil',
                                    [
                                        'type' => 'select',
                                        'id' => 'tipo_perfil',
                                        'label' => 'Tipo de Perfil',
                                        "empty" => "<Todos>",
                                        'options' => [],
                                        "disabled" => true,
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>
                        <?php else : ?>
                            <div class="col-lg-3">
                                <?= $this->Form->input(
                                    'tipo_perfil',
                                    [
                                        'type' => 'select',
                                        'id' => 'tipo_perfil',
                                        'label' => 'Tipo de Perfil',
                                        "empty" => "<Todos>",
                                        // 'options' => Configure::read("profileTypesTranslatedAdminNetwork"),
                                        'options' =>// $perfisUsuariosList
                                          [],
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>
                        <?php endif; ?>
                        <div class="col-lg-3">
                            <?= $this->Form->input(
                                'nome',
                                [
                                    'type' => 'text',
                                    'id' => 'nome',
                                    'label' => 'Nome',
                                    'class' => 'form-control col-lg-2',
                                    'placeholder' => "Nome..."
                                ]
                            ) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $this->Form->input(
                                'email',
                                [
                                    'type' => 'text',
                                    'id' => 'email',
                                    'label' => 'Email',
                                    'class' => 'form-control col-lg-2',
                                    'placeholder' => "E-mail..."
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
                                    'class' => 'form-control col-lg-2',
                                    'placeholder' => "CPF..."
                                ]
                            ) ?>
                        </div>
                    </div>
                    <div class="form-group row">

                        <!-- <div class="col-lg-3">
                            <? // $this->Form->input(
                            // 'doc_estrangeiro',
                            // [
                            //     'type' => 'text',
                            //     'id' => 'doc_estrangeiro',
                            //     'label' => 'Documento Estrangeiro',
                            //     'class' => 'form-control col-lg-2',
                            //     'placeHolder' => "Documento Estrangeiro"
                            // ]
                            // )
                            ?>
                        </div> -->

                        <div class="col-lg-6">

                            <?php
                            echo $this->Form->input(
                                'redes_id',
                                [
                                    'type' => 'select',
                                    "id" => "redes_id",
                                    'class' => 'redes_list',
                                    'options' => [],
                                    'multiple' => false,
                                    "empty" => "<Todos>",
                                    'label' => 'Filtrar por rede',
                                    ]
                                );
                                
                                ?>
                        </div>
                        <div class="col-lg-6">
                            <?php  echo $this->Form->input(
                                'clientes_id',
                                [
                                    'type' => 'select',
                                    'id' => 'clientes_rede',
                                    "empty" => "<Todos>",
                                    'class' => 'clientes_rede',
                                    'label' => 'Filtrar por unidade',
                                    'disabled' => 'disabled',
                                ]
                            );
                            
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12 text-right">
                            <button type="submit" class="btn btn-primary save-button botao-pesquisar" id="filtrar_usuarios">
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


<?php $this->append('script'); ?>
    <script type="text/javascript">
        var filtro_usuarios = 
          {
            init: function()
              {
                var self = this;
                self.carregarOpcoes();
                return this;
              },
            carregarOpcoes: function()
              {
                var opcoes_tipos_perfil = '<option value="" selected="selected">&lt;Todos&gt </option>' ;
                var opcoes_redes = '<option value="" selected="selected">&lt;Todos&gt </option>' ;
                  
                $.ajax(
                  {
                    url: '/app_gotas/usuarios/carregar_tipos_perfil',
                    data: {},
                    method: 'GET',
                    success: function(resposta)
                      {
                        $.each(resposta.source, function(i, item)
                          {
                            opcoes_tipos_perfil += '<option value="' + i + '">' + item + '</option>';
                          });
                        $("#tipo_perfil").html(opcoes_tipos_perfil);
                      }
                  });
                  $.ajax(
                  {
                    url: '/app_gotas/usuarios/carregar_redes',
                    data: {},
                    method: 'GET',
                    success: function(resposta)
                      {
                        $.each(resposta.source, function(i, item)
                          {
                            opcoes_tipos_perfil += '<option value="' + i + '">' + item + '</option>';
                          });
                        $("#redes_id").html(opcoes_tipos_perfil);
                      }
                  })
              }
          };
        $(document).ready(function()
          {
            filtro_usuarios.init();
          });
    </script>
<?php $this->end(); ?>