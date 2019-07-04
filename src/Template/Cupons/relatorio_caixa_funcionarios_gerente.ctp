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
use Cake\I18n\Number;

$debug = Configure::read("debug");

$title = "Relatório de Caixa de Funcionários";
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

$totalGeral = $dadosRelatorio["total"];

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
                            <div class="col-lg-4">

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
                            <div class="col-lg-4">

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
                                <label for="dataInicio">Data de Pesquisa:</label>
                                <input type="text" class="form-control datepicker-input" format="d/m/Y" name="data_pesquisa" id="data_pesquisa" placeholder="Data Início...">
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

                            <input type="hidden" name="data_pesquisa_envio" id="data_pesquisa_envio" value="<?= $dataPesquisa ?>" class="data-pesquisa-envio">

                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary botao-pesquisar">
                                    <span class="fa fa-search"></span>
                                    Pesquisar
                                </button>
                                <?php if (count($dadosRelatorio) > 0) : ?>
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

            <?php if (!empty($dadosRelatorio) && ($totalGeral["resgatados"] > 0 || $totalGeral["usados"] > 0
                || $totalGeral["valor_pago_gotas"] > 0 || $totalGeral["valor_pago_reais"] > 0
                || $totalGeral["brindes"] > 0 || $totalGeral["compras"] > 0)) : ?>
                <h1 class="text-center">Relatório de Caixa de Funcionários:</h1>
                <h4 class="text-center"><?= sprintf("De: %s Às %s: ", $dataInicioFormatada, $dataFimFormatada) ?></h4>

                <!-- Primeiro nível, funcinários -->
                <?php foreach ($dadosRelatorio["funcionarios"] as $key => $funcionario) : ?>
                    <?php
                    $turnos = $tipoRelatorio == REPORT_TYPE_ANALYTICAL ? $funcionario[REPORT_TYPE_ANALYTICAL] : $funcionario[REPORT_TYPE_SYNTHETIC];
                    $somaFuncionario = $funcionario["soma"];
                    ?>
                    <?php if ($somaFuncionario["resgatados"] > 0 || $somaFuncionario["usados"] > 0 || $somaFuncionario["valor_pago_gotas"] > 0 || $somaFuncionario["valor_pago_reais"] > 0 || $somaFuncionario["brindes"] > 0 || $somaFuncionario["compras"] > 0) : ?>

                        <h3 class="text-center">Funcionário: <?= $funcionario["nome"] ?></h3>
                        <p>
                            <?php foreach ($turnos as $turno) : ?>

                                <h4 class="text-center">Turno: <?= sprintf("%s às %s", $turno["horario_inicio"], $turno["horario_fim"]); ?></h4>

                                <?php if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) : ?>
                                    <!-- ANALÍTICO -->
                                    <div class="form-group">

                                        <!-- Exibe dados descritivo, se algum usuário fez a compra do brinde -->
                                        <?php if (count($turno["brindes"]) > 0) : ?>

                                            <?php foreach ($turno["brindes"] as $brinde) : ?>

                                                <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="5">
                                                                <h1 class='text-center'>Brinde: <?= $brinde["nome"] ?></h1>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Dados de Usuários -->
                                                        <?php foreach ($brinde["usuarios"] as $usuario) : ?>
                                                            <tr>
                                                                <th colspan="5">
                                                                    <h2 class="text-center"><?= $usuario["nome"] ?></h2>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h5>Brindes Resgatados / Validados</h5>
                                                                </td>
                                                                <td>
                                                                    <h5>Gotas Resgatadas</h5>
                                                                </td>
                                                                <td>
                                                                    <h5>Dinheiro Recebido</h5>
                                                                </td>
                                                                <td>
                                                                    <h5>Bonificação</h5>
                                                                </td>
                                                                <td>
                                                                    <h5>Vendas</h5>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= sprintf("%s / %s", $usuario["soma"]["resgatados"], $usuario["soma"]["usados"]) ?></td>
                                                                <td><?= $usuario["soma"]["valor_pago_gotas"] ?></td>
                                                                <td><?= Number::currency($usuario["soma"]["valor_pago_reais"]) ?></td>
                                                                <td><?= $usuario["soma"]["brindes"] ?></td>
                                                                <td><?= $usuario["soma"]["compras"] ?></td>
                                                            </tr>

                                                        <?php endforeach; ?>

                                                        <!-- Soma do Brinde -->
                                                        <table class="table table-bordered table-hover table-responsive table-striped table-condensed" colspan="6">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="5">
                                                                        <h3 class="text-center">Soma do Brinde <?= $brinde["nome"] ?></h3>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <h5>Brindes Resgatados / Validados</h5>
                                                                    </td>
                                                                    <td>
                                                                        <h5>Gotas Resgatadas</h5>
                                                                    </td>
                                                                    <td>
                                                                        <h5>Dinheiro Recebido</h5>
                                                                    </td>
                                                                    <td>
                                                                        <h5>Bonificação</h5>
                                                                    </td>
                                                                    <td>
                                                                        <h5>Vendas</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><?= sprintf("%s / %s", $brinde["soma"]["resgatados"], $brinde["soma"]["usados"]) ?></td>
                                                                    <td><?= $brinde["soma"]["valor_pago_gotas"] ?></td>
                                                                    <td><?= Number::currency($brinde["soma"]["valor_pago_reais"]) ?></td>
                                                                    <td><?= $brinde["soma"]["brindes"] ?></td>
                                                                    <td><?= $brinde["soma"]["compras"] ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </tbody>
                                                </table>

                                                <!-- Soma do Turno -->
                                                <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="5">
                                                                <h3 class="text-center">Soma do Turno: <?= sprintf("%s às %s", $turno["horario_inicio"], $turno["horario_fim"]); ?></h3>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <h5>Brindes Resgatados / Validados</h5>
                                                            </td>
                                                            <td>
                                                                <h5>Gotas Resgatadas</h5>
                                                            </td>
                                                            <td>
                                                                <h5>Dinheiro Recebido</h5>
                                                            </td>
                                                            <td>
                                                                <h5>Bonificação</h5>
                                                            </td>
                                                            <td>
                                                                <h5>Vendas</h5>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= sprintf("%s / %s", $turno["soma"]["resgatados"], $turno["soma"]["usados"]) ?></td>
                                                            <td><?= $turno["soma"]["valor_pago_gotas"] ?></td>
                                                            <td><?= Number::currency($turno["soma"]["valor_pago_reais"]) ?></td>
                                                            <td><?= $turno["soma"]["brindes"] ?></td>
                                                            <td><?= $turno["soma"]["compras"] ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <!-- Caso contrário, não há dados -->
                                            <h3>Não há dados para o turno!</h3>
                                        <?php endif; ?>

                                    </div>

                                <?php else : ?>
                                    <!-- SINTÉTICO -->
                                    <?php if (($turno["resgatados"] > 0)
                                        || ($turno["usados"] > 0)
                                        || ($turno["gotas"] > 0)
                                        || ($turno["dinheiro"] > 0)
                                        || ($turno["brindes"] > 0)
                                        || ($turno["compras"] > 0)
                                    ) : ?>

                                        <h5>Brinde: <?= $turno["nomeBrinde"] ?></h5>

                                        <?php if ($turno["resgatados"] > 0) : ?>
                                            <li class="list-group-item">Brindes Resgatados: <?= $turno["resgatados"] ?> </li>
                                        <?php endif; ?>

                                        <?php if ($turno["usados"] > 0) : ?>
                                            <li class="list-group-item">Brindes Usados: <?= $turno["usados"] ?> </li>
                                        <?php endif; ?>
                                        <?php if ($turno["gotas"] > 0) : ?>
                                            <!-- Qte de gotas recebido -->
                                            <li class="list-group-item">Total de Gotas Bonificadas: <?= $turno["gotas"] ?> </li>
                                        <?php endif; ?>

                                        <?php if ($turno["dinheiro"] > 0) : ?>
                                            <!-- Qte de dinheiro recebido daquele brinde -->
                                            <li class="list-group-item">Total de Dinheiro Recebido: <?= $this->Number->currency($turno["dinheiro"]) ?> </li>
                                        <?php endif; ?>

                                        <?php if ($turno["brindes"] > 0) : ?>
                                            <!-- Qte de Brindes vendidos via gotas -->
                                            <li class="list-group-item">Total de Bonificação: <?= $turno["brindes"] ?> </li>
                                        <?php endif; ?>

                                        <?php if ($turno["compras"] > 0) : ?>
                                            <!-- Qte de Brindes vendidos via dinheiro -->
                                            <li class="list-group-item">Total de Vendas: <?= $turno["compras"] ?> </li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                <?php endif; ?>
                            <?php endforeach; ?>
                        </p>
                    <?php endif; ?>
                    <!-- Soma Funcionário -->

                    <?php if ($somaFuncionario["resgatados"]  > 0 || $somaFuncionario["usados"] > 0 || $somaFuncionario["valor_pago_gotas"] > 0 || $somaFuncionario["valor_pago_reais"] > 0 || $somaFuncionario["brindes"] > 0 || $somaFuncionario["compras"] > 0) : ?>

                        <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th colspan="5">
                                        <h3 class="text-center">Soma Funcionário <?= $funcionario["nome"] ?>:</h3>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h5>Brindes Resgatados / Validados</h5>
                                    </td>
                                    <td>
                                        <h5>Gotas Resgatadas</h5>
                                    </td>
                                    <td>
                                        <h5>Dinheiro Recebido</h5>
                                    </td>
                                    <td>
                                        <h5>Bonificação</h5>
                                    </td>
                                    <td>
                                        <h5>Vendas</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= sprintf("%s / %s", $somaFuncionario["resgatados"], $somaFuncionario["usados"]) ?></td>
                                    <td><?= $somaFuncionario["valor_pago_gotas"] ?></td>
                                    <td><?= Number::currency($somaFuncionario["valor_pago_reais"]) ?></td>
                                    <td><?= $somaFuncionario["brindes"] ?></td>
                                    <td><?= $somaFuncionario["compras"] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>

                <?php endforeach; ?>


                <?php if (
                    $totalGeral["resgatados"] > 0 || $totalGeral["usados"] > 0
                    || $totalGeral["valor_pago_gotas"] > 0 || $totalGeral["valor_pago_reais"] > 0
                    || $totalGeral["brindes"] > 0 || $totalGeral["compras"] > 0
                ) : ?>
                    <div class="total-geral">
                        <h4>Total Geral</h4>
                        <ul class="list-group">
                            <li class="list-group-item"> Total de Brindes Resgatados: <?= $totalGeral["resgatados"] ?> / Validados: <?= $totalGeral["usados"] ?> </li>
                            <li class="list-group-item"> Total de Gotas Bonificadas: <?= $totalGeral["valor_pago_gotas"] ?> </li>
                            <li class="list-group-item"> Total de Dinheiro Recebido: <?= $this->Number->currency($totalGeral["valor_pago_reais"]) ?> </li>
                            <li class="list-group-item"> Total de Bonificação: <?= $totalGeral["brindes"] ?> </li>
                            <li class="list-group-item"> Total de Vendas: <?= $totalGeral["compras"] ?> </li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <h4 class="text-center">Utilize um dos filtros para gerar o relatório!</h4>
            <?php endif; ?>

        </div>

        <div class="print-area-thermal col-lg-3 print-thermal">
            <?php if (!empty($dadosRelatorio)) : ?>
                <h3 class="text-center">Relatório de Caixa de Funcionários:</h3>
                <p class="text-center"><?= sprintf("De: %s <br />Às %s: ", $dataInicioFormatada, $dataFimFormatada) ?></p>
            <?php else : ?>
                <h4 class="text-center">Utilize um dos filtros para gerar o relatório!</h4>
            <?php endif; ?>
            <?php foreach ($dadosRelatorio as $key => $funcionario) : ?>
                <?php
                $turnos = $tipoRelatorio == REPORT_TYPE_ANALYTICAL ? $funcionario[REPORT_TYPE_ANALYTICAL] : $funcionario[REPORT_TYPE_SYNTHETIC];
                $somaFuncionario = $funcionario["soma"];
                ?>
                <?php if ($somaFuncionario["somaResgatados"] > 0 || $somaFuncionario["somaUsados"] > 0 || $somaFuncionario["somaGotas"] > 0 || $somaFuncionario["somaDinheiro"] > 0 || $somaFuncionario["somaBrindes"] > 0 || $somaFuncionario["somaCompras"] > 0) : ?>

                    <h4>Funcionário: <?= $funcionario["nome"] ?></h4>
                    <p>
                        <?php foreach ($turnos as $turno) : ?>

                            <?php if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) : ?>
                                <div class="form-group">
                                    <?php if (count($turno["brinde"]["cupons"]) > 0) : ?>
                                        <?php foreach ($turno["brinde"]["cupons"] as $usuarios) : ?>
                                            <?php foreach ($usuarios as  $usuario) : ?>

                                                <?php if (count($usuario["dados"]) > 0) : ?>

                                                    <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="2"><strong>Brinde:</strong> <?= $turno["brinde"]["nome"] ?></th>
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
                                <?php if (($turno["resgatados"] > 0)
                                    || ($turno["usados"] > 0)
                                    || ($turno["gotas"] > 0)
                                    || ($turno["dinheiro"] > 0)
                                    || ($turno["brindes"] > 0)
                                    || ($turno["compras"] > 0)
                                ) : ?>

                                    <h5>Brinde: <?= $turno["nomeBrinde"] ?></h5>


                                    <?php if ($turno["resgatados"] > 0) : ?>
                                        <li class="list-group-item">Brindes Resgatados: <?= $turno["resgatados"] ?> </li>
                                    <?php endif; ?>

                                    <?php if ($turno["usados"] > 0) : ?>
                                        <li class="list-group-item">Brindes Usados: <?= $turno["usados"] ?> </li>
                                    <?php endif; ?>
                                    <?php if ($turno["gotas"] > 0) : ?>
                                        <!-- Qte de gotas recebido -->
                                        <li class="list-group-item">Total de Gotas Bonificadas: <?= $turno["gotas"] ?> </li>
                                    <?php endif; ?>

                                    <?php if ($turno["dinheiro"] > 0) : ?>
                                        <!-- Qte de dinheiro recebido daquele brinde -->
                                        <li class="list-group-item">Total de Dinheiro Recebido: <?= $this->Number->currency($turno["dinheiro"]) ?> </li>
                                    <?php endif; ?>

                                    <?php if ($turno["brindes"] > 0) : ?>
                                        <!-- Qte de Brindes vendidos via gotas -->
                                        <li class="list-group-item">Total de Bonificação: <?= $turno["brindes"] ?> </li>
                                    <?php endif; ?>

                                    <?php if ($turno["compras"] > 0) : ?>
                                        <!-- Qte de Brindes vendidos via dinheiro -->
                                        <li class="list-group-item">Total de Vendas: <?= $turno["compras"] ?> </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($somaFuncionario["somaResgatados"] > 0 || $somaFuncionario["somaUsados"] > 0 || $somaFuncionario["somaGotas"] > 0 || $somaFuncionario["somaDinheiro"] > 0 || $somaFuncionario["somaBrindes"] > 0 || $somaFuncionario["somaCompras"] > 0) : ?>
                            <div class="total-geral">
                                <h4>Soma Parcial do Funcionário <?= $funcionario["nome"] ?></h4>
                                <ul class="list-group">
                                    <li class="list-group-item"> Brindes Resgatados: <?= $somaFuncionario["somaResgatados"] ?> </li>
                                    <li class="list-group-item"> Brindes Usados: <?= $somaFuncionario["somaUsados"] ?> </li>
                                    <li class="list-group-item"> Gotas Bonificadas: <?= $somaFuncionario["somaGotas"] ?> </li>
                                    <li class="list-group-item"> Dinheiro Recebido: <?= $this->Number->currency($somaFuncionario["somaDinheiro"]) ?> </li>
                                    <li class="list-group-item"> Bonificação: <?= $somaFuncionario["somaBrindes"] ?> </li>
                                    <li class="list-group-item"> Vendas: <?= $somaFuncionario["somaCompras"] ?> </li>
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