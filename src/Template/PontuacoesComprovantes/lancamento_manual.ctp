<?php

/**
 * @description Exibe tela de lançamento manual de pontuacoes
 * @author      Gustavo Souza Gonçalves
 * @file        src\Template\PontuacoesComprovantes\lancamento_manual.ctp
 * @date        2019-10-09
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$debugExtension = Configure::read("debug") ? "" : "";

$title = "Lançamento Manual de Gotas para Cliente Final";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ["class" => "active"]);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);
?>

<div class="col-lg-3 col-md-2">
    <nav class="nav nav-pills nav-stacked">
        <li class="active">
            <a>Menu</a>
        </li>

    </nav>

</div>


<div class="col-lg-9">
    <div id="form">

        <legend><?= $title ?></legend>

        <div id="pesquisa-qrcode">
            <div class="form-group row">
                <div class="col-lg-6">
                    <label for="redes">Rede</label>
                    <select name="redes" id="redes" class="form-control" placeholder="Redes..." title="Redes"></select>
                </div>
                <div class="col-lg-6">
                    <label for="clientes">Estabelecimento</label>
                    <select name="clientes" id="clientes" class="form-control" placeholder="Estabelecimento..." title="Estabelecimento"></select>
                </div>
                <!-- <div class="col-lg-4">
                    <label for="gotas">Gotas do Estabelecimento*</label>
                    <select name="gotas" id="gotas" class="form-control" placeholder="Estabelecimento..." title="Estabelecimento" ></select>
                </div> -->
            </div>
            <div class="form-group row">
                <div class="col-lg-4">
                    <label for="usuario-cpf">CPF do Usuário</label>
                    <input type="text" name="usuario-cpf" id="usuario-cpf" class="form-control" placeholder="Informe o CPF do Usuário..." title="Informe o CPF do Usuário" autofocus>
                </div>
                <div class="col-lg-4">
                    <label for="usuario-nome">Nome</label>
                    <input type="text" name="usuario-nome" id="usuario-nome" class="form-control" placeholder="Nome do Usuário..." title="Nome do Usuário" disabled>
                </div>
                <div class="col-lg-4">
                    <label for="usuario-saldo">Saldo de Gotas</label>
                    <input type="text" name="usuario-saldo" id="usuario-saldo" class="form-control" placeholder="Saldo de Pontos do Usuário.." title="Saldo de Pontos do Usuário" readonly disabled>
                </div>
            </div>
            <!-- <div class="form-group row ">
                <div class="col-lg-2 pull-right">
                    <button type="button" class="btn btn-primary btn-block" id="botao-pesquisar">
                        <span class="fa fa-search"></span>
                        Pesquisar
                    </button>
                </div>
            </div> -->
        </div>

        <div id="dados" class="form-group row">
            <div class="col-lg-12">
                <legend>Dados à Serem Inseridos</legend>
            </div>

            <!-- Área de inserção de gotas -->
            <!-- Tabela de Gotas -->
            <div class="data-container">
                <div class="gotas-select">
                    <div class="col-lg-6">
                        <h4>Seleção de Gotas</h4>
                        <div class="form-group">
                            <label for="gotas">Gotas</label>
                            <select name="gotas" id="gotas" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label for="quantidade_multiplicador">Quantidade de Litros Abastecidos</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-check"></i> Inserir
                            </button>

                        </div>
                    </div>
                </div>
                <div class="gotas-table">
                    <div class="col-lg-6">
                        <h4>Dados à Serem Enviados</h4>
                        <div class="form-group">
                            <table class="table table-responsive table-condensed table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Gota</th>
                                        <th>Qte.</th>
                                        <th class="actions">Ações
                                            <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes"><span class=" fa fa-book"> Legendas</span></div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-check"></i> Inserir
                            </button>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">

            </div>

        </div>
    </div>

    <script src="/webroot/js/scripts/pontuacoes_comprovantes/lancamento_manual<?= $debugExtension ?>.js"></script>
    <link rel="stylesheet" href="/webroot/css/styles/pontuacoes_comprovantes/lancamento_manual<?= $debugExtension ?>.css">
