<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\PontuacoesComprovantes
 * @filename rtibrindes/src/Template/PontuacoesComprovantes/relatorio_pontuacoes_comprovantes_redes.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     15/03/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$title = "Relatório de Comprovantes das Pontuações por Redes";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);

?>

<?= $this->element(
    '../PontuacoesComprovantes/left_menu',
    [
        'show_reports_admin_rti' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend><?= $title ?> </legend>

    <?= $this->element('../PontuacoesComprovantes/filtro_relatorio_pontuacoes_comprovantes_redes', [
        'controller' => 'PontuacoesComprovantes', 'action' => 'relatorio_pontuacoes_comprovantes_redes'
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

    <?php foreach ($pontuacoesComprovantes as $key => $comprovante) : ?> 

        <table class="table table-striped table-hover table-condensed table-responsive">
            <tr>
                <th>Unidade:</th>
                <td><?= $comprovante->cliente->nome_fantasia ?> </td>
            </tr>
            <tr>
                <th>Cliente:</th>
                <td><?= $comprovante->usuario->nome ?></td>
            </tr>
            <tr>
                <th>Funcionário:</th>
                <td><?= $comprovante->funcionario->nome ?></td>
            </tr>
            <tr>
                <th>Chave da NFE: </th>
                <td><?= $comprovante->chave_nfe ?></td>
            </tr>
            <tr>
                <th>Estado</th>
                <td><?= $comprovante->estado_nfe ?></td>
            </tr>
            <tr>
                <th>Soma de Pontuação (Gotas)</th>
                <td><?= $comprovante->soma_pontuacoes[0]->quantidade_multiplicador ?></td>
            </tr>
            <tr>
                <th>Data de Processamento</th>
                <td><?= $comprovante->data->format('d/m/Y') ?></td>
            </tr>
        </table>
        
        <legend><?= __("Detalhamento do comprovante {0}", $comprovante->chave_nfe) ?> </legend>


                <table class="table table-hover table-striped table-condensed table-responsive">
                    <thead>
                        <tr>
                            <th><?= h(__("Gota")) ?> </th>
                            <th><?= h(__("Qte. Gotas Acumuladas")) ?> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comprovante->pontuacoes as $key => $pontuacao) : ?> 
                        
                            <tr>
                                <td><?= $pontuacao->gota->nome_parametro ?></td>
                                <td><?= $pontuacao->quantidade_multiplicador ?></td>
                                
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>


    <?php endforeach; ?>
</div>

