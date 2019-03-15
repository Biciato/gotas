<?php

/**
 * @description Arquivo para formulário de cadastro de usuários
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/usuario_form.ctp
 * @date        28/08/2017
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$show_menu = isset($show_menu) ? $show_menu : true;

$show_tipo_perfil = isset($show_tipo_perfil) ? $show_tipo_perfil : true;

$show_veiculos = isset($show_veiculos) ? $show_veiculos : false;

$usuarioLogadoTipoPerfil = isset($usuarioLogadoTipoPerfil) ? $usuarioLogadoTipoPerfil : (int)Configure::read('profileTypes')['UserProfileType'];
?>

<?php if (isset($usuarioLogado)) : ?>
<div class="usuarios form col-lg-12 col-md-8 columns content">

    <?php else : ?>

    <div class="col-lg-1"></div>
    <div class="container col-lg-10">

        <?php endif; ?>


        <fieldset>
            <legend>
                <?= isset($usuarioLogado) ? __('Adicionar conta') : __("Criar Conta") ?>
            </legend>

            <?= $this->Form->hidden('id'); ?>
            <?= $this->Form->hidden('usuarioLogadoTipoPerfil', ['value' => $usuarioLogadoTipoPerfil, 'class' => 'usuarioLogadoTipoPerfil']); ?>

            <?php if (isset($usuarioLogadoTipoPerfil)) {
                if ($usuarioLogadoTipoPerfil == PROFILE_TYPE_ADMIN_DEVELOPER) {
                    ?>
            <div class='col-lg-4'>
                <?php
                if (isset($redes_id)) {
                    echo $this->Form->input('tipo_perfil', array(
                        'type' => 'select',
                        'options' =>
                        array(
                            '' => '',
                            '1' => 'Administradores de uma Rede',
                            '3' => 'Administrador',
                            '4' => 'Gerente',
                            '5' => 'Funcionário',
                            '6' => 'Cliente Final',
                        )
                    ));
                } else {
                    echo $this->Form->input(
                        'tipo_perfil',
                        array(
                            'type' => 'select',
                            'options' =>
                            array(
                                '' => '',
                                '0' => 'Administradores da RTI / Desenvolvedor',
                                '1' => 'Administradores de uma Rede',
                                '3' => 'Administrador',
                                '4' => 'Gerente',
                                '5' => 'Funcionário',
                                '6' => 'Cliente Final',
                            )
                        )

                    );
                }

                ?>
            </div>
            <div class='col-lg-4 redes_input hidden'>
                <?php
                if (isset($redes_id)) {
                    echo $this->Form->hidden('redes_id', ['value' => $redes_id, 'id' => 'redes_id']);
                    echo $this->Form->input(
                        'redes_id',
                        [
                            'type' => 'text',
                            'readonly' => true,
                            'value' => $redes->toArray(),
                            'label' => 'Rede de destino'
                        ]
                    );
                } else {
                    echo $this->Form->input(
                        'redes_id',
                        [
                            'type' => 'select',
                            'class' => 'redes_list',
                            'options' => $redes,
                            'multiple' => false,
                            'empty' => true,
                            'label' => 'Rede de destino'
                        ]
                    );
                }
                ?>
            </div>

            <div class='col-lg-4 redes_input'>
                <?= $this->Form->input(
                    'clientes_id',
                    [
                        'type' => 'select',
                        'id' => 'clientes_rede',
                        'class' => 'clientes_rede',
                        'label' => 'Unidade da Rede'
                    ]
                )
                ?>
            </div>
            <?php

        } elseif ($usuarioLogadoTipoPerfil == PROFILE_TYPE_ADMIN_NETWORK) {
            ?>

            <div class='col-lg-6'>
                <?= $this->Form->input('tipo_perfil', [
                    'type' => 'select',
                    'options' =>
                    [
                        '' => '',
                        '1' => 'Administradores de uma Rede',
                        '3' => 'Administrador',
                        '4' => 'Gerente',
                        '5' => 'Funcionário',
                        '6' => 'Cliente Final',
                    ]
                ]); ?>
            </div>

            <div class='col-lg-6 redes_input'>

                <?= $this->Form->hidden('redes_id', ['value' => $redes_id, 'id' => 'redes_id']); ?>

                <?= $this->Form->input(
                    'clientes_id',
                    [
                        'type' => 'select',
                        'id' => 'clientes_rede',
                        'class' => 'clientes_rede',
                        'label' => 'Unidade da Rede'
                    ]
                );
                ?>

            </div>
            <?php

        } elseif ($usuarioLogadoTipoPerfil == PROFILE_TYPE_ADMIN_REGIONAL) {
            echo $this->Form->input('tipo_perfil', [
                'type' => 'select',
                'options' =>
                [
                    '' => '',
                    '2' => 'Administrador',
                    '3' => 'Gerente',
                    '4' => 'Funcionário',
                    '5' => 'Cliente Final',
                ]
            ]);
        } elseif ($usuarioLogadoTipoPerfil == PROFILE_TYPE_MANAGER) {
            echo $this->Form->input('tipo_perfil', [
                'type' => 'select',
                'options' =>
                [
                    '' => '',
                    '4' => 'Funcionário',
                    '5' => 'Cliente Final'
                ]
            ]);
        } elseif ($usuarioLogadoTipoPerfil == PROFILE_TYPE_WORKER) {
            echo $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => PROFILE_TYPE_MANAGER]);
        } else {
            echo $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => PROFILE_TYPE_USER]);
        }
    } else {
        echo $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => PROFILE_TYPE_USER]);
    }

    ?>
            <div class="form-group row">

                <div class="col-lg-12">

                    <?= $this->Form->input('estrangeiro', ['type' => 'checkbox', 'id' => 'alternarEstrangeiro', 'label' => 'Selecione se o usuário for estrangeiro']) ?>
                    <span id="doc_estrangeiro_validation" class="text-danger validation-message"></span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-12">
                    <span id="cpf_validation" class="text-danger validation-message"></span>
                </div>
                <div class="form-group col-lg-6" >
                    <div id="cpf_box">
                        <label
                            for="cpf">
                            CPF*
                        </label>
                        <input
                            name="cpf"
                            type="text"
                            id="cpf"
                            class="form-control"
                            required="required"
                            placeholder="CPF..." />
                    </div>
                    <div id="doc_estrangeiro_box">
                        <label for="doc_estrangeiro">Documento de Identificação Estrangeira*</label>
                        <input
                            type="text"
                            name="doc_estrangeiro"
                            id="doc_estrangeiro"
                            class="form-control"
                            placeholder="Documento Estrangeiro.."/>
                    </div>

                </div>

                <div class="form-group col-lg-6">
                    <label for="email">E-mail*</label>
                    <input
                        type="text"
                        name="email"
                        required="required"
                        id="email"
                        class="form-control"
                        placeholder="E-mail..."
                        />
                    <span id="email_validation" class="text-danger validation-message">
                </div>
            </div>



            <div class="group-video-capture col-lg-12">

                <div class="col-lg-5">
                    <div>
                        <span>Captura de Imagem</span>
                    </div>
                    <video id="video" autoplay="true" height="300"></video>

                    <div class="video-snapshot">
                        <div class="btn btn-primary" id="takeSnapshot">Tirar Foto</div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div>
                        <span>Foto Capturada</span>
                    </div>
                    <div class="video-captured">
                        <canvas width="400" height="300" id="canvas"></canvas>
                    </div>

                    <div class="video-confirm">
                        <div class="btn btn-primary" id="storeImage">Armazenar</div>
                    </div>

                </div>

            </div>

            <?= $this->Form->hidden('doc_invalido', ['id' => 'doc_invalido']) ?>

            <div class="form-group row">

                <div class="col-lg-5">


                    <label for="nome">Nome*</label>
                    <input
                        type="text"
                        name="nome"
                        required="required"
                        placeholder="Nome..."
                        id="nome"
                        class="form-control">
                </div>

                <div class="col-lg-3">
                    <?= $this->Form->input('sexo', array(
                        "placeholder" => "Sexo...",
                        "empty" => true,
                        "required" => true,
                        'options' =>
                        array(
                            '2' => 'Não informar',
                            '1' => 'Masculino',
                            '0' => 'Feminino'
                            )
                        )
                    ); ?>
                </div>

                <div class="col-lg-4">
                    <?= $this->Form->input(
                        'data_nasc',
                        [
                            'class' => 'datepicker-input',
                            'div' =>
                            [
                                'class' => 'form-inline',
                            ],
                            'type' => 'text',
                            'id' => 'data_nasc',
                            'format' => 'd/m/Y',
                            'default' => date('d/m/Y'),
                            // 'value' => date('d/m/Y'),
                            'value' => $this->DateUtil->dateToFormat($usuario["data_nasc"], "d/m/Y"),
                            'label' => 'Data de Nascimento'
                        ]
                    ); ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-3">

                    <?= $this->Form->input('necessidades_especiais', array(
                        "type" => "select",
                        'label' => 'Portador de Nec. Especiais?*',
                        'empty' => true,
                        "value" => -1,
                        'required' => true,
                        'placeholder' => "Necessidades Especiais...",
                        'options' =>
                            array(
                                "1" => 'Sim',
                                "0" => 'Não',
                            ),
                        )
                    ) ?>
                    </div>

                    <div class="col-lg-3">
                    <?= $this->Form->control('telefone'); ?>
                    </div>

                    <div class="col-lg-3">
                    <label for="senha">Senha*</label>
                    <input
                        type="password"
                        name="senha"
                        required="true"
                        maxLength="4"
                        id="senha"
                        class="form-control"/>
                    </div>

                    <div class="col-lg-3">
                    <label for="confirm_senha">Confirmar Senha*</label>
                    <input
                        type="password"
                        name="confirm_senha"
                        required="true"
                        maxLength="4"
                        id="confirm_senha"
                        class="form-control"/>
                    </div>
            </div>


            <div class="form-group row">
                <div class="col-lg-2">
                    <?= $this->Form->input(
                        'cep',
                        [
                            'label' => 'CEP',
                            'id' => 'cep',
                            'class' => 'cep',
                            'title' => 'CEP do local do cliente. Digite-o para realizar a busca.'
                        ]
                    );
                    ?>
                </div>

                <div class="col-lg-3">
                    <?= $this->Form->control('endereco', ['label' => 'Endereço', 'class' => 'endereco']); ?>
                </div>

                <div class="col-lg-2">
                    <?= $this->Form->control('endereco_numero', ['label' => 'Número', 'class' => 'numero']); ?>
                </div>
                <div class="col-lg-2">
                    <?= $this->Form->control('endereco_complemento', ['label' => 'Complemento', 'class' => 'complemento']); ?>
                </div>

                <div class="col-lg-3">

                    <?= $this->Form->control('bairro', ['class' => 'bairro']); ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-4">
                    <?= $this->Form->control('municipio', ['class' => 'municipio']); ?>
                </div>

                <div class="col-lg-4">
                    <?= $this->Form->input(
                        'estado',
                        [
                            'empty' => true,
                            'type' => 'select',
                            'options' => $this->Address->getStatesBrazil(),
                            'class' => 'estado'
                        ]
                    ); ?>

                </div>

                <div class="col-lg-4">
                        <?= $this->Form->control('pais', ['class' => 'pais']); ?>
                </div>
            </div>

            <?php if ($usuarioLogadoTipoPerfil != (int)Configure::read('profileTypes')['WorkerProfileType']) : ?>
            <div class="fields-is-final-customer ">
                <?php else : ?>
                <div>
                    <?php endif; ?>

                    <?php if (!is_null($usuarioLogado)) : ?>
                    <?= $this->Element('../Veiculos/veiculos_form'); ?>

                    <div class="col-lg-12">
                        <?= $this->Form->control('transportadora', ['type' => 'checkbox', 'id' => 'alternarTransportadora', 'label' => 'Marque se é de Transportadora', 'value' => 0]) ?>
                    </div>
                    <br />
                    <div class="form-group">
                        <?php
                        echo $this->Element('../Transportadoras/transportadoras_form');
                        ?>
                    </div>

                    <?php endif; ?>
                </div>
        </fieldset>
        <div class="form-group row">
            <div class="col-lg-12 text-right">

                <div style="display: inline-flex;" class="form-add-buttons">
                    <div class="sendDiv">
                        <div class="col-lg-12 text-right">
                            <button type="submit" class="btn btn-primary botao-confirmar" id="user_submit">
                                <i class="fa fa-save"></i>
                                Salvar
                            </button>

                            <?php if ($usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["AdminDeveloperProfileType"]) : ?>
                            <a href="/usuarios/index/" class="btn btn-danger botao-cancelar">
                                <i class="fa fa-window-close"></i>
                                Cancelar
                            </a>
                            <?php  ?>
                            <?php elseif ($usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["WorkerProfileType"]) : ?>
                            <a href="/" class="btn btn-danger botao-cancelar">
                                <i class="fa fa-window-close"></i>
                                Cancelar
                            </a>
                            <?php endif; ?>
                            <!-- <?= $this->Form->button(
                                        __(
                                            '{0} Salvar',
                                            $this->Html->tag('i', '', ['class' => 'fa fa-save'])
                                        ),
                                        [
                                            'id' => 'user_submit',
                                            'escape' => false
                                        ]
                                    ) ?> -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<?php
    $extension = Configure::read("debug") ? ""  : ".min";
// $this->fetch('script');
?>

<script src="<?php echo "/webroot/js/scripts/usuarios/add".$extension.".js" ?>"></script>
<link rel="stylesheet" href="<?= "/webroot/css/styles/usuarios/usuario_form".$extension.".css" ?>">
