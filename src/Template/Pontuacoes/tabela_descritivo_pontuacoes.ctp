<?php

use Cake\Core\Configure;
use Cake\Routing\Router;


/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/tabela_descritivo_pontuacoes.ctp
 * @date     28/08/2017
 */

$show_actions = isset($show_actions) ? $show_actions : true;
?>

<table class="table table-striped table-hover">

<thead>
    <tr>
        <th>
            <?= __('Nome') ?>
        </th>
        <th>
            <?= __('Qte. de Valor Adquirido') ?>
        </th>
        <th>
            <?= __('Qte. de Gotas') ?>
        </th>
        <?php if ($show_actions) : ?>
        <th>
            <?= __('Ações') ?>
            <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
        </th>
        <?php endif; ?>
    </tr>
</thead>
<tbody>
    <?php foreach ($pontos as $key => $value) : ?>
    <tr>
        <td>
            <?= h($value->gota->nome_parametro) ?>
        </td>
        <td>
            <?= h(floor($value->quantidade_multiplicador)) ?>
        </td>
        <td>
            <?= h(floor($value->quantidade_gotas)) ?>
        </td>
        <?php if ($show_actions) : ?>

        <td>

            <?php if ($usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']) : ?>
            <?= $this->Html->link(
                __(
                    "{0}",
                    $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                ),
                [
                    'controller' => 'pontuacoes',
                    'action' => 'editar_pontuacao', $value->id
                ],
                [
                    'title' => "Editar",
                    'class' => 'btn btn-primary btn-xs',
                    'escape' => false,
                ]
            ) ?>

            <?= $this->Html->link(
                __(
                    "{0}",
                    $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
                ),
                '#',
                [
                    "title" => "Remover",
                    'class' => 'btn btn-primary btn-danger  btn-xs',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-delete',
                    'data-action' => Router::url(
                        [
                            'controller' => 'pontuacoes',
                            'action' => 'remover_pontuacao', $value->id
                        ]
                    ), 'escape' => false
                ],
                false
            ) ?>
            <?php endif; ?>
        </td>
        <?php endif; ?>

    </tr>

    <?php endforeach; ?>

</tbody>
</table>
