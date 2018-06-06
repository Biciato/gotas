
<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\Veiculos
 * @filename rtibrindes/src/Template/Veiculos/relatorio_Veiculos_usuarios_redes.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     20/03/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$title = __("Relatório Veiculos Cadastrados de Clientes das Redes");

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);
?>

<?= $this->element(
    '../Veiculos/left_menu',
    [
        'show_reports' => true,
        'show_reports_admin_rti' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend><?= $title ?></legend>

    <?= $this->element(
        '../Veiculos/filtro_relatorio_veiculos_usuarios_redes',
        [
            'controller' => 'Veiculos',
            'action' => 'relatorio_veiculos_usuarios_redes'
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

    <?php if (sizeof($redes == 0)): ?> 
        <h4>A pesquisa não retornou dados!</h4>
    <?php endif; ?>
    
    <?php foreach ($redes as $key => $rede) : ?>

        <h4><?= __("Veiculos da Rede: {0} ", $rede['nome_rede']) ?> </h4>

        <table class="table table-hover table-striped table-condensed table-responsive">
            <thead>
                <tr>
                    <th><?= __("Placa") ?> </th>
                    <th><?= __("Modelo") ?> </th>
                    <th><?= __("Fabricante") ?> </th>
                    <th><?= __("Ano") ?> </th>
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
                <?php foreach ($rede['veiculos'] as $key => $veiculos) : ?>

                    <tr>
                        
                        <td><?= $veiculos->placa ?></td>
                        <td><?= $veiculos->modelo ?></td>
                        <td><?= $veiculos->fabricante ?></td>
                        <td><?= $veiculos->ano ?></td>
                        <td><?= $veiculos->audit_insert->format('d/m/Y') ?> </td>
                        <td> <?= $this->Html->link(
                                __(
                                    '{0} ',
                                    $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                ),
                                [
                                    'controller' => 'UsuariosHasVeiculos',
                                    'action' => 'relatorio_veiculos_usuarios_detalhado', $veiculos->id
                                ],
                                [
                                    'title' => 'Usuários que tem o veículo',
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

