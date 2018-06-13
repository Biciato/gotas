<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/clientes_has_brindes_habilitados/meus_brindes_ativados.ctp
 * @date     09/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Brindes Habilitados de Unidades', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<div class="row">
</div>
<?= $this->element(
    '../ClientesHasBrindesHabilitados/left_menu',
    [
        'mode' => 'activatedGifts',
        'go_back_url' =>
            [
            'controller' => 'pages',
            'action' => 'display'
        ]
    ]
)
?>
<div class="clientes_has_brindes_habilitados index col-lg-9 col-md-10 columns content">

<legend>
    <?php if ($user_logged['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) : ?>
        <?= __('Brindes Habilitados de Unidades') ?>
    <?php else : ?>
        <?= __('Brindes Habilitados de Unidade') ?>
    <?php endif; ?>
</legend>

<?php if ($user_logged['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) : ?>
    <?= $this->element('../Brindes/brindes_filtro_unidades', ['controller' => 'clientes_has_brindes_habilitados', 'action' => 'meus_brindes_ativados']) ?>
<?php else : ?>
    <?= $this->element('../Brindes/brindes_filtro_pesquisa_comum', ['controller' => 'clientes_has_brindes_habilitados', 'action' => 'meus_brindes_ativados']) ?>
<?php endif; ?>
    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('clientes_has_brindes_habilitado->brinde->nome', ['label' => 'Nome']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('clientes_has_brindes_habilitado->BrindeHabilitadoPrecoAtual', ['label' => 'Preço Atual (em gotas)']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('clientes_has_brindes_habilitado->BrindeHabilitadoPrecoAtual->preco', ['label' => 'Última Alteração de Preço']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('clientes_has_brindes_habilitado->cliente->razao_social', ['label' => 'Unidade Vinculada']) ?></th>
                <th scope="col"><?= __("Status") ?></th>
                <th scope="col" class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes_has_brindes_habilitados as $clientes_has_brindes_habilitado) : ?>


                <tr>
                    <td>
                        <?= $clientes_has_brindes_habilitado->brinde->nome ?>
                    </td>
                    <?php if (isset($clientes_has_brindes_habilitado->BrindeHabilitadoPrecoAtual)) : ?>
                        <td>
                            <?= $this->Number->precision(isset($clientes_has_brindes_habilitado->BrindeHabilitadoPrecoAtual) ? $clientes_has_brindes_habilitado->BrindeHabilitadoPrecoAtual->preco : null, 2); ?>
                        </td>
                        <td>
                            <?= $this->DateUtil->dateToFormat($clientes_has_brindes_habilitado->BrindeHabilitadoPrecoAtual->data_preco, 'd/m/Y H:i:s') ?>
                        </td>
                    <?php else : ?>
                        <td colspan="2"></td>
                    <?php endif; ?>

                    <td>
                        <?= h($clientes_has_brindes_habilitado->cliente->razao_social) ?>
                    </td>
                    <td>
                        <?= __($this->Boolean->convertEnabledToString($clientes_has_brindes_habilitado->habilitado)) ?>
                    </td>
                    <td class="actions">
                        <?=
                        $this->Html->link(
                            __(
                                '{0} ',
                                $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                            ),
                            [
                                'action' => 'detalhes_brinde', $clientes_has_brindes_habilitado->id
                            ],
                            [
                                'class' => 'btn btn-default btn-xs',
                                'title' => 'Ver detalhes',
                                'escape' => false
                            ]
                        )
                        ?>

                    <?php


                    if (!$clientes_has_brindes_habilitado->habilitado) : ?>

                    <?= $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                        ),
                        '#',
                        [
                            'class' => 'btn btn-xs btn-primary btn-confirm',
                            'title' => 'Habilitar',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-confirm-with-message',
                            'data-message' => __(Configure::read('messageEnableQuestion'), $clientes_has_brindes_habilitado->brinde->nome),
                            'data-action' => Router::url(
                                [
                                    'action' => 'habilitar_brinde', $clientes_has_brindes_habilitado->id,
                                    "?" => [
                                        'brindes_id' => $clientes_has_brindes_habilitado->brindes_id,
                                        'clientes_id' => $clientes_has_brindes_habilitado->clientes_id,
                                    ]
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
                            'class' => 'btn btn-xs btn-danger btn-confirm',
                            'title' => 'Desabilitar',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-confirm-with-message',
                            'data-message' => __(Configure::read('messageDisableQuestion'), $clientes_has_brindes_habilitado->brinde->nome),
                            'data-action' => Router::url(
                                [
                                    'action' => 'desabilitar_brinde',
                                    "?" => [
                                        'brindes_id' => $clientes_has_brindes_habilitado->brindes_id,
                                        'clientes_id' => $clientes_has_brindes_habilitado->clientes_id,
                                    ]
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
