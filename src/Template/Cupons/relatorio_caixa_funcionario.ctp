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
                                        "value" => $tipoFiltroSelecionado,
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
                            </i>
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
        <?php else : ?>
            <h4 class="text-center">Utilize um dos filtros para gerar o relatório!</h4>

        <?php endif; ?>

        <?php if ($tipoFiltroSelecionado == FILTER_TYPE_DATE_TIME && count($dadosVendaFuncionarios) > 0) : ?>
            <!-- Se filtro por hora, exibir mensagem: -->
            <h4 class="text-center">Relatório Parcial de Caixa do Funcionário, não vale como Relatório Oficial!</h4>
        <?php endif; ?>

        <?php foreach ($dadosVendaFuncionarios as $dados) : ?>
            <?php
            $turnos = $dados["turnos"];
            $somaAtual = $dados["soma"];
            ?>
            <h4>Funcionário: <?= $dados["nome"] ?></h4>
            <p>
                <?php foreach ($turnos as $turno) : ?>

                    <h4>Turno: De <?= $turno["horario_inicio"] ?> às <?= $turno["horario_fim"] ?> </h4>
                    <?php if (($turno["somaTurno"]["resgatados"] > 0)
                        || ($turno["somaTurno"]["usados"] > 0)
                        || ($turno["somaTurno"]["gotas"] > 0)
                        || ($turno["somaTurno"]["dinheiro"] > 0)
                        || ($turno["somaTurno"]["brindes"] > 0)
                        || ($turno["somaTurno"]["compras"] > 0)
                    ) : ?>

                        <?php foreach ($turno["cupons"] as $cupom) : ?>
                            <?php if (($cupom["resgatados"] > 0)
                                || ($cupom["usados"] > 0)
                                || ($cupom["gotas"] > 0)
                                || ($cupom["dinheiro"] > 0)
                                || ($cupom["brindes"] > 0)
                                || ($cupom["compras"] > 0)
                            ) : ?>
                                <h5>Brinde: <?= $cupom["nome_brinde"] ?></h5>

                                <?php if ($cupom["resgatados"] > 0 || $cupom["usados"] > 0) : ?>
                                    <li class="list-group-item">Brindes Resgatados: <?= $cupom["resgatados"] ?> / Validados: <?= $cupom["usados"] ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["gotas"] > 0) : ?>
                                    <!-- Qte de gotas recebido -->
                                    <li class="list-group-item">Gotas Resgatadas: <?= $cupom["gotas"] ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["dinheiro"] > 0) : ?>
                                    <!-- Qte de dinheiro recebido daquele brinde -->
                                    <li class="list-group-item">Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["brindes"] > 0) : ?>
                                    <!-- Qte de Brindes vendidos via gotas -->
                                    <li class="list-group-item">Bonificação: <?= $cupom["brindes"] ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["compras"] > 0) : ?>
                                    <!-- Qte de Brindes vendidos via dinheiro -->
                                    <li class="list-group-item">Vendas: <?= $cupom["compras"] ?> </li>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <div class="soma-turno">
                            <h4>Soma Turno</h4>
                            <ul class="list-group">
                                <li class="list-group-item"> Brindes Resgatados: <?= $turno["somaTurno"]["resgatados"] ?> / Validados: <?= $turno["somaTurno"]["usados"] ?> </li>
                                <li class="list-group-item"> Gotas Resgatadas: <?= $turno["somaTurno"]["gotas"] ?> </li>
                                <li class="list-group-item"> Dinheiro Recebido: <?= $this->Number->currency($turno["somaTurno"]["dinheiro"]) ?> </li>
                                <li class="list-group-item"> Bonificação: <?= $turno["somaTurno"]["brindes"] ?> </li>
                                <li class="list-group-item"> Vendas: <?= $turno["somaTurno"]["compras"] ?> </li>
                            </ul>
                        </div>
                    <?php else : ?>
                        <h4 class="text-center">Não há dados para o turno!</h4>
                    <?php endif; ?>
                <?php endforeach; ?>
            </p>

            <div class="total-geral">
                <h4>Total Geral</h4>
                <ul class="list-group">
                    <li class="list-group-item"> Total de Brindes Resgatados: <?= $totalGeral["totalResgatados"] ?> / Validados: <?= $totalGeral["totalUsados"] ?> </li>
                    <li class="list-group-item"> Total de Gotas Resgatadas: <?= $totalGeral["totalGotas"] ?> </li>
                    <li class="list-group-item"> Total de Dinheiro Recebido: <?= $this->Number->currency($totalGeral["totalDinheiro"]) ?> </li>
                    <li class="list-group-item"> Total de Bonificação: <?= $totalGeral["totalBrindes"] ?> </li>
                    <li class="list-group-item"> Total de Vendas: <?= $totalGeral["totalCompras"] ?> </li>
                </ul>
            </div>

        <?php endforeach; ?>
    </div>

    <div class="print-area-thermal col-lg-3 print-thermal">

        <?php if (!empty($tituloTurno)) : ?>
            <h4><?= $tituloTurno ?></h4>
        <?php else : ?>
            <h5 class="text-center">Utilize um dos filtros para gerar o relatório!</h5>

        <?php endif; ?>

        <?php if ($tipoFiltroSelecionado == FILTER_TYPE_DATE_TIME && count($dadosVendaFuncionarios) > 0) : ?>
            <!-- Se filtro por hora, exibir mensagem: -->
            <h6 class="text-center">Relatório Parcial de Caixa do Funcionário, não vale como Relatório Oficial!</h6>
        <?php endif; ?>

        <?php foreach ($dadosVendaFuncionarios as $dados) : ?>
            <?php
            $turnos = $dados["turnos"];
            $somaAtual = $dados["soma"];
            ?>
            <h6>Funcionário: <?= $dados["nome"] ?></h6>
            <p>
                <?php foreach ($turnos as $turno) : ?>

                    <span>Turno: <br /> De <?= $turno["horario_inicio"] ?> <br /> às <?= $turno["horario_fim"] ?> </span>
                    <?php if (($turno["somaTurno"]["resgatados"] > 0)
                        || ($turno["somaTurno"]["usados"] > 0)
                        || ($turno["somaTurno"]["gotas"] > 0)
                        || ($turno["somaTurno"]["dinheiro"] > 0)
                        || ($turno["somaTurno"]["brindes"] > 0)
                        || ($turno["somaTurno"]["compras"] > 0)
                    ) : ?>

                        <?php foreach ($turno["cupons"] as $cupom) : ?>
                            <?php if (($cupom["resgatados"] > 0)
                                || ($cupom["usados"] > 0)
                                || ($cupom["gotas"] > 0)
                                || ($cupom["dinheiro"] > 0)
                                || ($cupom["brindes"] > 0)
                                || ($cupom["compras"] > 0)
                            ) : ?>
                                <br />
                                <strong>Brinde: <?= $cupom["nome_brinde"] ?></strong>

                                <?php if ($cupom["resgatados"] > 0 || $cupom["usados"] > 0) : ?>
                                    <li class="list-group-item">Brindes Resgatados: <?= $cupom["resgatados"] ?> / Validados: <?= $cupom["usados"] ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["gotas"] > 0) : ?>
                                    <!-- Qte de gotas recebido -->
                                    <li class="list-group-item">Gotas Resgatadas: <?= $cupom["gotas"] ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["dinheiro"] > 0) : ?>
                                    <!-- Qte de dinheiro recebido daquele brinde -->
                                    <li class="list-group-item">Dinheiro Recebido: <?= $this->Number->currency($cupom["dinheiro"]) ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["brindes"] > 0) : ?>
                                    <!-- Qte de Brindes vendidos via gotas -->
                                    <li class="list-group-item">Bonificação: <?= $cupom["brindes"] ?> </li>
                                <?php endif; ?>

                                <?php if ($cupom["compras"] > 0) : ?>
                                    <!-- Qte de Brindes vendidos via dinheiro -->
                                    <li class="list-group-item">Vendas: <?= $cupom["compras"] ?> </li>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <div class="soma-turno">
                            <h4>Total Turno</h4>
                            <ul class="list-group">
                                <li class="list-group-item"> Brindes Resgatados: <?= $turno["somaTurno"]["resgatados"] ?> / Validados: <?= $turno["somaTurno"]["usados"] ?> </li>
                                <li class="list-group-item"> Gotas Resgatadas: <?= $turno["somaTurno"]["gotas"] ?> </li>
                                <li class="list-group-item"> Dinheiro Recebido: <?= $this->Number->currency($turno["somaTurno"]["dinheiro"]) ?> </li>
                                <li class="list-group-item"> Bonificação: <?= $turno["somaTurno"]["brindes"] ?> </li>
                                <li class="list-group-item"> Vendas: <?= $turno["somaTurno"]["compras"] ?> </li>
                            </ul>
                        </div>
                    <?php else : ?>
                        <h5 class="text-center">Não há dados para o turno!</h5>
                    <?php endif; ?>
                <?php endforeach; ?>
            </p>

            <div class="total-geral">
                <h4>Total Geral</h4>
                <ul class="list-group">
                    <li class="list-group-item"> Total de Brindes Resgatados: <?= $totalGeral["totalResgatados"] ?> / Validados: <?= $totalGeral["totalUsados"] ?> </li>
                    <li class="list-group-item"> Total de Gotas Resgatadas: <?= $totalGeral["totalGotas"] ?> </li>
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
