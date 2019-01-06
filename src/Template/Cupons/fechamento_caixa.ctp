<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Gotas/atribuir_gotas_form.ctp
 * @date     06/08/2017
 *
 * Arquivo para atribuir gotas de cliente na view de funcionário
 */

use Cake\Core\Configure;
use Cake\View\Helper\NumberHelper;

$debug = Configure::read("debug");

$title = "Fechamento de Caixa";
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'atribuir_gotas']) ?>

<div class="col-lg-9 col-md-8">

<legend><?= $title ?> </legend>


<div class="col-lg-8 print-area-common">

    <?php foreach ($dadosVendaFuncionarios as $key => $dadoVenda) : ?>
        <h3>Funcionário: <?= $dadoVenda["nome"] ?></h3>

        <p>
        <?php
        $turnoAnterior = $dadoVenda["turnoAnterior"];
        $dataInicioAnterior = $turnoAnterior["dataInicio"];
        $dataFimAnterior = $turnoAnterior["dataFim"];
        $turnoAtual = $dadoVenda["turnoAtual"];
        $dataInicioAtual = $turnoAnterior["dataInicio"];
        $dataFimAtual = $turnoAnterior["dataFim"];

        $somaAnterior = $dadoVenda["somaAnterior"];
        $somaAtual = $dadoVenda["somaAtual"];
        ?>
            <h4>Turno Anterior:</h4>
            <span><?= sprintf("De: %s Às %s: ", $dataInicioAnterior, $dataFimAnterior) ?></span>
            <p>
                <?php foreach ($turnoAnterior["dados"] as $cupom) : ?>

                    <h4>Brinde: <?= $cupom["nomeBrinde"] ?></h4>

                        <li class="list-group-item">Brindes Resgatados: <?= $cupom["resgatados"] ?> </li>
                        <li class="list-group-item">Brindes Usados: <?= $cupom["usados"] ?> </li>
                        <!-- Qte de gotas recebido -->
                        <li class="list-group-item">Total de Gotas Bonificadas: <?= $cupom["gotas"] ?> </li>
                        <!-- Qte de dinheiro recebido daquele brinde -->
                        <li class="list-group-item">Total de Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                        <!-- Qte de Brindes vendidos via gotas -->
                        <li class="list-group-item">Total de Bonificação: <?= $cupom["brindes"] ?> </li>
                        <!-- Qte de Brindes vendidos via dinheiro -->
                        <li class="list-group-item">Total de Vendas: <?= $cupom["compras"] ?> </li>

                <?php endforeach; ?>
            </p>

            <h5 ><strong>SubTotal Turno Anterior Funcionario <?= $dadoVenda["nome"] ?>:</strong></h5>

            <ul class="list-group">
                <li class="list-group-item">Soma de Brindes Resgatados: <?= $somaAnterior["somaResgatados"] ?> </li>
                <li class="list-group-item">Soma de Brindes Usados: <?= $somaAnterior["somaUsados"] ?> </li>
                <!-- Qte de gotas recebido -->
                <li class="list-group-item">Soma de Total de Gotas Bonificadas: <?= $somaAnterior["somaGotas"] ?> </li>
                <!-- Qte de dinheiro recebido daquele brinde -->
                <li class="list-group-item">Soma de Total de Dinheiro Recebido: <?= $this->Number->currency($somaAnterior["somaDinheiro"]) ?> </li>

                <!-- Qte de Brindes vendidos via gotas -->
                <li class="list-group-item">Soma de Total de Bonificação: <?= $somaAnterior["somaBrindes"] ?> </li>
                <!-- Qte de Brindes vendidos via dinheiro -->
                <li class="list-group-item">Soma de Total de Vendas: <?= $somaAnterior["somaCompras"] ?> </li>
            </ul>

            <h5>Turno Atual: </h5>
            <span><?= sprintf("De: %s Às %s: ", $dataInicioAtual, $dataFimAtual) ?></span>
            <p>

                <?php foreach ($turnoAtual["dados"] as $cupom) : ?>

                    <h4>Brinde: <?= $cupom["nomeBrinde"] ?></h4>

                    <ul class="list-group">
                        <li class="list-group-item">Brindes Resgatados: <?= $cupom["resgatados"] ?> </li>
                        <li class="list-group-item">Brindes Usados: <?= $cupom["usados"] ?> </li>
                        <!-- Qte de gotas recebido -->
                        <li class="list-group-item">Total de Gotas Bonificadas: <?= $cupom["gotas"] ?> </li>
                        <!-- Qte de dinheiro recebido daquele brinde -->
                        <li class="list-group-item">Total de Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                        <!-- Qte de Brindes vendidos via gotas -->
                        <li class="list-group-item">Total de Bonificação: <?= $cupom["brindes"] ?> </li>
                        <!-- Qte de Brindes vendidos via dinheiro -->
                        <li class="list-group-item">Total de Vendas: <?= $cupom["compras"] ?> </li>
                    </ul>

                <?php endforeach; ?>
            </p>

            <h5 ><strong>SubTotal Turno Atual Funcionario <?= $dadoVenda["nome"] ?>:</strong></h5>
            <ul class="list-group">
                <li class="list-group-item ">Soma de Brindes Resgatados: <?= $somaAtual["somaResgatados"] ?> </li>
                <li class="list-group-item">Soma de Brindes Usados: <?= $somaAtual["somaUsados"] ?> </li>
                <!-- Qte de gotas recebido -->
                <li class="list-group-item">Soma de Total de Gotas Bonificadas: <?= $somaAtual["somaGotas"] ?> </li>
                <!-- Qte de dinheiro recebido daquele brinde -->
                <li class="list-group-item">Soma de Total de Dinheiro Recebido: <?= $this->Number->currency($somaAtual["somaDinheiro"]) ?> </li>

                <!-- Qte de Brindes vendidos via gotas -->
                <li class="list-group-item">Soma de Total de Bonificação: <?= $somaAtual["somaBrindes"] ?> </li>
                <!-- Qte de Brindes vendidos via dinheiro -->
                <li class="list-group-item">Soma de Total de Vendas: <?= $somaAtual["somaCompras"] ?> </li>
            </ul>

            <div class="total-geral">
                <h4>Total Geral</h4>
                <ul class="list-group">
                    <li class="list-group-item"> Total de Brindes Resgatados:  <?= $totalGeral["totalResgatados"] ?> </li>
                    <li class="list-group-item"> Total de Brindes Usados:  <?= $totalGeral["totalUsados"] ?> </li>
                    <li class="list-group-item"> Total de Gotas Bonificadas:  <?= $totalGeral["totalGotas"] ?> </li>
                    <li class="list-group-item"> Total de Dinheiro Recebido:  <?= $this->Number->currency($totalGeral["totalDinheiro"]) ?> </li>
                    <li class="list-group-item"> Total de Bonificação:  <?= $totalGeral["totalBrindes"] ?> </li>
                    <li class="list-group-item"> Total de Vendas:  <?= $totalGeral["totalCompras"] ?> </li>
                </ul>
            </div>

    <?php endforeach; ?>
</div>

<div class="col-lg-4 text-center">
        <h4>Opções de impressão</h4>
        <div class="col-lg-6">
            <button type="button" class="imprimir btn btn-primary print-button-thermal " id="imprimir">
                <i class="fa fa-print"></i>
                Impressora Térmica
            </button>
        </div>
        <div class="col-lg-6">
            <button type="button" class="imprimir btn btn-primary print-button-common " id="imprimir">
                <i class="fa fa-print"></i>
                Impressora Comum
            </button>
        </div>
    </div>

</div>


<div class="print-area-thermal col-lg-3 print-thermal" >

    <?php foreach ($dadosVendaFuncionarios as $key => $dadoVenda) : ?>
        <span class="main-title">Funcionário: <?= $dadoVenda["nome"] ?></span>

        <p>
        <?php
        $turnoAnterior = $dadoVenda["turnoAnterior"];
        $dataInicioAnterior = $turnoAnterior["dataInicio"];
        $dataFimAnterior = $turnoAnterior["dataFim"];
        $turnoAtual = $dadoVenda["turnoAtual"];
        $dataInicioAtual = $turnoAnterior["dataInicio"];
        $dataFimAtual = $turnoAnterior["dataFim"];

        $somaAnterior = $dadoVenda["somaAnterior"];
        $somaAtual = $dadoVenda["somaAtual"];
        ?>
            <span class="shift-title">Turno Anterior:</span>
            <span class="shift-title-timer"><?= sprintf("De: %s Às %s: ", $dataInicioAnterior, $dataFimAnterior) ?></span>
            <p>
                <?php foreach ($turnoAnterior["dados"] as $cupom) : ?>

                    <span class="gift-title">Brinde: <?= $cupom["nomeBrinde"] ?></span>
                    <ul class="list-group">

                        <li class="list-group-item">Brindes Resgatados: <?= $cupom["resgatados"] ?> </li>
                        <li class="list-group-item">Brindes Usados: <?= $cupom["usados"] ?> </li>
                        <!-- Qte de gotas recebido -->
                        <li class="list-group-item">Total de Gotas Bonificadas: <?= $cupom["gotas"] ?> </li>
                        <!-- Qte de dinheiro recebido daquele brinde -->
                        <li class="list-group-item">Total de Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                        <!-- Qte de Brindes vendidos via gotas -->
                        <li class="list-group-item">Total de Bonificação: <?= $cupom["brindes"] ?> </li>
                        <!-- Qte de Brindes vendidos via dinheiro -->
                        <li class="list-group-item">Total de Vendas: <?= $cupom["compras"] ?> </li>
                    </ul>

                <?php endforeach; ?>
            </p>

            <h5 ><strong>SubTotal Turno Anterior Funcionario <?= $dadoVenda["nome"] ?>:</strong></h5>

            <ul class="list-group">
                <li class="list-group-item">Soma de Brindes Resgatados: <?= $somaAnterior["somaResgatados"] ?> </li>
                <li class="list-group-item">Soma de Brindes Usados: <?= $somaAnterior["somaUsados"] ?> </li>
                <!-- Qte de gotas recebido -->
                <li class="list-group-item">Soma de Total de Gotas Bonificadas: <?= $somaAnterior["somaGotas"] ?> </li>
                <!-- Qte de dinheiro recebido daquele brinde -->
                <li class="list-group-item">Soma de Total de Dinheiro Recebido: <?= $this->Number->currency($somaAnterior["somaDinheiro"]) ?> </li>

                <!-- Qte de Brindes vendidos via gotas -->
                <li class="list-group-item">Soma de Total de Bonificação: <?= $somaAnterior["somaBrindes"] ?> </li>
                <!-- Qte de Brindes vendidos via dinheiro -->
                <li class="list-group-item">Soma de Total de Vendas: <?= $somaAnterior["somaCompras"] ?> </li>
            </ul>

            <span class="shift-title">Turno Atual: </span>
            <span><?= sprintf("De: %s Às %s: ", $dataInicioAtual, $dataFimAtual) ?></span>
            <p>

                <?php foreach ($turnoAtual["dados"] as $cupom) : ?>

                <span class="gift-title">Brinde: <?= $cupom["nomeBrinde"] ?></span>

                <ul class="list-group">
                    <li class="list-group-item">Brindes Resgatados: <?= $cupom["resgatados"] ?> </li>
                    <li class="list-group-item">Brindes Usados: <?= $cupom["usados"] ?> </li>
                    <!-- Qte de gotas recebido -->
                    <li class="list-group-item">Total de Gotas Bonificadas: <?= $cupom["gotas"] ?> </li>
                    <!-- Qte de dinheiro recebido daquele brinde -->
                    <li class="list-group-item">Total de Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                    <!-- Qte de Brindes vendidos via gotas -->
                    <li class="list-group-item">Total de Bonificação: <?= $cupom["brindes"] ?> </li>
                    <!-- Qte de Brindes vendidos via dinheiro -->
                    <li class="list-group-item">Total de Vendas: <?= $cupom["compras"] ?> </li>
                </ul>

                <?php endforeach; ?>
            </p>

            <h5 ><strong>SubTotal Turno Atual Funcionario <?= $dadoVenda["nome"] ?>:</strong></h5>
            <ul class="list-group">
                <li class="list-group-item ">Soma de Brindes Resgatados: <?= $somaAtual["somaResgatados"] ?> </li>
                <li class="list-group-item">Soma de Brindes Usados: <?= $somaAtual["somaUsados"] ?> </li>
                <!-- Qte de gotas recebido -->
                <li class="list-group-item">Soma de Total de Gotas Bonificadas: <?= $somaAtual["somaGotas"] ?> </li>
                <!-- Qte de dinheiro recebido daquele brinde -->
                <li class="list-group-item">Soma de Total de Dinheiro Recebido: <?= $this->Number->currency($somaAtual["somaDinheiro"]) ?> </li>

                <!-- Qte de Brindes vendidos via gotas -->
                <li class="list-group-item">Soma de Total de Bonificação: <?= $somaAtual["somaBrindes"] ?> </li>
                <!-- Qte de Brindes vendidos via dinheiro -->
                <li class="list-group-item">Soma de Total de Vendas: <?= $somaAtual["somaCompras"] ?> </li>
            </ul>

            <div class="total-geral">
                <span class="main-title">Total Geral</span>
                <ul class="list-group">
                    <li class="list-group-item"> Total de Brindes Resgatados:  <?= $totalGeral["totalResgatados"] ?> </li>
                    <li class="list-group-item"> Total de Brindes Usados:  <?= $totalGeral["totalUsados"] ?> </li>
                    <li class="list-group-item"> Total de Gotas Bonificadas:  <?= $totalGeral["totalGotas"] ?> </li>
                    <li class="list-group-item"> Total de Dinheiro Recebido:  <?= $this->Number->currency($totalGeral["totalDinheiro"]) ?> </li>
                    <li class="list-group-item"> Total de Bonificação:  <?= $totalGeral["totalBrindes"] ?> </li>
                    <li class="list-group-item"> Total de Vendas:  <?= $totalGeral["totalCompras"] ?> </li>
                </ul>
            </div>

    <?php endforeach; ?>
</div>

<?php
// Adiciona comportamento jquery
$extensionJs = $debug ? ".js" : ".min.js";
$extensionCss = $debug ? ".css" : ".min.css";
echo $this->Html->script('scripts/cupons/fechamento_caixa' . $extensionJs);
echo $this->Html->css("styles/cupons/fechamento_caixa" . $extensionCss);
echo $this->fetch("script");
?>
