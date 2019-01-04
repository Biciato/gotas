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

$title = "Fechamento de Caixa";
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'atribuir_gotas']) ?>

<div class="col-lg-9 col-md-8">

<legend><?= $title ?> </legend>


<div class="col-lg-9">
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
            <h4>Turno Anterior: <?= sprintf("%s %s às %s", "De:", $dataInicioAnterior, $dataFimAnterior) ?></h4>
            <?php foreach ($turnoAnterior["dados"] as $cupom) : ?>

                <h6>Brinde: <?= $cupom["nomeBrinde"] ?></h6>

                    <li>Brindes Resgatados: <?= $cupom["resgatados"] ?> </li>
                    <li>Brindes Usados: <?= $cupom["usados"] ?> </li>
                    <!-- Qte de gotas recebido -->
                    <li>Total de Gotas Bonificadas: <?= $cupom["gotas"] ?> </li>
                    <!-- Qte de dinheiro recebido daquele brinde -->
                    <li>Total de Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                    <!-- Qte de Brindes vendidos via gotas -->
                    <li>Total de Bonificação: <?= $cupom["brindes"] ?> </li>
                    <!-- Qte de Brindes vendidos via dinheiro -->
                    <li>Total de Vendas: <?= $cupom["compras"] ?> </li>

            <?php endforeach; ?>

            <h5 ><strong>SubTotal Turno Anterior Funcionario <?= $dadoVenda["nome"] ?>:</strong></h5>

            <li>Soma de Brindes Resgatados: <?= $somaAnterior["somaResgatados"] ?> </li>
            <li>Soma de Brindes Usados: <?= $somaAnterior["somaUsados"] ?> </li>
            <!-- Qte de gotas recebido -->
            <li>Soma de Total de Gotas Bonificadas: <?= $somaAnterior["somaGotas"] ?> </li>
            <!-- Qte de dinheiro recebido daquele brinde -->
            <li>Soma de Total de Dinheiro Recebido: <?= $this->Number->currency($somaAnterior["somaDinheiro"]) ?> </li>

            <!-- Qte de Brindes vendidos via gotas -->
            <li>Soma de Total de Bonificação: <?= $somaAnterior["somaBrindes"] ?> </li>
            <!-- Qte de Brindes vendidos via dinheiro -->
            <li>Soma de Total de Vendas: <?= $somaAnterior["somaCompras"] ?> </li>

            <br />

            <h4>Turno Atual: <?= sprintf("%s %s às %s", "De:", $dataInicioAtual, $dataFimAtual) ?></h4>
            <?php foreach ($turnoAtual["dados"] as $cupom) : ?>

                <h6>Brinde: <?= $cupom["nomeBrinde"] ?></h6>

                    <li>Brindes Resgatados: <?= $cupom["resgatados"] ?> </li>
                    <li>Brindes Usados: <?= $cupom["usados"] ?> </li>
                    <!-- Qte de gotas recebido -->
                    <li>Total de Gotas Bonificadas: <?= $cupom["gotas"] ?> </li>
                    <!-- Qte de dinheiro recebido daquele brinde -->
                    <li>Total de Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                    <!-- Qte de Brindes vendidos via gotas -->
                    <li>Total de Bonificação: <?= $cupom["brindes"] ?> </li>
                    <!-- Qte de Brindes vendidos via dinheiro -->
                    <li>Total de Vendas: <?= $cupom["compras"] ?> </li>

            <?php endforeach; ?>

            <h5 ><strong>SubTotal Turno Atual Funcionario <?= $dadoVenda["nome"] ?>:</strong></h5>

            <li>Soma de Brindes Resgatados: <?= $somaAtual["somaResgatados"] ?> </li>
            <li>Soma de Brindes Usados: <?= $somaAtual["somaUsados"] ?> </li>
            <!-- Qte de gotas recebido -->
            <li>Soma de Total de Gotas Bonificadas: <?= $somaAtual["somaGotas"] ?> </li>
            <!-- Qte de dinheiro recebido daquele brinde -->
            <li>Soma de Total de Dinheiro Recebido: <?= $this->Number->currency($somaAtual["somaDinheiro"]) ?> </li>

            <!-- Qte de Brindes vendidos via gotas -->
            <li>Soma de Total de Bonificação: <?= $somaAtual["somaBrindes"] ?> </li>
            <!-- Qte de Brindes vendidos via dinheiro -->
            <li>Soma de Total de Vendas: <?= $somaAtual["somaCompras"] ?> </li>

            <br />

    <?php endforeach; ?>
    </div>
    <div class="col-lg-3 text-right">
        <button type="button" class="imprimir btn btn-primary " id="imprimir">
            <i class="fa fa-print"></i>
            Imprimir
        </button>
    </div>
</div>
