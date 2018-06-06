<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\ClientesHasBrindesHabilitadosPreco
 * @filename rtibrindes/src/Template/ClientesHasBrindesHabilitadosPreco/relatorio_historico_preco_brindes_detalhado.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     12/03/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$title = "Relatório Histórico Detalhado de Preços de Brindes por Redes";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Relatório Histórico Detalhado de Preços de Brindes por Redes', [
    'controller' => 'ClientesHasBrindesHabilitadosPreco',
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
    '../ClientesHasBrindesHabilitados/left_menu',
    [
        'show_reports_admin_rti' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend></legend>

    <?= $this->element('../ClientesHasBrindesHabilitadosPreco/filtro_relatorio_historico_preco_brindes_redes_detalhado', [
        'controller' => 'ClientesHasBrindesHabilitadosPreco', 'action' => 'relatorio_historico_preco_brindes_detalhado',
        'id' => $brinde->id
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

    <?php if (isset($historicoPrecoBrinde) && sizeof($historicoPrecoBrinde->toArray()) > 0) : ?>

        <h4><?= __("Histórico de Preço do Brinde {0} da Rede: {1}, Unidade: {2}", $brinde->nome, $rede->nome_rede, $cliente->nome_fantasia) ?> </h4>

        <h5 class='form-control disabled'><?= __(
                                                "Preço atual: {0}",
                                                $this->Number->precision($brinde->brinde_habilitado_preco_atual->preco, 2)
                                            ) ?></h5>

        <table class="table table-hover table-striped table-condensed table-responsive">
            <thead>
                <tr>
                    <th><?= h(__("Preço")) ?> </th>
                    <th><?= h(__("Status")) ?> </th>
                    <th><?= h(__("Data")) ?> </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historicoPrecoBrinde as $key => $historico) : ?>
                
                    <tr>
                        <td><?= h($this->Number->Precision($historico->preco, 2)) ?></td>
                        <td><?= h(Configure::read('giftApprovalStatusTranslated')[$historico->status_autorizacao]) ?></td>
                        <td><?= h($historico->audit_insert->format('d/m/Y H:i:s')) ?> </td>
                      
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>

    <?php else : ?> 

        <h4>Não há registros à serem exibidos!</h4>
    
    <?php endif; ?>

</div>

