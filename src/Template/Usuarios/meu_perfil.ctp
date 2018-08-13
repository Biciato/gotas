<?php

/**
 * @description Arquivo para formulário de cadastro de usuários
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/meu_perfil.ctp
 * @date        28/08/2017
 *
 */

use Cake\Core\Configure;

//  TODO: Menu
?>
    <nav class="col-lg-3 col-md-4">
        <ul class="nav nav-pills nav-stacked">
            <li class="active">
                <?= $this->Html->link(__('Menu'), []) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Alterar Dados Cadastrais'), ['action' => 'editar', $usuario->id]) ?>
            </li>
            <li>
                <?= $this->Html->link(
                    __("Alterar Senha"),
                    [
                        'controller' => 'usuarios',
                        'action' => 'alterar_senha',
                        $usuario->id
                    ]
                ) ?>
            </li>

        </ul>
    </nav>
    <div class="usuarios view col-lg-9 col-md-8 columns content">
        <div class="form-group row">

            <legend>
                <?= h($usuario->nome) ?>
            </legend>

            <div class="col-lg-9">
                <table class="table table-striped table-hover">
                    <tr>
                        <th scope="row">
                            <?= __('Nome') ?>
                        </th>
                        <th scope="row">
                            <?= __('Data de Nascimento') ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?= h($usuario->nome) ?>
                        </td>
                        <td>
                            <?= h(date('d/m/Y', strtotime($usuario->data_nasc))) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?= __('CPF') ?>
                        </th>
                        <th scope="row">
                            <?= __('Sexo') ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?= h($this->NumberFormat->formatNumberToCPF($usuario->cpf)) ?>
                        </td>
                        <td>
                            <?= $this->UserUtil->getGenderType($usuario->sexo) ?>
                        </td>

                    </tr>

                    <?php if ($user_logged["tipo_perfil"] <= Configure::read("profileTypes")["WorkerProfileType"]) : ?>
                        <tr>
                            <th scope="row">
                                <?= __('Tipo Perfil') ?>
                            </th>
                            <td>
                                <?= $this->UserUtil->getProfileType($this->Number->format($usuario->tipo_perfil)) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                </table>
            </div>
            <div class="col lg-3">
                <span style="font-weight: bolder;">Foto de perfil</span>
                <br />
                <?= $this->Html->image(__("{0}{1}", Configure::read("imageUserProfilePathRead"), $usuario->foto_perfil), array("alt" => "foto perfil", "title" => "Foto atual")) ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12">
                <legend>Dados de contato</legend>

                <table class="table table-striped table-hover">
                    <tr>
                        <th scope="row">
                            <?= __('Email') ?>
                        </th>
                        <th scope="row">
                            <?= __('Telefone') ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?= h($usuario->email) ?>
                        </td>
                        <td>
                            <?= h($this->Phone->formatPhone($usuario->telefone)) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?= __('Endereco') ?>
                        </th>
                        <th scope="row">
                            <?= __('Número') ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?= h(__($usuario->endereco)) ?>
                        </td>
                        <td>
                            <?= $usuario->endereco_numero ?>
                            <?= h($usuario->endereco_complemento) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?= __('Bairro') ?>
                        </th>
                        <th scope="row">
                            <?= __('Municipio') ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?= h($usuario->bairro) ?>
                        </td>
                        <td>
                            <?= h($usuario->municipio) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?= __('Estado') ?>
                        </th>
                        <th scope="row">
                            <?= __('CEP') ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?= h($usuario->estado) ?>
                        </td>
                        <td>
                            <?= h($this->Address->formatCep($usuario->cep)) ?>
                        </td>
                    </tr>

                    <tr>
                        <th><?= __("Data de Criação") ?></th>
                        <td>
                            <?= h($usuario->audit_insert->format('d/m/Y H:i:s')) ?>
                        </td>
                    </tr>
                    <?php if ($user_logged["tipo_perfil"] <= Configure::read("profileTypes")["WorkerProfileType"]) : ?>

                        <tr>
                            <th><?= __("Última atualização") ?></th>
                            <td>
                                <?= h(isset($usuario->audit_update) ? $usuario->audit_update->format('d/m/Y H:i:s') : null) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                </table>
            </div>

        </div>

    </div>
</div>
