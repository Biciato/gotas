<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\ClientesHasBrindesHabilitados
 * @filename rtibrindes/src/Template/ClientesHasBrindesHabilitados/relatorio_estoque_brindes_detalhado.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     09/03/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$title = "Relatório Detalhado de Estoque de Brindes por Redes";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add("Relatório Estoque de Brindes por Redes", [
    'controller' => 'ClientesHasBrindesEstoque',
    'action' => 'relatorio_estoque_brindes_redes'
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

    <?= $this->element('../ClientesHasBrindesEstoque/filtro_relatorio_estoque_brindes_detalhado', [
        'controller' => 'ClientesHasBrindesEstoque', 'action' => 'relatorio-estoque-brindes-detalhado',
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
    <?php if (isset($historicoEstoqueBrinde) && sizeof($historicoEstoqueBrinde->toArray()) > 0) : ?>

        <h4><?= __("Histórico de Estoque do Brinde {0} da Rede: {1}, Unidade: {2}", $brinde->nome, $rede->nome_rede, $cliente->nome_fantasia) ?> </h4>

        <h5 class='form-control disabled'><?= __("Saldo atual: {0}", $brinde->estoque[0]) ?></h5>

        <table class="table table-hover table-striped table-condensed table-responsive">
            <thead>
                <tr>
                    <th><?= h(__("Usuário")) ?> </th>
                    <th><?= h(__("Quantidade")) ?> </th>
                    <th><?= h(__("Tipo Operacao")) ?> </th>
                    <th><?= h(__("Data")) ?> </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historicoEstoqueBrinde as $key => $historico) : ?>
                
                    <tr>
                        <td><?= $historico->usuario->nome ?></td>
                        <td><?= $historico->quantidade ?></td>
                        <td><?= Configure::read('stockOperationTypesTranslated')[$historico->tipo_operacao] ?></td>
                        <td><?= $historico->audit_insert->format('d/m/Y H:i:s') ?> </td>
                      
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>

    <?php else : ?> 

        <h4>Não há registros à serem exibidos!</h4>
    
    <?php endif; ?>

</div>

