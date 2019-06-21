
<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\Transportadoras
 * @filename rtibrindes/src/Template/Transportadoras/relatorio_transportadoras_usuarios_redes.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     19/03/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$title = __("Relatório Transportadoras Cadastradas de Clientes das Redes");

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);
?>

<?= $this->element(
    '../Transportadoras/left_menu',
    [
        'show_reports' => true,
        'show_reports_admin_rti' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend><?= $title ?></legend>

    <?= $this->element(
        '../Transportadoras/filtro_relatorio_transportadoras_usuarios_redes',
        [
            'controller' => 'Transportadoras',
            'action' => 'relatorio_transportadoras_usuarios_redes'
        ]
    ) ?>

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


        <h4><?= __("Transportadoras da Rede: {0} ", $rede['nome_rede']) ?> </h4>

        <table class="table table-hover table-striped table-condensed table-responsive">
            <thead>
                <tr>
                    <th><?= __("Nome Fantasia") ?> </th>
                    <th><?= __("Razão Social") ?> </th>
                    <th><?= __("CNPJ") ?> </th>
                    <th><?= __("CEP") ?> </th>
                    <th><?= __("Endereco") ?> </th>
                    <th><?= __("Num.") ?> </th>
                    <th><?= __("Compl.") ?> </th>
                    <th><?= __("Bairro") ?> </th>
                    <th><?= __("Municipio") ?> </th>
                    <th><?= __("Estado") ?> </th>
                    <th><?= __("Tel Fixo") ?> </th>
                    <th><?= __("Tel Celular") ?> </th>
                    <th><?= __("Data Criação") ?> </th>
                    <th><?= __("Detalhes") ?>

                    <?= $this->Html->tag(
                        'button',
                        __(
                            "{0} Legendas",
                            $this->Html->tag('i', '', ['class' => 'fa fa-book'])
                        ),
                        [
                            'class' => 'btn btn-xs btn-default right-align modal-legend-icons-save',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalLegendIconsSave'
                        ]
                    ) ?>
                     </th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($rede['transportadoras'] as $key => $transportadora) : ?>

                    <tr>
                        <td><?= $transportadora->nome_fantasia ?> </td>
                        <td><?= $transportadora->razao_social ?> </td>
                        <td><?= $this->NumberFormat->formatNumberToCNPJ($transportadora->cnpj) ?> </td>
                        <td><?= $this->Address->formatCEP($transportadora->cep) ?> </td>
                        <td><?= $transportadora->endereco ?> </td>
                        <td><?= $transportadora->endereco_numero ?> </td>
                        <td><?= $transportadora->endereco_complemento ?> </td>
                        <td><?= $transportadora->bairro ?> </td>
                        <td><?= $transportadora->municipio ?> </td>
                        <td><?= $transportadora->estado ?> </td>
                        <td><?= $transportadora->tel_fixo ?> </td>
                        <td><?= $transportadora->tel_celular ?> </td>
                        <td><?= $transportadora->audit_insert->format('d/m/Y') ?> </td>
                        <td> <?= $this->Html->link(
                                __(
                                    '{0} ',
                                    $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                                ),
                                [
                                    'controller' => 'TransportadorasHasUsuarios',
                                    'action' => 'relatorio_transportadoras_usuarios_detalhado', $transportadora->id
                                ],
                                [
                                    'title' => 'Usuários que tem a transportadora',
                                    'class' => 'btn btn-xs btn-primary btn-confirm',
                                    'escape' => false
                                ]
                            );
                            ?>
                    </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endforeach; ?>



</div>

