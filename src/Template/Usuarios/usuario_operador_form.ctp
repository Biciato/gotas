<?php

/**
 * @description Arquivo para formulário de cadastro de operadores (administradores, gerentes, funcionários)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/usuario_operador_form.ctp
 * @date        27/12/2017
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

?>
<?= $this->Form->create($usuario) ?>
<fieldset>
    <legend>
        <?= __("{0} Conta", $title) ?>
    </legend>

    <?= $this->Form->hidden('id', ['id' => 'usuarios_id']); ?>
    <?= $this->Form->hidden('clientes_id', ['id' => 'clientes_id', 'value' => isset($clientes_id) ? $clientes_id : null]); ?>
    <?= $this->Form->hidden('usuario_logado_tipo_perfil', ['value' => $usuario_logado_tipo_perfil, 'class' => 'usuario_logado_tipo_perfil']); ?>

    <div class="form-group row">

        <?php if ($usuario_logado_tipo_perfil == Configure::read('profileTypes')['AdminDeveloperProfileType']) : ?>
            <div class='col-lg-4'>
                <?php if (isset($redes_id)) : ?>
                    <?= $this->Form->input('tipo_perfil', [
                        'type' => 'select',
                        'id' => 'tipo_perfil',
                        'options' =>
                            [
                            '' => '',
                            '1' => 'Administradores de uma Rede',
                            '3' => 'Administrador',
                            '4' => 'Gerente',
                            '5' => 'Funcionário'

                        ]
                    ]); ?>
                <?php else : ?>
                    <div class="col-lg-12">
                        <?= $this->Form->input('tipo_perfil', [
                            'type' => 'select',
                            'id' => 'tipo_perfil',
                            'options' =>
                                [
                                '' => '',
                                '0' => 'Administradores da RTI / Desenvolvedor',
                                '1' => 'Administradores de uma Rede',
                                '3' => 'Administrador',
                                '4' => 'Gerente',
                                '5' => 'Funcionário',
                            ]
                        ]); ?>
                    </div>

                <?php endif; ?>
                <!-- Deve exibir todas as Redes e Unidades -->

                <!-- ?> -->
            </div>
            <div class='col-lg-4 redes_input'>
                <?php if (isset($redes_id)) {
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
                <!-- Clientes Id -->
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
            <?php elseif (($usuario_logado_tipo_perfil == Configure::read('profileTypes')['AdminNetworkProfileType'])
            && ($user_logged["id"] !== $usuario["id"])) : ?>

                <div class='col-lg-6'>
                    <!-- Tipo Perfil -->
                    <?= $this->Form->input('tipo_perfil', [
                        'type' => 'select',
                        'id' => 'tipo_perfil',
                        'options' =>
                            [
                            '' => '',
                            '1' => 'Administradores de uma Rede',
                            '3' => 'Administrador',
                            '4' => 'Gerente',
                            '5' => 'Funcionário',

                        ]
                    ]); ?>
                </div>
                <div class='col-lg-6 '>

                    <?= $this->Form->hidden('redes_id', ['value' => $redes_id, 'id' => 'redes_id']); ?>

                    <?= $this->Form->input(
                        'clientes_id',
                        [
                            'type' => 'select',
                            'id' => 'clientes_rede',
                            'class' => 'clientes_rede',
                            'label' => 'Unidade da Rede'
                        ]
                    ); ?>

                </div>
                    <?php elseif ($usuario_logado_tipo_perfil == Configure::read('profileTypes')['AdminRegionalProfileType']) : ?>
                        <div class="col-lg-6">
                            <!-- Tipo Perfil -->
                            <?= $this->Form->input('tipo_perfil', [
                                'type' => 'select',
                                'id' => 'tipo_perfil',
                                'options' =>
                                    [
                                    '' => '',
                                    '2' => 'Administrador',
                                    '3' => 'Gerente',
                                    '4' => 'Funcionário',

                                ]
                            ]) ?>
                        </div>
                        <div class="col-lg-6">
                            <?= $this->Form->input(
                            'clientes_id',
                            [
                                'type' => 'select',
                                'id' => 'clientes_rede',
                                'class' => 'clientes_rede',
                                'label' => 'Unidade da Rede',
                                "empty" => "<Selecionar>",
                                "options" => $unidadesRede,
                                "required" => true,
                                "value" => $unidadeRedeId
                            ]
                        ); ?>
                        </div>
                    <?php elseif ($usuario_logado_tipo_perfil == Configure::read('profileTypes')['AdminLocalProfileType']) : ?>
                        <div class="col-lg-12">
                            <!-- Tipo Perfil -->
                            <?= $this->Form->input('tipo_perfil', [
                                'type' => 'select',
                                'id' => 'tipo_perfil',
                                'options' =>
                                    [
                                    '' => '',
                                    '3' => 'Gerente',
                                    '4' => 'Funcionário',

                                ]
                            ]) ?>
                        </div>
                    <?php elseif ($usuario_logado_tipo_perfil >= Configure::read('profileTypes')['ManagerProfileType']) : ?>
                        <!-- Tipo Perfil -->
                        <?= $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => '5']) ?>
                    <?php endif; ?>
    </div>

    <?php if ($mode == 'add') : ?>

    <div class="form-group row">
        <!-- Email -->
        <div class="col-lg-6">
            <?= $this->Form->input('email'); ?>
                <span id="email_validation" class="text-danger validation-message">
        </div>
        <!-- Senha -->
        <div class="col-lg-3">
            <?= $this->Form->input('senha', ['type' => 'password', 'required' => true, 'autofocus' => true]); ?>
        </div>
        <!-- Confirmar Senha -->
        <div class="col-lg-3">
            <?= $this->Form->input('confirm_senha', ['type' => 'password', 'required' => true, 'label' => 'Confirmar Senha']); ?>
        </div>
    </div>

    <?php else : ?>

    <div class="row">
        <div class="col-lg-12">
            <!-- Email -->
            <?= $this->Form->input('email'); ?>
                <span id="email_validation" class="text-danger validation-message">
        </div>
    </div>
    <?php endif; ?>

    <div class="form-group row">
        <div class="col-lg-4">

            <?= $this->Form->control('nome'); ?>
        </div>

        <div class="col-lg-2">
            <?= $this->Form->input('sexo', [
                'options' =>
                    [
                    '' => '',
                    '1' => 'Masculino',
                    '0' => 'Feminino'
                ]
            ]); ?>
        </div>

        <div class="col-lg-3">
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
            <?= $this->Form->control('telefone'); ?>
        </div>
    </div>


</fieldset>
    <div class="col-lg-12 form-group row">

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
<?= $this->Form->end() ?>


