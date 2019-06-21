<?php


use Cake\Core\Configure;
use Cake\Routing\Router;

$senhaObrigatoriaEdicao = isset($senhaObrigatoriaEdicao) ? $senhaObrigatoriaEdicao : false;

?>
<fieldset>
    <legend>
        <?= __('Editar Usuario') ?>
    </legend>

    <div class="form-group row">

        <?= $this->Form->hidden('id', ['id' => 'usuarios_id']); ?>
        <?= $this->Form->hidden('usuarioLogadoTipoPerfil', ['value' => $usuarioLogadoTipoPerfil, 'class' => 'usuarioLogadoTipoPerfil']); ?>

        <?= $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => (int)Configure::read('profileTypes')['UserProfileType']]) ?>

        <div class="col-lg-12">
            <?= $this->Form->input('estrangeiro', ['type' => 'checkbox', 'id' => 'alternarEstrangeiro', 'label' => 'Selecione se o usuário for estrangeiro']) ?>
        </div>

        <div class="form-group col-lg-6" id="cpf_box">
            <?php
            echo $this->Form->input('cpf', ['label' => 'CPF']);
            ?>
            <span id="cpf_validation" class="text-danger validation-message"></span>

        </div>

        <div id="doc_estrangeiro_box" class="col-lg-6">
            <?= $this->Form->input('doc_estrangeiro', ['id' => 'doc_estrangeiro', 'label' => 'Documento de Identificação Estrangeira']) ?>
            <br />
            <span id="doc_estrangeiro_validation" class="text-danger validation-message"></span>
        </div>

        <div class="form-group col-lg-6">
            <?= $this->Form->input('email'); ?>
            <span id="email_validation" class="text-danger validation-message">
        </div>

    </div>


    <!-- <div class="group-video-capture col-lg-12">

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

        </div> -->

    <div class="form-group row">
        <?= $this->Form->hidden('doc_invalido', ['id' => 'doc_invalido']) ?>

        <div class="col-lg-5">

            <?= $this->Form->control('nome'); ?>
        </div>

        <div class="col-lg-3">
            <?= $this->Form->input('sexo', [
                'options' =>
                [
                    '2' => 'Não informar',
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
                    'value' => $this->DateUtil->dateToFormat($usuario["data_nasc"], "d/m/Y"),
                    'label' => 'Data de Nascimento'
                ]
            ); ?>
        </div>

    </div>

    <div class="form-group row">
        <div class="col-lg-3">
            <?= $this->Form->input(
                'necessidades_especiais',
                array(
                    'label' => 'Portador de Nec. Especiais? ',
                    'empty' => null,
                    'options' => array(
                        1 => 'Sim',
                        0 => 'Não',
                    )
                )
            ) ?>
        </div>

        <div class="col-lg-3">
            <?= $this->Form->control('telefone'); ?>
        </div>

    </div>

    <div class="form-group row">
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

    <?php if ($senhaObrigatoriaEdicao): ?>
        <div class="form-group row">
            <div class="col-lg-4">
                <label for="senha">Senha de Confirmação do Usuário*</label>
                <input type="password"
                    required="required"
                    name="senha"
                    maxlength="4"
                    id="senha"
                    placeholder="Senha de Confirmação do Usuário..."
                    class="form-control"
                    title="Informe a senha do usuário para continuar">
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <button type="submit" class="btn btn-primary botao-confirmar"><i class="fa fa-save"> </i> Salvar</button>

            <?php
                    // @todo Ajustar/Verificar Outros níveis
            ?>
            <?php if ($usuarioLogado["tipo_perfil"] >= PROFILE_TYPE_ADMIN_NETWORK && $usuarioLogado["tipo_perfil"] <= PROFILE_TYPE_MANAGER): ?>
                <a href="/usuarios/meus_clientes" class="btn btn-danger botao-cancelar"> <i class="fa fa-window-close"></i> Cancelar</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- <div class="col-lg-2 text-right">

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

        </div> -->



</fieldset>
