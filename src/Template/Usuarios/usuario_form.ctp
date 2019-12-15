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
use App\Custom\RTI\DebugUtil;

$show_menu = isset($show_menu) ? $show_menu : true;
$show_tipo_perfil = isset($show_tipo_perfil) ? $show_tipo_perfil : true;
$show_veiculos = isset($show_veiculos) ? $show_veiculos : false;
$usuarioLogadoTipoPerfil = isset($usuarioLogadoTipoPerfil) ? $usuarioLogadoTipoPerfil : (int) Configure::read('profileTypes')['UserProfileType'];

$listaPerfisRedirecionarCancelar = isset($listaPerfisRedirecionarCancelar) ? $listaPerfisRedirecionarCancelar : array();

?>

<?php
// if (isset($usuarioLogado)) :
if (isset($usuarioLogado) && $usuarioLogado->tipo_perfil != PROFILE_TYPE_WORKER) :
    ?>
    <div class="usuarios form col-lg-12 col-md-8 columns content">
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
                <div class="form-group col-lg-6">
                    <div id="cpf_box">
                        <label for="cpf">
                            CPF*
                        </label>
                        <input name="cpf" type="text" id="cpf" class="form-control" required="required" placeholder="CPF..." />
                    </div>
                    <div id="doc_estrangeiro_box">
                        <label for="doc_estrangeiro">Documento de Identificação Estrangeira*</label>
                        <input type="text" name="doc_estrangeiro" id="doc_estrangeiro" class="form-control" placeholder="Documento Estrangeiro.." />
                    </div>

                </div>

                <div class="form-group col-lg-6">
                    <label for="email">E-mail</label>
                    <input type="text" name="email" id="email" class="form-control" placeholder="E-mail..." />
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
                    <input type="text" name="nome" required="required" placeholder="Nome..." id="nome" class="form-control">
                </div>

                <div class="col-lg-3">
                    <?= $this->Form->input(
                            'sexo',
                            array(
                                "placeholder" => "Sexo*...",
                                "empty" => true,
                                "label" => "Sexo*",
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

                    <?= $this->Form->input(
                            'necessidades_especiais',
                            array(
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
                    <label for="telefone">Telefone</label>
                    <input type="telefone" name="telefone" id="telefone" placeholder="Telefone..." class="form-control" value="<?= $usuario['telefone'] ?>">
                </div>

                <div class="col-lg-3">
                    <label for="senha">Senha*</label>
                    <input type="password" name="senha" required="true" minLength="6" placeholder="Senha..." id="senha" class="form-control" />
                </div>

                <div class="col-lg-3">
                    <label for="confirm_senha">Confirmar Senha*</label>
                    <input type="password" name="confirm_senha" required="true" placeholder="Confirmar Senha..." minLength="6" id="confirm_senha" class="form-control" />
                </div>
            </div>


            <div class="form-group row">
                <div class="col-lg-2">
                    <label for="cep">CEP</label>
                    <input type="text" name="cep" placeholder="CEP..." id="cep" class="form-control cep" value="<?= $usuario['cep'] ?>">

                </div>

                <div class="col-lg-3">
                    <label for="endereco">Endereço</label>
                    <input type="text" name="endereco" id="endereco" class="form-control endereco" placeholder="Endereço..." value="<?= $usuario['endereco'] ?>">

                </div>

                <div class="col-lg-2">
                    <label for="endereco_numero">Número</label>
                    <input type="text" name="endereco_numero" id="endereco_numero" class="form-control numero" placeholder="Número..." value="<?= $usuario['endereco_numero'] ?>" />
                </div>
                <div class="col-lg-2">
                    <label for="endereco_complemento">Complemento</label>
                    <input type="text" name="endereco_complemento" id="endereco_complemento" class="form-control complemento" placeholder="Complemento..." value="<?= $usuario['endereco_complemento'] ?>" />
                </div>

                <div class="col-lg-3">
                    <label for="bairro">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control bairro" placeholder="Bairro..." value="<?= $usuario['bairro'] ?>" />
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-4">
                    <label for="municipio">Municipio</label>
                    <input type="text" name="municipio" id="municipio" class="form-control municipio" placeholder="Municipio..." value="<?= $usuario['bairro'] ?>" />
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
                    <label for="pais">País</label>
                    <input type="text" name="pais" id="pais" class="form-control pais" placeholder="País..." value="<?= $usuario['pais'] ?>" />
                </div>
            </div>

            <?php if ($usuarioLogadoTipoPerfil != PROFILE_TYPE_WORKER) : ?>
                <div class="fields-is-final-customer">
                <?php else : ?>
                    <div class="">
                    <?php endif; ?>

                    <?php if (!is_null($usuarioLogado)) : ?>

                        <?= $this->Element('../Veiculos/veiculos_form'); ?>

                        <div class="form-group row">
                            <div class="col-lg-12">
                                <?= $this->Form->control('transportadora', ['type' => 'checkbox', 'id' => 'alternarTransportadora', 'label' => 'Marque se é de Transportadora', 'value' => 0]) ?>
                            </div>
                        </div>
                        <br />
                        <?php
                                echo $this->Element('../Transportadoras/transportadoras_form');
                                ?>

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
                            <?php elseif (in_array($usuarioLogado["tipo_perfil"], array(PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL, PROFILE_TYPE_ADMIN_LOCAL, PROFILE_TYPE_MANAGER))) : ?>
                                <a href="/usuarios/meusClientes/" class="btn btn-danger botao-cancelar">
                                    <i class="fa fa-window-close"></i>
                                    Cancelar
                                </a>
                            <?php elseif ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_WORKER) : ?>
                                <a href="/" class="btn btn-danger botao-cancelar">
                                    <i class="fa fa-window-close"></i>
                                    Cancelar
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
<?php else : ?>
    <?php
        $estiloUsuarioLogado = "container ";
        $estiloUsuarioLogado .= isset($usuarioLogado) ? "col-lg-12" : "";
        ?>
    <div class="<?= $estiloUsuarioLogado ?>">
        <div class="row">
            <div class="center-block">
                <fieldset>
                    <legend> <?= isset($usuarioLogado) ? __('Adicionar conta') : __("Criar Conta") ?> </legend>

                    <?php
                        echo $this->Form->hidden('id');
                        echo $this->Form->hidden('usuarioLogadoTipoPerfil', ['value' => $usuarioLogadoTipoPerfil, 'class' => 'usuarioLogadoTipoPerfil']);
                        echo $this->Form->hidden('tipo_perfil', ['id' => 'tipo_perfil', 'value' => PROFILE_TYPE_USER]);
                        ?>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <span id="cpf_validation" class="text-danger validation-message"></span>
                        </div>
                        <div class="col-lg-4">
                            <div id="cpf_box">
                                <label for="cpf">
                                    CPF*
                                </label>
                                <input name="cpf" type="text" id="cpf" class="form-control" required="required" placeholder="CPF..." />
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label for="nome">Nome*</label>
                            <input type="text" name="nome" required="required" placeholder="Nome..." id="nome" class="form-control">
                        </div>

                        <div class="col-lg-4">
                            <label for="telefone">Telefone*</label>
                            <input type="text" name="telefone" id="telefone" class="form-control" placeholder="Telefone..." required="required" />
                        </div>

                    </div>
                    <?= $this->Form->hidden('doc_invalido', ['id' => 'doc_invalido']) ?>

                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label for="email">E-mail</label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="E-mail..." />
                            <span id="email_validation" class="text-danger validation-message">
                        </div>
                        <div class="col-lg-4">
                            <label for="senha">Senha*</label>
                            <input type="password" name="senha" minlength="6" placeholder="Senha..." id="senha" class="form-control senha" value="123456" required />
                        </div>

                        <div class="col-lg-4">
                            <label for="confirm_senha">Confirmar Senha*</label>
                            <input type="password" name="confirm_senha" placeholder="Confirmar Senha..." minlength="6" id="confirm_senha" class="form-control" value="123456" required />
                        </div>
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
                                    <?php elseif (in_array($usuarioLogado["tipo_perfil"], array(PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL, PROFILE_TYPE_ADMIN_LOCAL, PROFILE_TYPE_MANAGER))) : ?>
                                        <a href="/usuarios/meusClientes/" class="btn btn-danger botao-cancelar">
                                            <i class="fa fa-window-close"></i>
                                            Cancelar
                                        </a>
                                    <?php elseif ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_WORKER) : ?>
                                        <a href="/" class="btn btn-danger botao-cancelar">
                                            <i class="fa fa-window-close"></i>
                                            Cancelar
                                        </a>
                                    <?php endif; ?>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
<?php endif; ?>


</div>

<?php
$extension = Configure::read("debug") ? ""  : ".min";
// $this->fetch('script');
?>

<script src="/webroot/js/scripts/usuarios/add<?= $extension ?>.js?version=<?= SYSTEM_VERSION ?>" ></script>
<link rel="stylesheet" href="/webroot/css/styles/usuarios/usuario_form<?= $extension ?>.css?<?php SYSTEM_VERSION ?>" >
