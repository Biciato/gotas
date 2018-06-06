<?php 


use Cake\Core\Configure;
use Cake\Routing\Router;

?>
  <fieldset>
        <legend><?= __('Editar Usuario') ?></legend>
        
            <?= $this->Form->hidden('id', ['id' => 'usuarios_id']); ?>
            <?= $this->Form->hidden('usuario_logado_tipo_perfil', ['value' => $usuario_logado_tipo_perfil, 'class' => 'usuario_logado_tipo_perfil']); ?>
            
            <?= $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => (int)Configure::read('profileTypes')['UserProfileType']]) ?>

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

            <div class="row col-lg-12">
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

            <div class="col-lg-2">

                <div class="form-add-buttons">
                    <div class="sendDiv">
                        <?= $this->Form->button(
                            __(
                                '{0} Salvar',
                                $this->Html->tag('i', '', ['class' => 'fa fa-save'])
                            ),
                            [
                                'id' => 'user_submit',
                                'class' => 'btn btn-primary btn-block',
                                'escape' => false
                            ]
                        ) ?>
                </div>

            </div>

            <div class="col-lg-10">
            </div>
    </fieldset>