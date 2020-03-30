<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/alterar_cliente_pontuacao.ctp
 * @date     17/10/2017
 */

?>

<?= $this->element('../Pontuacoes/left_menu', ['controller' => 'pontuacoes', 'action' => 'detalhes_cupom', 'id' => $pontuacao->id, 'mode' => 'details']) ?>

<?= $this->Form->create() ?>
<div class="col-lg-9 col-md-10 columns">

    <legend>
        <?= __("Alterar Cliente de Pontuação Atribuída") ?>
    </legend>

    <h4>Dados do Cupom Atual</h4>

    <table class="table table-striped table-hover">
        <tr>
            <th>
                <?= __('Cliente Atual') ?>
            </th>
            <td>
                <?= h($pontuacao->usuario->nome) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Funcionário do atendimento') ?>
            </th>
            <td>
                <?= h($pontuacao->funcionario->nome) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Total de Gotas') ?>
            </th>
            <td>
                <?= h($this->Number->precision($pontuacao->soma_pontuacoes, 2)) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Chave da NFE') ?>
            </th>
            <td>
                <?= h($pontuacao->chave_nfe) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Data Impressão ') ?>
            </th>
            <td>
                <?= h($pontuacao->data->format('d/m/Y H:i:s')) ?>
            </td>

        </tr>
    </table>

    <?= $this->element('../Usuarios/filtro_usuarios_ajax'); ?>

    <?= $this->Form->text('clientes_id', ['id' => 'clientes_id', 'value' => $cliente->id, 'class' => 'hidden', 'label' => false]); ?>

    <!--
        // não pode ter restrict query!
        // motivo: pode ter sido a primeira gota do cliente e um funcionário
        // pode atribuir estas gotas incorretamente, então o gestor não conseguirá fazer o ajuste
        // $this->Form->text('restrict_query', ['id' => 'restrict_query', 'value' => true, 'style' => 'display: none;']);
        -->

    <div class="form-group row">
        <div class="col-lg-12">
            <div class="pull-right">
                <button type="submit" class="btn btn-primary" id="button-confirm" disabled><i class='fas fa-check'></i> Alterar Usuário </button>
            </div>
        </div>
    </div>
</div>

<?= $this->Form->end(); ?>

<?php
$extension = Configure::read("debug") ? ""  : ".min";
?>
<script src="/webroot/js/scripts/pontuacoes/alterar_cliente_pontuacao<?= $extension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/css/styles/pontuacoes/alterar_cliente_pontuacao<?= $extension ?>.css?<?= SYSTEM_VERSION ?>">

<?= $this->fetch('script') ?>
