<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\Gotas
 * @filename rtibrindes/src/Template/Gotas/relatorio_historico_preco_brindes_detalhado.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     12/03/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$title = "Relatório Gotas por Redes";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [
    'controller' => 'Gotas',
    'action' => 'relatorio_historico_preco_brindes'
]);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);


?>

<?= $this->element(
    '../Gotas/left_menu',
    [
        'show_reports_admin_rti' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend><?= $title ?> </legend>

    <?= $this->element('../Gotas/filtro_relatorio_gotas_redes', [
        'controller' => 'Gotas', 'action' => 'relatorio_gotas_redes'
    ]) ?>

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
            <?php if (sizeof($rede['gotas']) > 0) : ?>

                <h4><?= __("Gotas da Rede: {0}", $rede['nome_rede']) ?> </h4>


                <table class="table table-hover table-striped table-condensed table-responsive">
                    <thead>
                        <tr>
                            <th><?= h(__("Unidade")) ?> </th>
                            <th><?= h(__("Nome Parâmetro")) ?> </th>
                            <th><?= h(__("Multiplicador")) ?> </th>
                            <th><?= h(__("Habilitado")) ?> </th>
                            <th><?= h(__("Data")) ?> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rede['gotas'] as $key => $gota) : ?>


                            <tr>
                                <td><?= $gota->cliente->nome_fantasia ?></td>
                                <td><?= $gota->nome_parametro ?></td>
                                <td><?= $this->Number->precision($gota->multiplicador_gota, 2) ?></td>
                                <td><?= $this->Boolean->convertEnabledToString($gota->habilitado) ?></td>
                                <td><?= h($gota->audit_insert->format('d/m/Y')) ?></td>


                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else : ?>

                <h4>Não há registros à serem exibidos!</h4>

            <?php endif; ?>

        <?php endforeach; ?>
    </div>
