<?php

/**
 * @description Exibe tela de lançamento manual de pontuacoes
 * @author      Gustavo Souza Gonçalves
 * @file        src\Template\PontuacoesComprovantes\correcao_gotas.ctp
 * @date        2019-10-15
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$debugExtension = Configure::read("debug") ? "" : "";

$title = "Correção de Gotas de Usuário";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ["class" => "active"]);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);
?>

<div class="col-lg-3 col-md-2">
    <nav class="nav nav-pills nav-stacked">
        <li class="active">
            <a>Menu</a>
        </li>
        <li>
            <a id="reiniciar">
                <i class="fas fa-refresh"></i>
                Reiniciar
            </a>
        </li>
    </nav>

</div>


<div class="col-lg-9">
    <div id="form">

        <legend><?= $title ?></legend>

        <div id="pesquisa-qrcode">
            <div class="form-group row">
                <div class="col-lg-4">
                    <label for="redes">Rede:</label>
                    <select name="redes" id="redes" class="form-control" placeholder="Redes..." title="Redes"></select>
                </div>
                <div class="col-lg-2">
                    <label for="usuario-cpf">Pesquisar Por:</label>
                    <select id="usuario-options-search" name="usuario-options-search" class="form-control" autofocus>
                        <option value="nome">Nome</option>
                        <option value="cpf">CPF</option>
                        <option value="telefone" selected>Telefone</option>
                        <option value="placa">Placa</option>
                    </select>
                </div>
                <div class="col-lg-6">
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
                                <tr>
                                    <th></th>
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                    <th>Data Nasc.</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-6">
                    <label for="usuario-nome">Nome:</label>
                    <input type="text" name="usuario-nome" id="usuario-nome" class="form-control" placeholder="Nome do Usuário..." title="Nome do Usuário" disabled>
                </div>
                <div class="col-lg-6">
                    <label for="usuario-saldo">Saldo de Gotas:</label>
                    <input type="text" name="usuario-saldo" id="usuario-saldo" class="form-control" placeholder="Saldo de Pontos do Usuário.." title="Saldo de Pontos do Usuário" readonly disabled>
                </div>
            </div>
        </div>

        <div id="dados">
            <div class="form-group row">
                <div class="col-lg-12">
                    <legend>Dados à Serem Inseridos:</legend>
                </div>
            </div>

            <!-- Área de inserção de gotas -->
            <!-- Tabela de Gotas -->
            <div class="data-container">
                <div class="gotas-select">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="quantidade_multiplicador">Quantidade de Pontos à ser Ajustado</label>
                            <input type="text" class="form-control" name="quantidade_multiplicador" id="quantidade-multiplicador">
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="button" class="btn btn-primary" id="botao-gravar-gotas">
                            <i class="fas fa-save"></i> Gravar
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas<?= $debugExtension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
    <link rel="stylesheet" href="/webroot/css/styles/pontuacoes_comprovantes/correcao_gotas<?= $debugExtension ?>.css?version=<?= SYSTEM_VERSION ?>">
