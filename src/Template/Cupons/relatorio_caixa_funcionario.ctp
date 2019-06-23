<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src\Template\Cupons\relatorio_caixa_funcionario.ctp
 * @date     06/08/2017
 *
 * Tela de Relatório de Caixa dos Funcionários de um Posto. Visualizado por um mesmo funcionário
 * 
 */

use Cake\Core\Configure;
use Cake\View\Helper\NumberHelper;
use App\Custom\RTI\DebugUtil;

$debug = Configure::read("debug");

$title = "Relatório de Caixa de Funcionários";
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'relatorio_caixa_funcionario']) ?>

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
                    <form action="/cupons/relatorioCaixaFuncionario/" method="post">
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label for="tipoFiltro">Tipo de Filtro:</label>
                                <?= $this->Form->input(
                                    "tipoFiltro",
                                    array(
                                        "select",
                                        "options" => $tipoFiltroList,
                                        "name" => "tipoFiltro",
                                        "id" => "tipoFiltro",
                                        "value" => "Turno",
                                        "label" => false,
                                        "autofocus" => true

                                    )
                                ); ?>
                            </div>
                            <div class="col-lg-4">
                                <label for="dataInicio">Data Início:</label>
                                <input type="text" class="form-control datetimepicker-input" format="d/m/Y" name="data_inicio" id="data_inicio" placeholder="Data Início...">

                            </div>
                            <div class="col-lg-4">
                                <label for="dataFim">Data Fim:</label>
                                <input type="text" class="form-control datetimepicker-input" format="d/m/Y" name="data_fim" id="data_fim" placeholder="Data Fim...">
                            </div>
                        </div>
                        <div class="form-group row">
                            <i class="col-lg-12 text-right">

                                <button type="submit" class="btn btn-primary botao-pesquisar">
                                    <span class="fa fa-bar-chart"></span>
                                    Gerar Relatório
                                </button>
                                <?php if (count($dadosVendaFuncionarios) > 0) : ?>
                                    <button type="button" class="imprimir btn btn-default print-button-thermal" id="imprimir">
                                        <i class="fa fa-print"></i>
                                        Impressora Térmica
                                    </button>
                                    <button type="button" class="imprimir btn btn-default print-button-common" id="imprimir">
                                        <i class="fa fa-print"></i>
                                        Impressora Comum
                                    </button>
                                <?php endif; ?>
                        </div>
                </div>

                <input type="hidden" name="data_inicio_envio" id="data_inicio_envio" value="<?= $dataInicio ?>" class="data-inicio-envio">
                <input type="hidden" name="data_fim_envio" id="data_fim_envio" value="<?= $dataFim ?>" class="data-fim-envio">
                </form>

            </div>
        </div>
    </div>

    <div class="col-lg-12 print-area-common">

        <?php if (!empty($tituloTurno)) : ?>
            <h3><?= $tituloTurno ?></h3>
            <span><?= sprintf("De: %s Às %s: ", $dataInicio, $dataFim) ?></span>
        <?php else : ?>
            <h4 class="text-center">Utilize um dos filtros para gerar o relatório!</h4>

        <?php endif; ?>

        <?php if ($tipoFiltroSelecionado == FILTER_TYPE_DATE_TIME && count($dadosVendaFuncionarios) > 0) : ?>
            <!-- Se filtro por hora, exibir mensagem: -->
            <h4 class="text-center">Relatório Parcial de Caixa do Funcionário, não vale como Relatório Oficial!</h4>
        <?php endif; ?>

        <?php foreach ($dadosVendaFuncionarios as $key => $dadoVenda) : ?>
            <?php
            $filtrarTurno = $dadoVenda["filtrarTurno"];
            $somaAtual = $dadoVenda["soma"];
            ?>
            <h4>Funcionário: <?= $dadoVenda["nome"] ?></h4>
            <p>
                <?php foreach ($filtrarTurno["dados"] as $cupom) : ?>

                    <?php if (($cupom["resgatados"] > 0)
                        || ($cupom["usados"] > 0)
                        || ($cupom["gotas"] > 0)
                        || ($cupom["dinheiro"] > 0)
                        || ($cupom["brindes"] > 0)
                        || ($cupom["compras"] > 0)
                    ) : ?>

                        <h5>Brinde: <?= $cupom["nomeBrinde"] ?></h5>

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

            <div class="total-geral">
                <h4>Total Geral</h4>
                <ul class="list-group">
                    <li class="list-group-item"> Total de Brindes Resgatados: <?= $totalGeral["totalResgatados"] ?> </li>
                    <li class="list-group-item"> Total de Brindes Usados: <?= $totalGeral["totalUsados"] ?> </li>
                    <li class="list-group-item"> Total de Gotas Bonificadas: <?= $totalGeral["totalGotas"] ?> </li>
                    <li class="list-group-item"> Total de Dinheiro Recebido: <?= $this->Number->currency($totalGeral["totalDinheiro"]) ?> </li>
                    <li class="list-group-item"> Total de Bonificação: <?= $totalGeral["totalBrindes"] ?> </li>
                    <li class="list-group-item"> Total de Vendas: <?= $totalGeral["totalCompras"] ?> </li>
                </ul>
            </div>

        <?php endforeach; ?>
    </div>

    <div class="print-area-thermal col-lg-3 print-thermal">
        <?php if (!empty($tituloTurno)) : ?>
            <h3><?= $tituloTurno ?></h3>
            <span><?= sprintf("Período: %s Às %s: ", $dataInicio, $dataFim) ?></span>
        <?php else : ?>
            <h4 class="text-center">Utilize um dos filtros para gerar o relatório!</h4>

        <?php endif; ?>
        <?php foreach ($dadosVendaFuncionarios as $key => $dadoVenda) : ?>
            <?php
            $filtrarTurno = $dadoVenda["filtrarTurno"];
            $somaAtual = $dadoVenda["soma"];
            ?>
            <h4>Funcionário: <?= $dadoVenda["nome"] ?></h4>
            <p>
                <?php foreach ($filtrarTurno["dados"] as $cupom) : ?>

                    <?php if (($cupom["resgatados"] > 0)
                        || ($cupom["usados"] > 0)
                        || ($cupom["gotas"] > 0)
                        || ($cupom["dinheiro"] > 0)
                        || ($cupom["brindes"] > 0)
                        || ($cupom["compras"] > 0)
                    ) : ?>

                        <h5>Brinde: <?= $cupom["nomeBrinde"] ?></h5>

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
                <div class="total-geral">
                    <h4>Soma Parcial do Funcionário <?= $dadoVenda["nome"] ?></h4>
                    <ul class="list-group">
                        <li class="list-group-item"> Brindes Resgatados: <?= $somaAtual["somaResgatados"] ?> </li>
                        <li class="list-group-item"> Brindes Usados: <?= $somaAtual["somaUsados"] ?> </li>
                        <li class="list-group-item"> Gotas Bonificadas: <?= $somaAtual["somaGotas"] ?> </li>
                        <li class="list-group-item"> Dinheiro Recebido: <?= $this->Number->currency($somaAtual["somaDinheiro"]) ?> </li>
                        <li class="list-group-item"> Bonificação: <?= $somaAtual["somaBrindes"] ?> </li>
                        <li class="list-group-item"> Vendas: <?= $somaAtual["somaCompras"] ?> </li>
                    </ul>
                </div>
            </p>

            <div class="total-geral">
                <h4>Total Geral</h4>
                <ul class="list-group">
                    <li class="list-group-item"> Total de Brindes Resgatados: <?= $totalGeral["totalResgatados"] ?> </li>
                    <li class="list-group-item"> Total de Brindes Usados: <?= $totalGeral["totalUsados"] ?> </li>
                    <li class="list-group-item"> Total de Gotas Bonificadas: <?= $totalGeral["totalGotas"] ?> </li>
                    <li class="list-group-item"> Total de Dinheiro Recebido: <?= $this->Number->currency($totalGeral["totalDinheiro"]) ?> </li>
                    <li class="list-group-item"> Total de Bonificação: <?= $totalGeral["totalBrindes"] ?> </li>
                    <li class="list-group-item"> Total de Vendas: <?= $totalGeral["totalCompras"] ?> </li>
                </ul>
            </div>

        <?php endforeach; ?>
    </div>

    <?php
    // Adiciona comportamento jquery
    $extensionJs = $debug ? ".js" : ".min.js";
    $extensionCss = $debug ? ".css" : ".min.css";
    echo $this->Html->script('scripts/cupons/relatorio_caixa_funcionario' . $extensionJs);
    echo $this->Html->css("styles/cupons/relatorio_caixa_funcionario" . $extensionCss);
    echo $this->fetch("script");
    ?>