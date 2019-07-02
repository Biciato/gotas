<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src\Template\Cupons\relatorio_caixa_funcionarios_gerente.ctp
 * @date     06/08/2017
 *
 * Tela de Relatório de Caixa dos Funcionários de um Posto. Visualizado por um gerente
 */

use Cake\Core\Configure;
use Cake\View\Helper\NumberHelper;
use App\Custom\RTI\DebugUtil;
use App\View\Helper\BooleanHelper;

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
                    <form action="/cupons/relatorioCaixaFuncionariosGerente/" method="post">
                        <div class="form-group row">
                            <div class="col-lg-3">

                                <label for="funcionario">Funcionário:</label>
                                <?= $this->Form->input(
                                    "funcionario",
                                    array(
                                        "type" => "select",
                                        "name" => "funcionario",
                                        "id" => "funcionario",
                                        "empty" => "Selecionar...",
                                        "placeholder" => "Funcionário...",
                                        "label" => false,
                                        "options" => $funcionariosList,
                                        "value" => $funcionarioSelecionado,
                                    )
                                );
                                ?>

                            </div>
                            <div class="col-lg-3">

                                <label for="brinde">Brinde:</label>
                                <?= $this->Form->input(
                                    "brinde",
                                    array(
                                        "type" => "select",
                                        "name" => "brinde",
                                        "id" => "brinde",
                                        "empty" => "Selecionar...",
                                        "placeholder" => "Brinde...",
                                        "label" => false,
                                        "options" => $brindesList,
                                        "value" => $brindeSelecionado,
                                    )
                                );
                                ?>

                            </div>

                            <div class="col-lg-2">
                                <label for="dataInicio">Data Início:</label>
                                <input type="text" class="form-control datepicker-input" format="d/m/Y" name="data_inicio" id="data_inicio" placeholder="Data Início...">
                            </div>
                            <div class="col-lg-2">
                                <label for="dataFim">Data Fim:</label>
                                <input type="text" class="form-control datepicker-input" format="d/m/Y" name="data_fim" id="data_fim" placeholder="Data Fim...">
                            </div>

                            <div class="col-lg-2">
                                <label for="tipo_relatorio">Tipo de Relatório:</label>
                                <?= $this->Form->input(
                                    "tipo_relatorio",
                                    array(
                                        "type" => "select",
                                        "name" => "tipo_relatorio",
                                        "id" => "tipo_relatorio",
                                        "label" => false,
                                        "options" => array(
                                            REPORT_TYPE_ANALYTICAL => REPORT_TYPE_ANALYTICAL,
                                            REPORT_TYPE_SYNTHETIC => REPORT_TYPE_SYNTHETIC
                                        ),
                                        "value" => $tipoRelatorio
                                    )
                                ); ?>
                            </div>

                            <input type="text" name="data_inicio_envio" id="data_inicio_envio" value="<?= $dataInicio ?>" class="data-inicio-envio">
                            <input type="text" name="data_fim_envio" id="data_fim_envio" value="<?= $dataFim ?>" class="data-fim-envio">

                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary botao-pesquisar">
                                    <span class="fa fa-search"></span>
                                    Pesquisar
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

                    </form>

                </div>
            </div>
        </div>

        <div class="col-lg-12 print-area-common">

            <?php if (!empty($dadosVendaFuncionarios)) : ?>
                <h2 class="text-center">Relatório de Caixa de Funcionários:</h2>
                <p class="text-center"><?= sprintf("De: %s Às %s: ", $dataInicioFormatada, $dataFimFormatada) ?></p>
            <?php else : ?>
                <h4 class="text-center">Utilize um dos filtros para gerar o relatório!</h4>
            <?php endif; ?>
            <?php foreach ($dadosVendaFuncionarios as $key => $dadoVenda) : ?>
                <?php
                $cupons = $tipoRelatorio == REPORT_TYPE_ANALYTICAL ? $dadoVenda[REPORT_TYPE_ANALYTICAL] : $dadoVenda[REPORT_TYPE_SYNTHETIC];
                $somaAtual = $dadoVenda["soma"];
                ?>
                <?php if ($somaAtual["somaResgatados"] > 0 || $somaAtual["somaUsados"] > 0 || $somaAtual["somaGotas"] > 0 || $somaAtual["somaDinheiro"] > 0 || $somaAtual["somaBrindes"] > 0 || $somaAtual["somaCompras"] > 0) : ?>

                    <h3>Funcionário: <?= $dadoVenda["nome"] ?></h3>
                    <p>
                        <?php foreach ($cupons as $cupom) : ?>

                            <?php if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) : ?>
                                <div class="form-group">
                                    <?php if (count($cupom["brinde"]["cupons"]) > 0) : ?>
                                        <?php foreach ($cupom["brinde"]["cupons"] as $usuarios) : ?>
                                            <?php foreach ($usuarios as  $usuario) : ?>

                                                <?php if (count($usuario["dados"]) > 0) : ?>

                                                    <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="2"><strong>Brinde:</strong> <?= $cupom["brinde"]["nome"] ?></th>
                                                                <th colspan="3">Cliente: <?= $usuario["nome"] ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Resgatado</td>
                                                                <td>Usado</td>
                                                                <td>Valor Pago em Gotas</td>
                                                                <td>Valor Pago em Reais</td>
                                                                <td>Data</td>
                                                            </tr>
                                                            <?php foreach ($usuario["dados"] as $dado) : ?>
                                                                <tr>
                                                                    <td><?= $this->Boolean->convertBooleanToString($dado["resgatado"]) ?></td>
                                                                    <td><?= $this->Boolean->convertBooleanToString($dado["usado"]) ?></td>
                                                                    <td><?= $this->Number->precision($dado["valor_pago_gotas"], 2); ?></td>
                                                                    <td><?= $this->Number->precision($dado["valor_pago_reais"], 2); ?></td>
                                                                    <td><?= $dado["data"] ?></td>


                                                                </tr>
                                                            <?php endforeach; ?>

                                                        </tbody>
                                                    </table>

                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>

                                    <?php endif; ?>
                                </div>

                            <?php else : ?>
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

                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($somaAtual["somaResgatados"] > 0 || $somaAtual["somaUsados"] > 0 || $somaAtual["somaGotas"] > 0 || $somaAtual["somaDinheiro"] > 0 || $somaAtual["somaBrindes"] > 0 || $somaAtual["somaCompras"] > 0) : ?>
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
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

            <?php endforeach; ?>


            <?php if (
                $totalGeral["totalResgatados"] > 0 || $totalGeral["totalUsados"] > 0
                || $totalGeral["totalGotas"] > 0 || $totalGeral["totalDinheiro"] > 0
                || $totalGeral["totalBrindes"] > 0 || $totalGeral["totalCompras"] > 0
            ) : ?>
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
            <?php endif; ?>
        </div>

        <div class="print-area-thermal col-lg-3 print-thermal">
            <?php if (!empty($dadosVendaFuncionarios)) : ?>
                <h3 class="text-center">Relatório de Caixa de Funcionários:</h3>
                <p class="text-center"><?= sprintf("De: %s <br />Às %s: ", $dataInicioFormatada, $dataFimFormatada) ?></p>
            <?php else : ?>
                <h4 class="text-center">Utilize um dos filtros para gerar o relatório!</h4>
            <?php endif; ?>
            <?php foreach ($dadosVendaFuncionarios as $key => $dadoVenda) : ?>
                <?php
                $cupons = $tipoRelatorio == REPORT_TYPE_ANALYTICAL ? $dadoVenda[REPORT_TYPE_ANALYTICAL] : $dadoVenda[REPORT_TYPE_SYNTHETIC];
                $somaAtual = $dadoVenda["soma"];
                ?>
                <?php if ($somaAtual["somaResgatados"] > 0 || $somaAtual["somaUsados"] > 0 || $somaAtual["somaGotas"] > 0 || $somaAtual["somaDinheiro"] > 0 || $somaAtual["somaBrindes"] > 0 || $somaAtual["somaCompras"] > 0) : ?>

                    <h4>Funcionário: <?= $dadoVenda["nome"] ?></h4>
                    <p>
                        <?php foreach ($cupons as $cupom) : ?>

                            <?php if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) : ?>
                                <div class="form-group">
                                    <?php if (count($cupom["brinde"]["cupons"]) > 0) : ?>
                                        <?php foreach ($cupom["brinde"]["cupons"] as $usuarios) : ?>
                                            <?php foreach ($usuarios as  $usuario) : ?>

                                                <?php if (count($usuario["dados"]) > 0) : ?>

                                                    <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="2"><strong>Brinde:</strong> <?= $cupom["brinde"]["nome"] ?></th>
                                                                <th colspan="3">Cliente: <?= $usuario["nome"] ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Resgatado</td>
                                                                <td>Usado</td>
                                                                <td>Valor Pago em Gotas</td>
                                                                <td>Valor Pago em Reais</td>
                                                                <td>Data</td>
                                                            </tr>
                                                            <?php foreach ($usuario["dados"] as $dado) : ?>
                                                                <tr>
                                                                    <td><?= $this->Boolean->convertBooleanToString($dado["resgatado"]) ?></td>
                                                                    <td><?= $this->Boolean->convertBooleanToString($dado["usado"]) ?></td>
                                                                    <td><?= $this->Number->precision($dado["valor_pago_gotas"], 2); ?></td>
                                                                    <td><?= $this->Number->precision($dado["valor_pago_reais"], 2); ?></td>
                                                                    <td><?= $dado["data"] ?></td>


                                                                </tr>
                                                            <?php endforeach; ?>

                                                        </tbody>
                                                    </table>

                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>

                                    <?php endif; ?>
                                </div>

                            <?php else : ?>
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

                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($somaAtual["somaResgatados"] > 0 || $somaAtual["somaUsados"] > 0 || $somaAtual["somaGotas"] > 0 || $somaAtual["somaDinheiro"] > 0 || $somaAtual["somaBrindes"] > 0 || $somaAtual["somaCompras"] > 0) : ?>
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
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

            <?php endforeach; ?>


            <?php if (
                $totalGeral["totalResgatados"] > 0 || $totalGeral["totalUsados"] > 0
                || $totalGeral["totalGotas"] > 0 || $totalGeral["totalDinheiro"] > 0
                || $totalGeral["totalBrindes"] > 0 || $totalGeral["totalCompras"] > 0
            ) : ?>
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
            <?php endif; ?>
        </div>

        <?php
        // Adiciona comportamento jquery
        $extensionJs = $debug ? ".js" : ".min.js";
        $extensionCss = $debug ? ".css" : ".min.css";
        echo $this->Html->script('scripts/cupons/relatorio_caixa_funcionarios_gerente' . $extensionJs);
        echo $this->Html->css("styles/cupons/relatorio_caixa_funcionarios_gerente" . $extensionCss);
        echo $this->fetch("script");
        ?>