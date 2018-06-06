<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Veiculo[]|\Cake\Collection\CollectionInterface $veiculos
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Veículos', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->element(
    '../Veiculos/left_menu',
    [

    ]
) ?>

<div class="veiculos index col-lg-9 col-md-8 columns content">
    <legend><?= __('Veiculos') ?></legend>

    <?= $this->element('../Veiculos/filtro_veiculos', ['controller' => 'veiculos', 'action' => 'index']) ?>
    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('placa') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modelo') ?></th>
                <th scope="col"><?= $this->Paginator->sort('fabricante') ?></th>
                <th scope="col"><?= $this->Paginator->sort('ano') ?></th>
                <th scope="col" class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($veiculos as $veiculo) : ?>
            <tr>
                <td><?= h($veiculo->placa) ?></td>
                <td><?= h($veiculo->modelo) ?></td>
                <td><?= h($veiculo->fabricante) ?></td>
                <td><?= $this->Number->format($veiculo->ano) ?></td>
                <td class="actions">
                    <?= $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                        ),
                        [
                            'action' => 'view', $veiculo->id
                        ],
                        [
                            'class' => 'btn btn-primary btn-xs',
                            'escape' => false,
                            'title' => 'Ver'
                        ]
                    ) ?>
                    <?=
                    $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                        ),
                        [
                            'action' => 'edit', $veiculo->id
                        ],
                        [
                            'class' => 'btn btn-primary btn-xs',
                            'escape' => false,
                            'title' => 'Editar'
                        ]
                    )
                    ?>
                    <?=
                    $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
                        ),
                        '#',
                        [
                            'title' => 'Deletar',
                            'class' => 'btn btn-xs btn-danger btn-confirm',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-delete-with-message',
                            'data-message' => __(Configure::read('messageDeleteQuestion'), $veiculo->placa),
                            'data-action' => Router::url(
                                [
                                    'action' => 'delete', $veiculo->id,
                                    '?' =>
                                        [
                                        'veiculo_id' => $veiculo->id,
                                        'return_url' => 'index'
                                    ]
                                ]
                            ),
                            'escape' => false
                        ],
                        false
                    );
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
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
</div>
