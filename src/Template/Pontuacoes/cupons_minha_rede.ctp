<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/cupons_minha_rede.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Cupons Emitidos', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../Pontuacoes/left_menu', ['controller' => 'pages', 'action' => 'display', 'mode' => 'report']) ?>

<div class="col-lg-9 col-md-10 columns">
    <legend>
        <?= __("Cupons Emitidos") ?>
    </legend>

    <div class="col-lg-12">
        <?= $this->element('../Pontuacoes/filtro_cupons', ['controller' => 'pontuacoes', 'action' => 'cupons_minha_rede', 'unidades_ids' => $unidadesIds]) ?>
    </div>

    <div class="col-lg-12">

        <table class="table table-striped table-hover table-responsive table-condensed">
            <thead>
                <tr>
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
                    <th >
                        <?= $this->Paginator->sort('requer_auditoria', ['label' => 'Nec. Auditoria?']) ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('auditado', ['label' => 'Auditado?']) ?>
                    </th>
                        <th>
                        <?= $this->Paginator->sort('registro_invalido', ['label' => 'Inválido?']) ?>
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
                <?php foreach ($pontuacoes_cliente as $pontuacao) : ?>

                <tr>
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
                        <?= h($this->Boolean->convertBooleanToString($pontuacao->requer_auditoria)) ?>
                    </td>
                    <td>
                        <?= h($this->Boolean->convertBooleanToString($pontuacao->auditado)); ?>
                    </td>

                    <td>
                        <?= h($this->Boolean->convertBooleanToString($pontuacao->registro_invalido)); ?>
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
    </div>
</div>
