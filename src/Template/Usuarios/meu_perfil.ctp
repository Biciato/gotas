<?php
/**
 * @description Arquivo para formulário de cadastro de usuários
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/meu_perfil.ctp
 * @date        28/08/2017
 *
 */
?>
    <nav class="col-lg-3 col-md-4">
        <ul class="nav nav-pills nav-stacked">
            <li class="active">
                <?= $this->Html->link(__('Menu'), []) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Editar meus dados'), ['action' => 'edit', $usuario->id]) ?>
            </li>
            <li>
                <?= $this->Html->link(__("Alterar senha"),
                    [
                    'controller' => 'usuarios',
                    'action' => 'alterar_senha',
                    $usuario->id
                    ]) ?>
            </li>

        </ul>
    </nav>
    <div class="usuarios view col-lg-9 col-md-8 columns content">
        <legend>
            <?= h($usuario->nome) ?>
        </legend>
        <table class="table table-striped table-hover">
            <tr>
                <th scope="row">
                    <?= __('Nome') ?>
                </th>
                <td>
                    <?= h($usuario->nome) ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('Tipo Perfil') ?>
                </th>
                <td>
                    <?= $this->UserUtil->getProfileType($this->Number->format($usuario->tipo_perfil)) ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('Cpf') ?>
                </th>
                <td>
                    <?= h($this->NumberFormat->formatNumberToCPF($usuario->cpf)) ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('Data de Nascimento') ?>
                </th>
                <td>
                    <?= h(date('d/m/Y', strtotime($usuario->data_nasc))) ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('Email') ?>
                </th>
                <td>
                    <?= h($usuario->email) ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <?= __('Telefone') ?>
                </th>
                <td>
                    <?= h($this->Phone->formatPhone($usuario->telefone)) ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('Endereco') ?>
                </th>
                <td>
                    <?= h(__($usuario->endereco)) ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('Endereco Complemento') ?>
                </th>
                <td>
                    <?= h($usuario->endereco_complemento) ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('Número') ?>
                </th>
                <td>
                    <?= $usuario->endereco_numero ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('Bairro') ?>
                </th>
                <td>
                    <?= h($usuario->bairro) ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('Municipio') ?>
                </th>
                <td>
                    <?= h($usuario->municipio) ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('Estado') ?>
                </th>
                <td>
                    <?= h($usuario->estado) ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?= __('CEP') ?>
                </th>
                <td>
                    <?= h($this->Address->formatCep($usuario->cep)) ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <?= __('Sexo') ?>
                </th>
                <td>
                    <?= $this->UserUtil->getGenderType($usuario->sexo) ?>
                </td>
            </tr>
            <tr>
                <th><?= __("Data de Criação") ?></th>
                <td>
                    <?= h($usuario->audit_insert->format('d/m/Y H:i:s')) ?>
                </td>
            </tr>
            <tr>
                <th><?= __("Última atualização") ?></th>
                <td>
                    <?= h(isset($usuario->audit_update) ? $usuario->audit_update->format('d/m/Y H:i:s') : null) ?>
                </td>
            </tr>

        </table>
    </div>
