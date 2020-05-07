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
 

    <?= $this->Form->hidden('id', ['id' => 'usuarios_id']); ?>
    <?= $this->Form->hidden('clientes_id', ['id' => 'clientes_id', 'value' => isset($clientesId) ? $clientesId : null]); ?>
    <?= $this->Form->hidden('usuarioLogadoTipoPerfil', ['value' => $usuarioLogadoTipoPerfil, 'class' => 'usuarioLogadoTipoPerfil']); ?>

    <div class="form-group row">

        <?php if ($usuarioLogadoTipoPerfil == PROFILE_TYPE_ADMIN_DEVELOPER) : ?>
            <div class='col-lg-4'>
                <label for="tipo_perfil">Tipo de Perfil*</label>
                <?php
                    $listaPerfis = array();
                    $perfis = array();

                    if (!isset($redesId)) {
                        // $listaPerfis[] = array(0 => "Administradores da RTI / Desenvolvedor");
                        $perfis[0] = "Administradores da RTI / Desenvolvedor";
                    }

                    $perfis[1] = 'Administradores de uma Rede';
                    $perfis[3] = 'Administrador';
                    $perfis[4] = 'Gerente';
                    $perfis[5] = 'Funcionário';
                    $listaPerfis = $perfis;
                    ?>
                <?= $this->Form->input('tipo_perfil', [
                        'type' => 'select',
                        'id' => 'tipo_perfil',
                        "empty" => true,
                        "placeholder" => "Tipo de Perfil...",
                        "autofocus",
                        "required" => "required",
                        "label" => false,
                        'options' => $listaPerfis,
                        'attributes' => array("autofocus")
                    ]); ?>

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
                                "id" => "redes_id",
                                'readonly' => true,
                                'value' => $redes->toArray(),
                                'label' => 'Rede de Destino*'
                            ]
                        );
                    } else {
                        echo $this->Form->input(
                            'redes_id',
                            [
                                'type' => 'select',
                                "id" => "redes_id",
                                'class' => 'redes_list',
                                'options' => $redes,
                                'multiple' => false,
                                'empty' => true,
                                'label' => 'Rede de Destino*',
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
                            'label' => 'Unidade da Rede*',
                            "options" => $unidadesRede,
                            'value' => $unidadeRedeId
                        ]
                    )
                    ?>
            </div>
        <?php elseif (($usuarioLogadoTipoPerfil == PROFILE_TYPE_ADMIN_NETWORK)
            && ($usuarioLogado["id"] !== $usuario["id"])
        ) : ?>

            <div class='col-lg-6'>
                <!-- Tipo Perfil -->
                <label for="tipo_perfil">Tipo de Perfil*</label>
                <?= $this->Form->input('tipo_perfil', [
                        'type' => 'select',
                        'id' => 'tipo_perfil',
                        "autofocus",
                        "required" => "required",
                        "label" => false,
                        "empty" => true,
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
        <?php elseif ($usuarioLogadoTipoPerfil == PROFILE_TYPE_ADMIN_REGIONAL) : ?>

            <div class="col-lg-6">
                <!-- Tipo Perfil -->
                <label for="tipo_perfil">Tipo de Perfil*</label>
                <?= $this->Form->input('tipo_perfil', [
                        'type' => 'select',
                        'id' => 'tipo_perfil',
                        "empty" => true,
                        "label" => false,
                        "autofocus",
                        "required" => "required",
                        "value" => $usuario["tipo_perfil"],
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
                            'label' => 'Unidade da Rede*',
                            "empty" => true,
                            "options" => $unidadesRede,
                            "required" => "required",
                            "value" => $unidadeRedeId
                        ]
                    ); ?>
            </div>
        <?php elseif ($usuarioLogadoTipoPerfil == PROFILE_TYPE_ADMIN_LOCAL) : ?>
            <div class="col-lg-12">
                <!-- Tipo Perfil -->
                <label for="tipo_perfil">Tipo de Perfil*</label>
                <?= $this->Form->input('tipo_perfil', [
                        'type' => 'select',
                        'id' => 'tipo_perfil',
                        "label" => false,
                        "required" => "required",
                        "autofocus",
                        "empty" => true,
                        "value" => $usuario["tipo_perfil"],
                        'options' =>
                        array(
                            4 => 'Gerente',
                            5 => 'Funcionário',
                        )
                    ]) ?>
            </div>
        <?php elseif ($usuarioLogadoTipoPerfil >= Configure::read('profileTypes')['ManagerProfileType']) : ?>
            <!-- Tipo Perfil -->
            <?= $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => PROFILE_TYPE_WORKER]) ?>
        <?php endif; ?>
    </div>


    <div class="form-group row">
        <!-- Email -->
        <div class="col-lg-4">
            <label for="email">E-mail*</label>
            <input type="text" name="email" required="required" id="email" class="form-control email" placeholder="E-mail..." value="<?= $usuario['email'] ?>" autofocus>
            <span id="email_validation" class="text-danger validation-message">
        </div>
        <!-- nome -->
        <div class="col-lg-4">
            <label for="nome">Nome*</label>
            <input type="text" placeholder="Nome" name="nome" id="nome" value="<?= $usuario['nome'] ?>" class="form-control" required />
        </div>
        <!-- telefone -->
        <div class="col-lg-4">
            <?php if ($usuario["tipo_perfil"] != PROFILE_TYPE_WORKER) : ?>
                <label for="telefone" id="label-telefone">Telefone*</label>
                <input type="text" placeholder="Telefone" name="telefone" id="telefone" value="<?= $usuario['telefone'] ?>" class="form-control" required="required" />
            <?php else : ?>
                <label for="telefone" id="label-telefone">Telefone</label>
                <input type="text" placeholder="Telefone" name="telefone" id="telefone" value="<?= $usuario['telefone'] ?>" class="form-control" />
            <?php endif; ?>
        </div>
    </div>
    <?php if ($mode == "add") : ?>
        <div class="form-group row">
            <!-- Senha -->
            <div class="col-lg-6">
                <label for="senha">Senha*</label>
                <input type="password" placeholder="Senha..." name="senha" id="senha" minlength="8" value="<?= $usuario['senha'] ?>" class="form-control" required="required" />
            </div>
            <div class="col-lg-6">
                <!-- Confirmar Senha -->
                <label for="confirm_senha">Confirmar Senha*</label>
                <input type="password" placeholder="Confirmar Senha..." name="confirm_senha" id="confirm_senha" value="<?= $usuario['confirm_senha'] ?>" minlength="8" class="form-control" required="required" />
            </div>
        </div>
    <?php endif; ?>

</fieldset>
<div class="form-group row">
    <div class="col-lg-12 text-right">
        <button type="submit" class="btn btn-primary botao-confirmar"><i class="fa fa-save"> </i> Salvar</button>

        <a href="/usuarios/usuarios-rede/<?php echo $redesId; ?> " class="btn btn-danger botao-cancelar">
            <i class="fa fa-window-close">
            </i>
            Cancelar
        </a>
    </div>

</div>
<?= $this->Form->end() ?>
