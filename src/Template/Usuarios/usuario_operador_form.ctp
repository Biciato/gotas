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
    <?= $this->Form->hidden('usuarioLogadoTipoPerfil', ['value' => $usuarioLogadoTipoPerfil, 'class' => 'usuarioLogadoTipoPerfil']); ?>

    <div class="form-group row">

        <?php if ($usuarioLogadoTipoPerfil == Configure::read('profileTypes')['AdminDeveloperProfileType']) : ?>
            <div class='col-lg-4'>
                <?php if (isset($redesId)) : ?>
                    <?= $this->Form->input('tipo_perfil', [
                        'type' => 'select',
                        'id' => 'tipo_perfil',
                        "empty" => "<Selecionar>",
                        'options' =>
                            array(
                            1 => 'Administradores de uma Rede',
                            3 => 'Administrador',
                            4 => 'Gerente',
                            5 => 'Funcionário'
                        )
                    ]); ?>
                <?php else : ?>
                    <div class="col-lg-12">
                        <?= $this->Form->input('tipo_perfil', [
                            'type' => 'select',
                            'id' => 'tipo_perfil',
                            "empty" => "<Selecionar>",
                            'options' =>
                                array(
                                0 => 'Administradores da RTI / Desenvolvedor',
                                1 => 'Administradores de uma Rede',
                                3 => 'Administrador',
                                4 => 'Gerente',
                                5 => 'Funcionário'
                            )
                        ]); ?>
                    </div>

                <?php endif; ?>
                <!-- Deve exibir todas as Redes e Unidades -->

                <!-- ?> -->
            </div>
            <div class='col-lg-4 redes_input'>
                <?php if (isset($redesId)) {
                    echo $this->Form->hidden('redes_id', ['value' => $redesId, 'id' => 'redes_id']);
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
                        'label' => 'Unidade da Rede',
                        "options" => $unidadesRede,
                        'value' => $unidadeRedeId
                    ]
                )
                ?>
            </div>
            <?php elseif (($usuarioLogadoTipoPerfil == Configure::read('profileTypes')['AdminNetworkProfileType'])
                && ($usuarioLogado["id"] !== $usuario["id"])) : ?>

                <div class='col-lg-6'>
                    <!-- Tipo Perfil -->
                    <?= $this->Form->input('tipo_perfil', [
                        'type' => 'select',
                        'id' => 'tipo_perfil',
                        "empty" => "<Selecionar>",
                        'options' =>
                            [
                            1 => 'Administradores de uma Rede',
                            3 => 'Administrador',
                            4 => 'Gerente',
                            5 => 'Funcionário',

                        ]
                    ]); ?>
                </div>
                <div class='col-lg-6 '>

                    <?= $this->Form->hidden('redes_id', ['value' => $redesId, 'id' => 'redes_id']); ?>

                    <?= $this->Form->input(
                        'clientes_id',
                        [
                            'type' => 'select',
                            'id' => 'clientes_rede',
                            'class' => 'clientes_rede',
                            'label' => 'Unidade da Rede',
                            "options" => $unidadesRede,
                            'value' => $unidadeRedeId
                        ]
                    ); ?>

                </div>
                    <?php elseif ($usuarioLogadoTipoPerfil == Configure::read('profileTypes')['AdminRegionalProfileType']) : ?>
                        <div class="col-lg-6">
                            <!-- Tipo Perfil -->
                            <?= $this->Form->input('tipo_perfil', [
                                'type' => 'select',
                                'id' => 'tipo_perfil',
                                "empty" => "<Selecionar>",
                                "value" => $usuario["cliente_has_usuario"]["tipo_perfil"],
                                'options' =>
                                    [
                                    3 => 'Administrador',
                                    4 => 'Gerente',
                                    5 => 'Funcionário',

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
                    <?php elseif ($usuarioLogadoTipoPerfil == Configure::read('profileTypes')['AdminLocalProfileType']) : ?>
                        <div class="col-lg-12">
                            <!-- Tipo Perfil -->
                            <?= $this->Form->input('tipo_perfil', [
                                'type' => 'select',
                                'id' => 'tipo_perfil',
                                "empty" => "<Selecionar>",
                                "value" => $usuario["cliente_has_usuario"]["tipo_perfil"],
                                'options' =>
                                    array(
                                    4 => 'Gerente',
                                    5 => 'Funcionário',
                                )
                            ]) ?>
                        </div>
                    <?php elseif ($usuarioLogadoTipoPerfil >= Configure::read('profileTypes')['ManagerProfileType']) : ?>
                        <!-- Tipo Perfil -->
                        <?= $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => '5']) ?>
                    <?php endif; ?>
    </div>

    <?php if ($mode == 'add') : ?>

    <div class="form-group row">
        <!-- Email -->
        <div class="col-lg-4">
            <?= $this->Form->input('email', array("label" => "Email*")); ?>
                <span id="email_validation" class="text-danger validation-message">
        </div>
        <!-- nome -->
        <div class="col-lg-4">

            <?= $this->Form->control('nome', array("label" => "Nome*")); ?>
        </div>
        <!-- telefone -->
        <div class="col-lg-4">
            <label for="telefone" id="label-telefone">Telefone*</label>
            <?= $this->Form->control('telefone', array("label" => false, "id" => "telefone", "required", )); ?>
        </div>
        <!-- Senha -->
        <div class="col-lg-6">
            <?= $this->Form->input('senha', array("label" => "Senha*", 'type' => 'password', 'required' => true, 'autofocus' => true)); ?>
        </div>
        <!-- Confirmar Senha -->
        <div class="col-lg-6">
            <?= $this->Form->input('confirm_senha', array("label" => "Confirmar Senha*", 'type' => 'password', 'required' => true)); ?>
        </div>
    </div>

    <?php else : ?>

        <div class="row">
            <div class="col-lg-4">
                <!-- Email -->
                <?= $this->Form->input('email'); ?>
                    <span id="email_validation" class="text-danger validation-message">
            </div>
               <!-- nome -->
            <div class="col-lg-4">
                <label for="nome">Nome*</label>
                <input type="text" placeholder="Nome" name="nome" id="nome" value="<?= $usuario['nome'] ?>" class="form-control" required/>
            </div>
                <!-- telefone -->
            <div class="col-lg-4">
                <label for="telefone" id="label-telefone">Telefone*</label>
                <input type="text" 
                    placeholder="Telefone" 
                    name="telefone" 
                    id="telefone" 
                    value="<?= $usuario['telefone'] ?>" 
                    class="form-control" 
                    required/>
                
            </div>
            </div>
    <?php endif; ?>


</fieldset>
<div class="form-group row">
    <div class="col-lg-12 text-right">
        <button type="submit" class="btn btn-primary botao-confirmar"><i class="fa fa-save"> </i> Salvar</button>

        <a href="/usuarios/usuarios-rede/<?php echo $redesId; ?> " class="btn btn-danger "> <i class="fa fa-window-close"></i> Cancelar</a>
    </div>

</div>
<?= $this->Form->end() ?>


