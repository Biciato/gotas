<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/brindes_minha_rede.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
use App\Custom\RTI\DebugUtil;

// DebugUtil::printArray($unidadesIds->toArray());

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Brindes da Minha Rede', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../Brindes/left_menu', ['mode' => 'add', "clientesId" => $cliente["id"]]) ?>
<div class="brindes index col-lg-9 col-md-10 columns content">
    <legend><?= __("Cadastro de Brindes da Rede") ?></legend>

    <?= $this->element('../Brindes/brindes_filtro', ['controller' => 'brindes', 'action' => 'brindes_minha_rede', 'unidadesIds' => $unidadesIds]) ?>

    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('nome') ?></th>
                <th><?= $this->Paginator->sort('tempo_uso_brinde', array("label" => "Tempo de Uso")) ?></th>
                <th><?= $this->Paginator->sort('ilimitado') ?></th>
                <th><?= $this->Paginator->sort('tipo_venda') ?></th>
                <th><?= $this->Paginator->sort('preco_padrao') ?></th>
                <th><?= $this->Paginator->sort('valor_moeda_venda_padrao') ?></th>
                <th><?= __("Status") ?></th>
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($brindes as $brinde) : ?>
                <tr>

                    <td><?= h($brinde->nome) ?></td>
                    <td><?= h($brinde->tempo_uso_brinde) ?></td>
                    <td><?= $this->Boolean->convertBooleanToString($brinde->ilimitado) ?></td>
                    <td><?= $brinde->tipo_venda ?></td>
                    <td><?= $this->Number->precision($brinde->preco_padrao, 2) ?></td>
                    <td><?= $this->Number->precision($brinde->valor_moeda_venda_padrao, 2) ?></td>
                    <td><?= $this->Boolean->convertEnabledToString($brinde->habilitado) ?></td>

                    <td class="actions" style="white-space:nowrap">
                        <?= $this->Html->link(
                            __(
                                '{0}',
                                $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                            ),
                            [
                                'action' => 'ver_brinde_rede', $brinde->id
                            ],
                            [
                                'title' => 'Ver detalhes',
                                'class' => 'btn btn-default btn-xs botao-navegacao-tabela',
                                'escape' => false
                            ]
                        ) ?>
                        <?= $this->Html->link(
                            __(
                                '{0}',
                                $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                            ),
                            [
                                'action' => 'editar_brinde_rede', $brinde->id
                            ],
                            [
                                'title' => 'Editar',
                                'class' => 'btn btn-primary btn-xs botao-navegacao-tabela',
                                'escape' => false
                            ]
                        )
                        ?>
                        <?php if (!$brinde->habilitado) : ?>

                              <?= $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                    ),
                                    '#',
                                    [
                                        'title' => 'Habilitar',
                                        'class' => 'btn btn-xs btn-primary btn-confirm',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-confirm-with-message',
                                        'data-message' => __(Configure::read('messageEnableQuestion'), $brinde->nome),
                                        'data-action' => Router::url(
                                            [
                                                'action' => 'ativar_brinde', $brinde->id
                                            ]
                                        ),
                                        'escape' => false
                                    ],
                                    false
                                ); ?>


                    <?php else : ?>

                              <?= $this->Html->link(
                                    __(
                                        '{0}',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                    ),
                                    '#',
                                    [
                                        'title' => 'Desabilitar',
                                        'class' => 'btn btn-xs btn-danger btn-confirm',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-confirm-with-message',
                                        'data-message' => __(Configure::read('messageDisableQuestion'), $brinde->nome),
                                        'data-action' => Router::url(
                                            [
                                                'action' => 'desativar_brinde', $brinde->id
                                            ]
                                        ),
                                        'escape' => false
                                    ],
                                    false
                                ); ?>

                    <?php endif; ?>
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
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
</div>
