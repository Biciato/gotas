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

$totalGeral = !empty($dadosRelatorio["total"]) ? $dadosRelatorio["total"] : array();

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

            <?php if ( !empty($dadosRelatorio) && (count($totalGeral) > 0) && (($totalGeral["resgatados"] > 0 || $totalGeral["usados"] > 0 || $totalGeral["valor_pago_gotas"] > 0 || $totalGeral["valor_pago_reais"] > 0 || $totalGeral["brindes"] > 0 || $totalGeral["compras"] > 0))
            ) : ?>
                <h1 class="text-center">Relatório <?= $tipoRelatorio ?> de Caixa de Funcionários:</h1>
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

                                <?php if ($turno["soma"]["resgatados"] > 0 || $turno["soma"]["usados"] > 0 || $turno["soma"]["valor_pago_gotas"] > 0 || $turno["soma"]["valor_pago_reais"] > 0 || $turno["soma"]["brindes"] > 0 || $turno["soma"]["compras"] > 0) : ?>
                                    <h4 class="text-center">Turno: <?= sprintf("%s às %s", $turno["horario_inicio"], $turno["horario_fim"]); ?></h4>

                                    <?php if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) : ?>
                                        <!-- ANALÍTICO -->
                                        <div class="form-group">

                                            <!-- Exibe dados descritivo, se algum usuário fez a compra do brinde -->
                                            <?php if (count($turno["brindes"]) > 0) : ?>

                                                <?php foreach ($turno["brindes"] as $brinde) : ?>

                                                    <?php if ($brinde["soma"]["resgatados"] > 0 || $brinde["soma"]["usados"] > 0 || $brinde["soma"]["valor_pago_gotas"] > 0 || $brinde["soma"]["valor_pago_reais"] > 0 || $brinde["soma"]["brindes"] > 0 || $brinde["soma"]["compras"] > 0) : ?>

                                                        <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="5">
                                                                        <h1 class='text-center'>Brinde: <?= $brinde["nome"] ?></h1>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                        <?php foreach ($brinde["usuarios"] as $usuario) : ?>
                                                            <?php if ($usuario["soma"]["resgatados"] > 0 || $usuario["soma"]["usados"] > 0 || $usuario["soma"]["valor_pago_gotas"] > 0 || $usuario["soma"]["valor_pago_reais"] > 0 || $usuario["soma"]["brindes"] > 0 || $usuario["soma"]["compras"] > 0) : ?>

                                                                <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                                    <thead>
                                                                        <!-- Dados de Usuários -->
                                                                        <tr>
                                                                            <th colspan="5">
                                                                                <h2 class="text-center">Cliente: <?= $usuario["nome"] ?></h2>
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <h5>Resgatados / Validados</h5>
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

                                                                    </tbody>
                                                                </table>
                                                            <?php endif; ?>
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
                                                                        <h5>Resgatados / Validados</h5>
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
                                                    <?php endif; ?>


                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <!-- Caso contrário, não há dados -->
                                                <h3>Não há dados para o turno!</h3>
                                            <?php endif; ?>

                                        </div>

                                    <?php else : ?>
                                        <!-- SINTÉTICO -->

                                        <div class="form-group">

                                            <!-- Exibe dados descritivo, se algum usuário fez a compra do brinde -->
                                            <?php if (count($turno["brindes"]) > 0) : ?>

                                                <?php foreach ($turno["brindes"] as $brinde) : ?>

                                                    <?php if ($brinde["soma"]["resgatados"] > 0 || $brinde["soma"]["usados"] > 0 || $brinde["soma"]["valor_pago_gotas"] > 0 || $brinde["soma"]["valor_pago_reais"] > 0 || $brinde["soma"]["brindes"] > 0 || $brinde["soma"]["compras"] > 0) : ?>

                                                        <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="5">
                                                                        <h1 class='text-center'>Brinde: <?= $brinde["nome_brinde"] ?></h1>
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

                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($turno["soma"]["resgatados"] > 0 ||  $turno["soma"]["usados"] > 0 ||  $turno["soma"]["valor_pago_gotas"]  > 0 ||  $turno["soma"]["valor_pago_reais"] > 0 ||  $turno["soma"]["brindes"]  > 0 ||  $turno["soma"]["compras"]  > 0) : ?>
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

        <div class="print-area-thermal col-lg-5 print-thermal">

            <?php if (!empty($dadosRelatorio) && ($totalGeral["resgatados"] > 0 || $totalGeral["usados"] > 0
                || $totalGeral["valor_pago_gotas"] > 0 || $totalGeral["valor_pago_reais"] > 0
                || $totalGeral["brindes"] > 0 || $totalGeral["compras"] > 0)) : ?>
                <h1 class="text-center">Relatório <?= $tipoRelatorio ?> de Caixa de Funcionários:</h1>
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

                                <?php if ($turno["soma"]["resgatados"] > 0 || $turno["soma"]["usados"] > 0 || $turno["soma"]["valor_pago_gotas"] > 0 || $turno["soma"]["valor_pago_reais"] > 0 || $turno["soma"]["brindes"] > 0 || $turno["soma"]["compras"] > 0) : ?>
                                    <h4 class="text-center">Turno: <?= sprintf("%s às %s", $turno["horario_inicio"], $turno["horario_fim"]); ?></h4>

                                    <?php if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) : ?>
                                        <!-- ANALÍTICO -->
                                        <div class="form-group">

                                            <!-- Exibe dados descritivo, se algum usuário fez a compra do brinde -->
                                            <?php if (count($turno["brindes"]) > 0) : ?>

                                                <?php foreach ($turno["brindes"] as $brinde) : ?>

                                                    <?php if ($brinde["soma"]["resgatados"] > 0 || $brinde["soma"]["usados"] > 0 || $brinde["soma"]["valor_pago_gotas"] > 0 || $brinde["soma"]["valor_pago_reais"] > 0 || $brinde["soma"]["brindes"] > 0 || $brinde["soma"]["compras"] > 0) : ?>

                                                        <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="5">
                                                                        <h1 class='text-center'>Brinde: <?= $brinde["nome"] ?></h1>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                        <?php foreach ($brinde["usuarios"] as $usuario) : ?>
                                                            <?php if ($usuario["soma"]["resgatados"] > 0 || $usuario["soma"]["usados"] > 0 || $usuario["soma"]["valor_pago_gotas"] > 0 || $usuario["soma"]["valor_pago_reais"] > 0 || $usuario["soma"]["brindes"] > 0 || $usuario["soma"]["compras"] > 0) : ?>

                                                                <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                                    <thead>
                                                                        <!-- Dados de Usuários -->
                                                                        <tr>
                                                                            <th colspan="5">
                                                                                <h2 class="text-center">Cliente: <?= $usuario["nome"] ?></h2>
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <h5>Resgatados / Validados</h5>
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

                                                                    </tbody>
                                                                </table>
                                                            <?php endif; ?>
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
                                                                        <h5>Resgatados / Validados</h5>
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
                                                    <?php endif; ?>


                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <!-- Caso contrário, não há dados -->
                                                <h3>Não há dados para o turno!</h3>
                                            <?php endif; ?>

                                        </div>

                                    <?php else : ?>
                                        <!-- SINTÉTICO -->

                                        <div class="form-group">

                                            <!-- Exibe dados descritivo, se algum usuário fez a compra do brinde -->
                                            <?php if (count($turno["brindes"]) > 0) : ?>

                                                <?php foreach ($turno["brindes"] as $brinde) : ?>

                                                    <?php if ($brinde["soma"]["resgatados"] > 0 || $brinde["soma"]["usados"] > 0 || $brinde["soma"]["valor_pago_gotas"] > 0 || $brinde["soma"]["valor_pago_reais"] > 0 || $brinde["soma"]["brindes"] > 0 || $brinde["soma"]["compras"] > 0) : ?>

                                                        <table class="table table-bordered table-hover table-responsive table-striped table-condensed">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="5">
                                                                        <h1 class='text-center'>Brinde: <?= $brinde["nome_brinde"] ?></h1>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <h5>Resgatados / Validados</h5>
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

                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($turno["soma"]["resgatados"] > 0 ||  $turno["soma"]["usados"] > 0 ||  $turno["soma"]["valor_pago_gotas"]  > 0 ||  $turno["soma"]["valor_pago_reais"] > 0 ||  $turno["soma"]["brindes"]  > 0 ||  $turno["soma"]["compras"]  > 0) : ?>
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

        <?php
        // Adiciona comportamento jquery
        $extensionJs = $debug ? ".js" : ".min.js";
        $extensionCss = $debug ? ".css" : ".min.css";
        echo $this->Html->script('scripts/cupons/relatorio_caixa_funcionarios_gerente' . $extensionJs);
        echo $this->Html->css("styles/cupons/relatorio_caixa_funcionarios_gerente" . $extensionCss);
        echo $this->fetch("script");
        ?>