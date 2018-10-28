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

                if ($usuarioLogadoTipoPerfil == Configure::read('profileTypes')['AdminDeveloperProfileType']) {


                    ?> 
                <div class='col-lg-4'>
                    <?php 
                    if (isset($redes_id)) {
                        echo $this->Form->input('tipo_perfil', [
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
                        ]);
                    } else {
                        echo $this->Form->input('tipo_perfil', [
                            'type' => 'select',
                            'options' =>
                                [
                                '' => '',
                                '0' => 'Administradores da RTI / Desenvolvedor',
                                '1' => 'Administradores de uma Rede',
                                '3' => 'Administrador',
                                '4' => 'Gerente',
                                '5' => 'Funcionário',
                                '6' => 'Cliente Final',

                            ]
                        ]);
                    }
                    

                    ?> 
                    </div>
                    <div class='col-lg-4 redes_input'>
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

            } elseif ($usuarioLogadoTipoPerfil == Configure::read('profileTypes')['AdminNetworkProfileType']) {
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
            } elseif ($usuarioLogadoTipoPerfil == Configure::read('profileTypes')['AdminRegionalProfileType']) {
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
            } elseif ($usuarioLogadoTipoPerfil == Configure::read('profileTypes')['ManagerProfileType']) {
                echo $this->Form->input('tipo_perfil', [
                    'type' => 'select',
                    'options' =>
                        [
                        '' => '',
                        '4' => 'Funcionário',
                        '5' => 'Cliente Final'
                    ]
                ]);
            } elseif ($usuarioLogadoTipoPerfil == Configure::read('profileTypes')['WorkerProfileType']) {
                echo $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => Configure::read('profileTypes')['WorkerProfileType']]);
            } else {
                echo $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => Configure::read('profileTypes')['UserProfileType']]);

            }
        } else {
            echo $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => Configure::read('profileTypes')['UserProfileType']]);

        }

        ?>

            <div class="col-lg-12">

                <?= $this->Form->input('estrangeiro', ['type' => 'checkbox', 'id' => 'alternarEstrangeiro', 'label' => 'Selecione se o usuário for estrangeiro']) ?>

                <div id="doc_estrangeiro_box">

                    <?= $this->Form->input('doc_estrangeiro', ['id' => 'doc_estrangeiro', 'label' => 'Documento de Identificação Estrangeira']) ?>
                    
                    <span id="doc_estrangeiro_validation" class="text-danger validation-message"></span>

                </div>

            </div>

            <div class="col-lg-12">
                <span id="cpf_validation" class="text-danger validation-message"></span>
            </div>
            <div class="form-group col-lg-6" id="cpf_box">
                <?php
                echo $this->Form->input('cpf', ['label' => 'CPF']);
                ?>
                
            </div>

            <div class="form-group col-lg-6">
                <?= $this->Form->input('email'); ?>
                    <span id="email_validation" class="text-danger validation-message">
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

            <div class="col-lg-5">

                <?= $this->Form->control('nome'); ?>
            </div>

            <div class="col-lg-3">
                <?= $this->Form->input('sexo', [
                    'options' =>
                        [
                        '' => '',
                        '1' => 'Masculino',
                        '0' => 'Feminino'
                    ]
                ]); ?>
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
                            'value' => date('d/m/Y'),
                            'label' => 'Data de Nascimento'
                        ]
                    ); ?>
            </div>

            <div class="col-lg-3">
                
                <?= $this->Form->input('necessidades_especiais', ['label' => 'Portador de Nec. Especiais? ', 'options' => [
                    '' => '',
                    1 => 'Sim',
                    0 => 'Não',
                ]]) ?>
            </div>
            
            <div class="col-lg-3">
                <?= $this->Form->control('telefone'); ?>
            </div>

            <div class="col-lg-3">
                <?= $this->Form->input('senha', ['type' => 'password', 'required' => true, 'autofocus' => true, 'maxLength' => 4]); ?>
            </div>

            <div class="col-lg-3">
                <?= $this->Form->input('confirm_senha', ['type' => 'password', 'required' => true, 'label' => 'Confirmar Senha', 'maxLength' => 4]); ?>
            </div>


            <div class="col-lg-2">
                <?= $this->Form->input(
                    'cep',
                    [
                        'label' => 'CEP*',
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
        <div class="col-lg-12">

            <div style="display: inline-flex;" class="form-add-buttons">
                <div class="sendDiv">
                    <?= $this->Form->button(
                        __(
                            '{0} Salvar',
                            $this->Html->tag('i', '', ['class' => 'fa fa-save'])
                        ),
                        [
                            'id' => 'user_submit',
                            'escape' => false
                        ]
                    ) ?>
            </div>
            
        </div>
        </div>
    </div>
        
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/usuarios/add'); ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/usuarios/add.min'); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>
