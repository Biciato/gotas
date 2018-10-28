<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\Brindes
 * @filename rtibrindes/src/Template/Brindes/relatorio_brindes_redes.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     06/03/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$title = __("Relatório Brindes por Redes");

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);
?>

<?= $this->element(
    '../Brindes/left_menu',
    [
        'show_reports_admin_rti' => $usuarioLogado['tipo_perfil'] == (int)Configure::read('profileTypes')['AdminDeveloperProfileType'],
        'show_reports_admin' => $usuarioLogado['tipo_perfil'] >= (int)Configure::read('profileTypes')['AdminNetworkProfileType'] && $usuarioLogado['tipo_perfil'] <= (int)Configure::read('profileTypes')['AdminLocalProfileType']

    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend></legend>

    <?= $this->element('../Brindes/filtro_brindes_relatorio', ['controller' => 'Brindes', 'action' => 'relatorio_brindes_redes']) ?>

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


        <?php if (sizeof($rede['brindes']) > 0) : ?>
            <h4><?= __("Brindes da Rede: {0} ", $rede['nome_rede']) ?> </h4>

            <table class="table table-hover table-striped table-condensed table-responsive">
                <thead>
                    <tr>
                        <th><?= h(__("Nome")) ?> </th>
                        <th><?= h(__("Equip. RTI Shower")) ?> </th>
                        <th><?= h(__("Tempo RTI Shower (se Equip. RTI Shower)")) ?> </th>
                        <th><?= h(__("Estoque Ilimitado?")) ?> </th>
                        <th><?= h(__("Habilitado para Uso")) ?> </th>
                        <th><?= h(__("Preco Padrão")) ?> </th>
                        <th><?= h(__("Data Criação")) ?> </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rede['brindes'] as $key => $brinde) : ?>

                        <tr>
                            <td><?= $brinde->nome ?> </td>
                            <td><?= h($this->Boolean->convertBooleanToString($brinde->equipamento_rti_shower)) ?> </td>
                            <td><?= $brinde->tempo_rti_shower ?> </td>
                            <td><?= h($this->Boolean->convertBooleanToString($brinde->ilimitado)) ?> </td>
                            <td><?= h($this->Boolean->convertEnabledToString($brinde->habilitado)) ?> </td>
                            <td><?= $this->Number->precision($brinde->preco_padrao, 2) ?> </td>
                            <td><?= $brinde->audit_insert->format('d/m/Y') ?> </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
    <?php endforeach; ?>



</div>

