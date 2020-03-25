<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/filtro_usuarios_ajax.ctp
 * @date     14/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$isVendaAvulsa = isset($isVendaAvulsa) ? $isVendaAvulsa : false;
?>

<div class="form-group" id="form">

    <div class="form-group row">

        <div class="col-lg-12">
            <h4>Selecione um cliente</h4>
        </div>

        <div class="col-lg-2">
            <label for="usuario-options-search">Pesquisar Por:</label>
            <select id="usuario-options-search" name="usuario-options-search" class="form-control" autofocus>
                <option value="nome">Nome</option>
                <option value="cpf">CPF</option>
                <option value="telefone" selected>Telefone</option>
                <option value="placa">Placa</option>
            </select>
        </div>
        <div class="col-lg-10">
            <label for="usuario-cpf">Dados de Pesquisa do Usuário:</label>
            <input type="text" name="usuario-parameter-search" id="usuario-parameter-search" class="form-control" placeholder="" title="">
        </div>
    </div>

    <div class="form-group row">
        <div class="col-lg-12">
            <div class="pull-right">
                <div class="btn btn-primary" title="Pesquisar" id="usuario-parameter-button-search"><i class="fas fa-search-plus"></i> Pesquisar </div>
            </div>
        </div>
    </div>

    <span class="text-danger validation-message" id="userValidationMessage"></span>

    <div id="veiculo-region">
        <h4>Dados do Veículo:</h4>

        <div class="form-group row">
            <div class="col-lg-3">
                <label for="veiculo-placa">Placa:</label>
                <input type="text" name="veiculo-placa" id="veiculo-placa" class="form-control" disabled placeholder="Placa do Veículo..." title="Placa do Veículo">
            </div>
            <div class="col-lg-3">
                <label for="veiculo-modelo">Modelo:</label>
                <input type="text" name="veiculo-modelo" id="veiculo-modelo" class="form-control" disabled placeholder="Modelo do Veículo..." title="Modelo do Veículo">

            </div>
            <div class="col-lg-3">
                <label for="veiculo-fabricante">Fabricante:</label>
                <input type="text" name="veiculo-fabricante" id="veiculo-fabricante" class="form-control" disabled placeholder="Fabricante do Veículo..." title="Fabricante do Veículo">

            </div>
            <div class="col-lg-3">
                <label for="veiculo-ano">Ano:</label>
                <input type="text" name="veiculo-ano" id="veiculo-ano" class="form-control" disabled placeholder="Ano do Veículo..." title="Ano do Veículo">

            </div>
        </div>
    </div>

    <!-- Div para lista de seleção de usuários -->
    <div id="usuarios-region">
        <h4>Usuários Encontrados:</h4>
        <div class="form-group row">
            <div class="col-lg-12">
                <table class="table table-bordered table-hover table-striped table-condensed" id='usuarios-table'>
                    <thead>
                        <!-- <tr>
                            <th></th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Data Nasc.</th>
                            <th>Ações</th>
                        </tr> -->
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="user-selected">
        <div class="form-group row">
            <!-- <div class="form-group row  "> -->
            <div class="col-lg-12">
                <h4>Cliente selecionado</h4>
            </div>

            <input type="hidden" name="usuarios_id" id="usuarios-id" class="usuarios-id" value="<?php echo !empty($usuarios_id) ? $usuarios_id : null; ?>" />
            <div class="col-lg-4">
                <label for="usuario-nome">Nome:</label>
                <input type="text" name="usuario-nome" id="usuario-nome" class="form-control" placeholder="Nome do Usuário..." title="Nome do Usuário" disabled>
            </div>
            <div class="col-lg-4">
                <label for="usuario-data-nasc">Data Nascimento:</label>
                <input type="text" name="usuario_data_nasc" id="usuario-data-nasc" class="form-control" placeholder="Data de Nascimento..." title="Data de Nascimento" disabled>
            </div>
            <div class="col-lg-4">
                <label for="usuario-saldo">Saldo de Gotas:</label>
                <input type="text" name="usuario-saldo" id="usuario-saldo" class="form-control" placeholder="Saldo de Pontos do Usuário.." title="Saldo de Pontos do Usuário" readonly disabled>
            </div>
        </div>
    </div>
</div>

<?php
$extension = Configure::read("debug") ? ""  : ".min";
?>
<script src="/webroot/js/scripts/usuarios/filtro_usuarios_ajax<?= $extension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/css/styles/usuarios/filtro_usuarios_ajax<?= $extension ?>.css?<?php SYSTEM_VERSION ?>">
