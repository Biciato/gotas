<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\ClientesHasBrindesHabilitadosPreco
 * @filename rtibrindes/src/Template/ClientesHasBrindesHabilitadosPreco/relatorio_historico_preco_brindes_redes.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     12/03/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$title = "Relatório Histórico Preços de Brindes por Redes";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);
?>

<?= $this->element(
    '../ClientesHasBrindesHabilitados/left_menu',
    [
        'show_reports_admin_rti' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend></legend>

    <?= $this->element('../ClientesHasBrindesHabilitadosPreco/filtro_relatorio_historico_preco_brindes_redes', ['controller' => 'ClientesHasBrindesHabilitadosPreco', 'action' => 'relatorio_historico_preco_brindes_redes']) ?>

    <div class="pull-right">
        <?= $this->Html->tag(
            'button',
            __(
                "{0} Exportar",
                $this->Html->tag('i', '', ['class' => 'fa fa-file-excel-o'])
            ),
            [
                'class' => 'btn btn-primary btn-export-html'
            ]
        ) ?>

        <?= $this->Html->tag(
            'button',
            __(
                "{0} Imprimir",
                $this->Html->tag('i', '', ['class' => 'fa fa-print'])
            ),
            [
                'class' => 'btn btn-primary btn-print-html'
            ]
        ) ?>
    </div>
    <!-- <h4>Lista de Redes</h4> -->
        <!-- <table> -->
    <div class='table-export'>

    <?php foreach ($redes as $key => $rede) : ?>


        <?php if (sizeof($rede['clientesBrindes']) > 0) : ?>
            <h4><?= __("Preços de Brindes da Rede: {0} ", $rede['nome_rede']) ?> </h4>

            <table class="table table-hover table-striped table-condensed table-responsive">
                <thead>
                    <tr>
                        <th><?= h(__("Unidade")) ?> </th>
                        <th><?= h(__("Brinde")) ?> </th>
                        <th><?= h(__("Preço Atual")) ?> </th>
                        <th><?= h(__("Data Alteração")) ?> </th>
                        <th><?= h(__("Detalhes")) ?> </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rede['clientesBrindes'] as $key => $brindesHabilitados) : ?>
                        <?php foreach ($brindesHabilitados as $key => $brindeHabilitado) : ?>

                            <tr>
                                <td><?= $brindeHabilitado->cliente->nome_fantasia ?></td>
                                <td><?= $brindeHabilitado->brinde->nome ?></td>
                                <td><?= h($this->Number->precision($brindeHabilitado->brinde_habilitado_preco_atual->preco, 2)) ?></td>
                                <td><?= $brindeHabilitado->brinde_habilitado_preco_atual->audit_insert->format('d/m/Y') ?> </td>
                                <td>
                                
                                   <?= $this->Html->link(
                                        __(
                                            '{0}',
                                            $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                                        ),
                                        [
                                            'controller' => 'ClientesHasBrindesHabilitadosPreco',
                                            'action' => 'RelatorioHistoricoPrecoBrindesDetalhado', $brindeHabilitado->id
                                        ],
                                        [
                                            'title' => 'Detalhes',
                                            'class' => 'btn btn-primary btn-xs',
                                            'escape' => false
                                        ]
                                    ) ?>
                                    </td>
                            </tr>

                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
    <?php endforeach; ?>

    



</div>

