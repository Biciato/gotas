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
                <div class="col-lg-6">
                    <label for="redes">Rede</label>
                    <select name="redes" id="redes" class="form-control" placeholder="Redes..." title="Redes"></select>
                </div>
                <div class="col-lg-6">
                    <label for="usuario-cpf">CPF do Usuário</label>
                    <input type="text" name="usuario-cpf" id="usuario-cpf" class="form-control" placeholder="Informe o CPF do Usuário..." title="Informe o CPF do Usuário" autofocus>
                </div>

            </div>
            <div class="form-group row">
                <div class="col-lg-6">
                    <label for="usuario-nome">Nome</label>
                    <input type="text" name="usuario-nome" id="usuario-nome" class="form-control" placeholder="Nome do Usuário..." title="Nome do Usuário" disabled>
                </div>
                <div class="col-lg-6">
                    <label for="usuario-saldo">Saldo de Gotas</label>
                    <input type="text" name="usuario-saldo" id="usuario-saldo" class="form-control" placeholder="Saldo de Pontos do Usuário.." title="Saldo de Pontos do Usuário" readonly disabled>
                </div>
            </div>
        </div>

        <div id="dados">
            <div class="form-group row">
                <div class="col-lg-12">
                    <legend>Dados à Serem Inseridos</legend>
                </div>
            </div>

            <!-- Área de inserção de gotas -->
            <!-- Tabela de Gotas -->
            <div class="data-container">
                <div class="gotas-select">
                    <h4>Seleção de Gotas</h4>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="quantidade_multiplicador">Qte. Litros Abastecidos</label>
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

    <script src="/webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas<?= $debugExtension ?>.js"></script>
    <link rel="stylesheet" href="/webroot/css/styles/pontuacoes_comprovantes/correcao_gotas<?= $debugExtension ?>.css">