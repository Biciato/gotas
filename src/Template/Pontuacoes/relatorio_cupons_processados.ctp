<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/relatorio_cupons_processados.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$title = "Relatório de Cupons Processados";
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../Pontuacoes/left_menu', ['controller' => 'pages', 'action' => 'display', 'mode' => 'report']) ?>

<div class="col-lg-9 col-md-10 columns">
    <legend>
        <?= $title ?>
    </legend>

    <div class="col-lg-12">
        <?= $this->element('../Pontuacoes/filtro_cupons', ['controller' => 'pontuacoes', 'action' => 'relatorio_cupons_processados', 'unidades_ids' => $unidadesIds, 'start' => $start, 'end' => $end]) ?>
    </div>

    <div class="col-lg-12">

        <table class="table table-striped table-hover table-responsive table-condensed">
            <thead>
                <tr>
                    <th>
                        <?= $this->Paginator->sort('id', ['label' => 'Id']) ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('usuario.nome', ['label' => 'Cliente']) ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('funcionario.nome', ['label' => 'Funcionário']) ?>
                    </th>

                    <th>
                        <?= $this->Paginator->sort('pontuacoes.soma_quantidade', ['label' => 'Total de Gotas']) ?>
                    </th>

                    <th>
                        <?= $this->Paginator->sort('chave_nfe', ['label' => 'Chave da NFE']) ?>
                    </th>

                    <th>
                        <?= $this->Paginator->sort('data', ['label' => 'Data Impressão ']) ?>
                    </th>

                    <th class="actions">
                        <?= __('Ações') ?>
                        <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pontuacoes as $pontuacao) : ?>

                <tr>
                    <td>
                        <?= h($pontuacao->id) ?>
                    </td>
                    <td>
                        <?= h($pontuacao->usuario->nome) ?>
                    </td>
                    <td>
                        <?= h($pontuacao->funcionario->nome) ?>
                    </td>
                    <td>
                        <?= h(floor($pontuacao->soma_pontuacoes)) ?>
                    </td>
                    <td>
                        <?= h($pontuacao->chave_nfe) ?>
                    </td>
                    <td>
                        <?= h($pontuacao->data->format('d/m/Y H:i:s')) ?>
                    </td>

                    <td class="actions" style="white-space:nowrap">
                        <?=
                        $this->Html->link(
                            __(
                                '{0}',
                                $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                            ),
                            [
                                'action' => 'detalhesCupom', $pontuacao->id
                            ],
                            [
                                'title' => 'Ver detalhes',
                                'class' => 'btn btn-default btn-xs',
                                'escape' => false
                            ]
                        )
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="paginator">
        <center>
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('primeiro')) ?>
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape' => false]) ?>
                <?= $this->Paginator->numbers(['escape' => false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape' => false]) ?>
                <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p>
                <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?>
            </p>
        </center>
    </div>
    </div>
</div>
