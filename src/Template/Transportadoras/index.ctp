<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transportadora[]|\Cake\Collection\CollectionInterface $transportadoras
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Transportadoras', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->element('../Transportadoras/left_menu', ['controller' => 'transportadoras', 'action' => 'index', 'mode' => 'view']) ?>
<div class="transportadoras index col-lg-9 col-md-10 columns content">
    <legend><?= __('Transportadoras') ?></legend>

    <?= $this->element('../Transportadoras/filtro_transportadoras', ['controller' => 'transportadoras', 'action' => 'index']) ?>
    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('nome_fantasia') ?></th>
                <th scope="col"><?= $this->Paginator->sort('razao_social') ?></th>
                <th scope="col"><?= $this->Paginator->sort('cnpj') ?></th>
                <th scope="col"><?= $this->Paginator->sort('municipio') ?></th>
                <th scope="col"><?= $this->Paginator->sort('estado') ?></th>
                <th scope="col"><?= $this->Paginator->sort('pais', ['label' => 'País']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('tel_fixo', ['label' => 'Fixo']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('tel_celular', ['label' => 'Celular']) ?></th>

                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transportadoras as $transportadora) : ?>
                <tr>
                    <td><?= h($transportadora->nome_fantasia) ?></td>
                    <td><?= h($transportadora->razao_social) ?></td>
                    <td><?= h($this->NumberFormat->formatNumberToCNPJ($transportadora->cnpj)) ?></td>
                    <td><?= h($transportadora->municipio) ?></td>
                    <td><?= h($this->Address->getStatesBrazil($transportadora->estado)) ?></td>
                    <td><?= h($transportadora->pais) ?></td>
                    <td><?= h($this->Phone->formatPhone($transportadora->tel_fixo)) ?></td>
                    <td><?= h($this->Phone->formatPhone($transportadora->tel_celular)) ?></td>
                    <td class="Ações">
                        <?=
                        $this->Html->link(
                            __(
                                '{0} ',
                                $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                            ),
                            [
                                'action' => 'view', $transportadora->id
                            ],
                            [
                                'title' => 'Ver',
                                'class' => 'btn btn-default btn-xs botao-navegacao-tabela',
                                'escape' => false
                            ]
                        )
                        ?>
                        <?=
                        $this->Html->link(
                            __(
                                '{0}',
                                $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                            ),
                            [
                                'action' => 'edit', $transportadora->id
                            ],
                            [
                                'title' => 'Editar',
                                'class' => 'btn btn-primary btn-xs botao-navegacao-tabela',
                                'escape' => false
                            ]
                        )
                        ?>
                        <?=
                        $this->Html->link(
                            __(
                                '{0} ',
                                $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
                            ),
                            '#',
                            [
                                'title' => 'Deletar',
                                'class' => 'btn btn-xs btn-danger btn-confirm',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-delete-with-message',
                                'data-message' => __(Configure::read('messageDeleteQuestion'), $transportadora->nome_fantasia),
                                'data-action' => Router::url(
                                    [
                                        'action' => 'delete', $transportadora->id,
                                        '?' =>
                                            [
                                            'transportadora_id' => $transportadora->id,
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
