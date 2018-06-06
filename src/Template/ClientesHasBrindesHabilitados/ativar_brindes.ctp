<?php

/**
 * @var \App\View\AppView $this
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesHabilitados/ativar_brindes.ctp
 * @date     11/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Brindes Habilitados de Unidades', ['controller' => 'clientes_has_brindes_habilitados', 'action' => 'meus_brindes_ativados']);

$this->Breadcrumbs->add('Ativar Brinde em Unidade', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>
<?= $this->element(
    '../ClientesHasBrindesHabilitados/left_menu',
    [
        'mode' => 'backOnly',
        'go_back_url' =>
            [
            'controller' => 'clientes_has_brindes_habilitados',
            'action' => 'meus_brindes_ativados'
        ]
    ]
) ?>

<div class="clientesHasBrindeHabilitados view col-lg-9 col-md-8 columns content">
  <legend><?= __('Ativar Brinde em Unidade') ?></legend>
<?= $this->element('../Brindes/brindes_filtro_unidades', ['controller' => 'clientes_has_brindes_habilitados', 'action' => 'ativar_brindes', 'unidades_ids' => $unidades_ids, 'todas_unidades' => false]) ?>
    <table class="table table-striped table-hover table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('Nome') ?></th>
                <th scope="col" class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>

            <?php if (sizeof($brindes_desabilitados) == 0): ?> 

            <tr>
                <td colspan="2">
                    Não há brindes desabilitados para esta unidade!
                </td>
            </tr>
            <?php else: ?> 

                <?php foreach ($brindes_desabilitados as $brindes_desabilitado) : ?>
                    <tr>
                        <td><?= $brindes_desabilitado->nome ?></td>
                        <td>
                            <?= $this->Html->link(
                                __('{0}',$this->Html->tag('i', '', ['class' => 'fa fa-power-off'])),
                                '#',
                                [
                                    'class' => 'btn btn-xs btn-primary btn-confirm',
                                    'title' => 'Habilitar',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-confirm-with-message',
                                    'data-message' => __(Configure::read('messageEnableQuestion'), $brindes_desabilitado->nome),
                                    'data-action' => Router::url(
                                        [
                                            'action' => 'habilitar_brinde',
                                            '?' =>
                                            [
                                                'clientes_id' => $clientes_id,
                                                'brindes_id' => $brindes_desabilitado->id,
                                            ]
                                        ]
                                    ),
                                    'escape' => false
                                ],
                                false
                            ); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif;?>
        </tbody>
    </table>
</div>