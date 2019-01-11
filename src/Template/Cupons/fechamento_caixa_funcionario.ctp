<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src\Template\Cupons\fechamento_caixa_funcionario.ctp
 * @date     06/08/2017
 *
 * Arquivo para atribuir gotas de cliente na view de funcionário
 */

use Cake\Core\Configure;
use Cake\View\Helper\NumberHelper;

$debug = Configure::read("debug");

$title = "Fechamento de Caixa de Funcionários";
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'fechamento_caixa_funcionario']) ?>

<div class="col-lg-9 col-md-8">

<legend><?= $title ?> </legend>

<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
                <div>
                    <span class="fa fa-search"></span>
                        Exibir / Ocultar Filtros
                </div>
        </div>
        <div id="filter-coupons" class="panel-collapse collapse in">
            <div class="panel-body">
            <form action="/cupons/fechamentoCaixaFuncionario/" method="post">
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label for="filtrar-turno-anterior">Filtrar Turno Anterior?</label>
                        <?= $this->Form->input(
                            "filtrar_turno_anterior",
                            array(
                                "type" => "select",
                                "options" => $filtrarTurnoAnteriorList,
                                "value" => $filtrarTurnoAnterior,
                                "label" => false

                            )
                        ) ?>

                    </div>

                </div>
                <div class="form-group row">
                    <div class="col-lg-12 text-right">
                        <button type="submit"
                            class="btn btn-primary botao-pesquisar">
                            <span class="fa fa-search"></span>
                            Pesquisar
                        </button>
                        <button type="button" class="imprimir btn btn-default print-button-thermal " id="imprimir">
                            <i class="fa fa-print"></i>
                            Impressora Térmica
                        </button>
                        <button type="button" class="imprimir btn btn-default print-button-common " id="imprimir">
                            <i class="fa fa-print"></i>
                            Impressora Comum
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="col-lg-12 print-area-common">

    <?php foreach ($dadosVendaFuncionarios as $key => $dadoVenda) : ?>
        <h3>Funcionário: <?= $dadoVenda["nome"] ?></h3>

        <p>
        <?php

        if ($filtrarTurnoAnterior) {

            $turnoAnterior = $dadoVenda["turnoAnterior"];
            $dataInicioAnterior = $turnoAnterior["dataInicio"];
            $dataFimAnterior = $turnoAnterior["dataFim"];
            $somaAnterior = $dadoVenda["somaAnterior"];
        }

        $turnoAtual = $dadoVenda["turnoAtual"];
        $dataInicioAtual = $turnoAtual["dataInicio"];
        $dataFimAtual = $turnoAtual["dataFim"];
        $somaAtual = $dadoVenda["somaAtual"];
        ?>
            <?php if ($filtrarTurnoAnterior == 1) : ?>
                <h4>Turno Anterior:</h4>
                <span><?= sprintf("De: %s Às %s: ", $dataInicioAnterior, $dataFimAnterior) ?></span>
                <p>
                    <?php foreach ($turnoAnterior["dados"] as $cupom) : ?>

                        <?php if (($cupom["resgatados"] > 0)
                            && ($cupom["usados"] > 0)
                            && ($cupom["gotas"] > 0)
                            && ($cupom["dinheiro"] > 0)
                            && ($cupom["brindes"] > 0)
                            && ($cupom["compras"] > 0)) : ?>

                            <h4>Brinde: <?= $cupom["nomeBrinde"] ?></h4>

                            <?php if ($cupom["resgatados"] > 0) : ?>
                                <li class="list-group-item">Brindes Resgatados: <?= $cupom["resgatados"] ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["usados"] > 0) : ?>
                                <li class="list-group-item">Brindes Usados: <?= $cupom["usados"] ?> </li>
                            <?php endif; ?>
                            <?php if ($cupom["gotas"] > 0) : ?>
                                <!-- Qte de gotas recebido -->
                                <li class="list-group-item">Total de Gotas Bonificadas: <?= $cupom["gotas"] ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["dinheiro"] > 0) : ?>
                                <!-- Qte de dinheiro recebido daquele brinde -->
                                <li class="list-group-item">Total de Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["brindes"] > 0) : ?>
                                <!-- Qte de Brindes vendidos via gotas -->
                                <li class="list-group-item">Total de Bonificação: <?= $cupom["brindes"] ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["compras"] > 0) : ?>
                                <!-- Qte de Brindes vendidos via dinheiro -->
                                <li class="list-group-item">Total de Vendas: <?= $cupom["compras"] ?> </li>
                            <?php endif; ?>

                        <?php endif; ?>

                    <?php endforeach; ?>
                </p>

                <?php if ($filtrarTurnoAnterior == 1) : ?>

                    <h5><strong>SubTotal Turno Anterior Funcionario <?= $dadoVenda["nome"] ?>:</strong></h5>

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
                <?php endif; ?>

            <?php endif; ?>
            <h4>Turno Atual: </h4>
            <span><?= sprintf("De: %s Às %s: ", $dataInicioAtual, $dataFimAtual) ?></span>
            <p>

                <?php foreach ($turnoAtual["dados"] as $cupom) : ?>

                    <?php if (($cupom["resgatados"] > 0)
                        && ($cupom["usados"] > 0)
                        && ($cupom["gotas"] > 0)
                        && ($cupom["dinheiro"] > 0)
                        && ($cupom["brindes"] > 0)
                        && ($cupom["compras"] > 0)) : ?>

                        <h4>Brinde: <?= $cupom["nomeBrinde"] ?></h4>

                            <?php if ($cupom["resgatados"] > 0) : ?>
                                <li class="list-group-item">Brindes Resgatados: <?= $cupom["resgatados"] ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["usados"] > 0) : ?>
                                <li class="list-group-item">Brindes Usados: <?= $cupom["usados"] ?> </li>
                            <?php endif; ?>
                            <?php if ($cupom["gotas"] > 0) : ?>
                                <!-- Qte de gotas recebido -->
                                <li class="list-group-item">Total de Gotas Bonificadas: <?= $cupom["gotas"] ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["dinheiro"] > 0) : ?>
                                <!-- Qte de dinheiro recebido daquele brinde -->
                                <li class="list-group-item">Total de Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["brindes"] > 0) : ?>
                                <!-- Qte de Brindes vendidos via gotas -->
                                <li class="list-group-item">Total de Bonificação: <?= $cupom["brindes"] ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["compras"] > 0) : ?>
                                <!-- Qte de Brindes vendidos via dinheiro -->
                                <li class="list-group-item">Total de Vendas: <?= $cupom["compras"] ?> </li>
                            <?php endif; ?>

                    <?php endif; ?>

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



<div class="print-area-thermal col-lg-3 print-thermal" >
<!-- <div class="print-area-thermal col-lg-3" > -->

    <?php foreach ($dadosVendaFuncionarios as $key => $dadoVenda) : ?>
        <span class="main-title">Funcionário: <?= $dadoVenda["nome"] ?></span>

        <p>
        <?php
           if ($filtrarTurnoAnterior) {

            $turnoAnterior = $dadoVenda["turnoAnterior"];
            $dataInicioAnterior = $turnoAnterior["dataInicio"];
            $dataFimAnterior = $turnoAnterior["dataFim"];
            $somaAnterior = $dadoVenda["somaAnterior"];
        }

        $turnoAtual = $dadoVenda["turnoAtual"];
        $dataInicioAtual = $turnoAtual["dataInicio"];
        $dataFimAtual = $turnoAtual["dataFim"];
        $somaAtual = $dadoVenda["somaAtual"];
        ?>
            <?php if ($filtrarTurnoAnterior == 1) : ?>
                <span class="shift-title">Turno Anterior:</span>
                <span class="shift-title-timer"><?= sprintf("De: %s Às %s: ", $dataInicioAnterior, $dataFimAnterior) ?></span>
                <p>
                    <?php foreach ($turnoAnterior["dados"] as $cupom) : ?>
                        <?php if (($cupom["resgatados"] > 0)
                            && ($cupom["usados"] > 0)
                            && ($cupom["gotas"] > 0)
                            && ($cupom["dinheiro"] > 0)
                            && ($cupom["brindes"] > 0)
                            && ($cupom["compras"] > 0)) : ?>

                            <span class="gift-title">Brinde: <?= $cupom["nomeBrinde"] ?></span>
                            <ul class="list-group">

                                <?php if ($cupom["resgatados"] > 0) : ?>
                                    <li class="list-group-item">Brindes Resgatados: <?= $cupom["resgatados"] ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["usados"] > 0) : ?>
                                    <li class="list-group-item">Brindes Usados: <?= $cupom["usados"] ?> </li>
                                <?php endif; ?>
                                <?php if ($cupom["gotas"] > 0) : ?>
                                    <!-- Qte de gotas recebido -->
                                    <li class="list-group-item">Total de Gotas Bonificadas: <?= $cupom["gotas"] ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["dinheiro"] > 0) : ?>
                                    <!-- Qte de dinheiro recebido daquele brinde -->
                                    <li class="list-group-item">Total de Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["brindes"] > 0) : ?>
                                    <!-- Qte de Brindes vendidos via gotas -->
                                    <li class="list-group-item">Total de Bonificação: <?= $cupom["brindes"] ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["compras"] > 0) : ?>
                                    <!-- Qte de Brindes vendidos via dinheiro -->
                                    <li class="list-group-item">Total de Vendas: <?= $cupom["compras"] ?> </li>
                                <?php endif; ?>
                            </ul>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </p>
            <?php endif; ?>

            <?php if ($filtrarTurnoAnterior == 1) : ?>
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
            <?php endif;?>

            <span class="shift-title">Turno Atual: </span>
            <span><?= sprintf("De: %s Às %s: ", $dataInicioAtual, $dataFimAtual) ?></span>
            <p>

                <?php foreach ($turnoAtual["dados"] as $cupom) : ?>

                <?php if (($cupom["resgatados"] > 0)
                    && ($cupom["usados"] > 0)
                    && ($cupom["gotas"] > 0)
                    && ($cupom["dinheiro"] > 0)
                    && ($cupom["brindes"] > 0)
                    && ($cupom["compras"] > 0)) : ?>

                        <span class="gift-title">Brinde: <?= $cupom["nomeBrinde"] ?></span>
                        <ul class="list-group">

                            <?php if ($cupom["resgatados"] > 0) : ?>
                                <li class="list-group-item">Brindes Resgatados: <?= $cupom["resgatados"] ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["usados"] > 0) : ?>
                                <li class="list-group-item">Brindes Usados: <?= $cupom["usados"] ?> </li>
                            <?php endif; ?>
                            <?php if ($cupom["gotas"] > 0) : ?>
                                <!-- Qte de gotas recebido -->
                                <li class="list-group-item">Total de Gotas Bonificadas: <?= $cupom["gotas"] ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["dinheiro"] > 0) : ?>
                                <!-- Qte de dinheiro recebido daquele brinde -->
                                <li class="list-group-item">Total de Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["brindes"] > 0) : ?>
                                <!-- Qte de Brindes vendidos via gotas -->
                                <li class="list-group-item">Total de Bonificação: <?= $cupom["brindes"] ?> </li>
                            <?php endif; ?>

                            <?php if ($cupom["compras"] > 0) : ?>
                                <!-- Qte de Brindes vendidos via dinheiro -->
                                <li class="list-group-item">Total de Vendas: <?= $cupom["compras"] ?> </li>
                            <?php endif; ?>
                        </ul>

                <?php endif; ?>

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
