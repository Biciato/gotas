<?php

/**
 * configurarTiposBrindesRedes.ctp
 *
 * View para genero_brindes/index
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TiposBrindesRede[]|\Cake\Collection\CollectionInterface $genero_brindes
 *
 * @category View
 * @package App\Template\TiposBrindesRedes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      2018-05-30
 *
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Escolher Rede para Configurar Tipos de Brindes', array("controller" => "tiposBrindesRedes", "action" => "index"));
$this->Breadcrumbs->add('Tipos de Brindes da Rede', array(), array('class' => 'active'));
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element(
    '../TiposBrindesRedes/left_menu',
    [
        'mode' => 'add',
        'show_reports' => false,
        "redesId" => $rede["id"]
    ]
) ?>
<div class="redes index col-lg-9 col-md-10 columns content">
    <legend><?= __('Tipos de Brindes para Rede: {0}', $rede["nome_rede"]) ?></legend>

    <?= $this->element(
        '../TiposBrindesRedes/filtro_tipos_brindes_redes',
        [
            'controller' => 'tiposBrindesRedes',
            'action' => 'configurar_tipos_brindes_rede',
            "id" => $rede["id"]
        ]
    ) ?>
    <table class="table table-striped table-hover table-responsive">
        <thead>
            <tr>
                <th scope="col" style="min-width: 200px";><?= $this->Paginator->sort('nome', ["label" => "Nome"]) ?></th>
                <th scope="col"><?= $this->Paginator->sort('equipamento_rti', ["label" => "Equip. RTI?"]) ?></th>
                <th scope="col"><?= $this->Paginator->sort('brinde_necessidades_especiais', ["label" => "Brinde Nec. Especiais?"]) ?></th>
                <th scope="col"><?= $this->Paginator->sort('habilitado', ["label" => "Habilitado?"]) ?></th>
                <th scope="col"><?= $this->Paginator->sort('atribuir_automatico', ["label" => "Atribuir Auto.?"]) ?></th>
                <th scope="col" class="actions">
                    <?= __('Ações') ?>
                    <?= $this->Html->tag(
                        'button',
                        __(
                            "{0} Legendas",
                            $this->Html->tag('i', '', ['class' => 'fa fa-book'])
                        ),
                        [
                            'class' => 'btn btn-xs btn-default right-align modal-legend-icons-save',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalLegendIconsSave'
                        ]
                    ) ?>

                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tiposBrindes as $tipo) : ?>
            <tr>
                <td><?= h($tipo->nome . ($tipo->brinde_necessidades_especiais == 1 ? " (PNE)" : null)) ?> </td>
                <td><?= h($this->Boolean->convertBooleanToString($tipo->equipamento_rti)) ?> </td>
                <td><?= h($this->Boolean->convertBooleanToString($tipo->brinde_necessidades_especiais)) ?> </td>
                <td><?= h($this->Boolean->convertEnabledToString($tipo->habilitado)) ?> </td>
                <td><?= h($this->Boolean->convertBooleanToString($tipo->atribuir_automatico)) ?> </td>
                <td class="actions" style="white-space:nowrap">
                    <!-- Info -->

                    <?= $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                        ),
                        [
                            'action' => 'ver_detalhes',
                            $tipo->id
                        ],
                        [
                            'class' => 'btn btn-default btn-xs',
                            'escape' => false,
                            "title" => "Ver detalhes"
                        ]
                    ) ?>
                    <!-- Editar -->
                    <?= $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                        ),
                        [
                            'action' => 'editar_tipos_brindes_rede',
                            $tipo->id
                        ],
                        [
                            'class' => 'btn btn-primary btn-xs',
                            'escape' => false,
                            "title" => "Editar"
                        ]
                    ) ?>
                    <!-- Delete -->
                    <?= $this->Html->link(
                        __(
                            '{0} ',
                            $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
                        ),
                        '#',
                        [
                            'class' => 'btn btn-xs btn-danger btn-confirm',
                            "title" => "Deletar",
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-delete-with-message',
                            'data-message' => __(Configure::read('messageDeleteQuestion'), $tipo->nome),
                            'data-action' => Router::url(
                                [
                                    'action' => 'delete', $tipo->id,
                                    '?' =>
                                        [
                                        'return_url' => $this->request->here
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
