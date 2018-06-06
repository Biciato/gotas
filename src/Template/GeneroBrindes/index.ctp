<?php

/**
 * index.ctp
 *
 * View para genero_brindes/index
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\GeneroBrinde[]|\Cake\Collection\CollectionInterface $genero_brindes
 *
 * @category View
 * @package App\Template\GeneroBrindes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date 30/05/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.0.0
 *
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Gênero de Brindes', [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element(
    '../GeneroBrindes/left_menu',
    [
        'mode' => 'view',
        'show_reports' => false
    ]
) ?>
<div class="redes index col-lg-9 col-md-10 columns content">
    <legend><?= __('Gênero de Brindes') ?></legend>

    <?= $this->element(
        '../GeneroBrindes/filtro_genero_brindes',
        [
            'controller' => 'genero_brindes',
            'action' => 'index'
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
            <?php foreach ($generoBrindes as $genero) : ?>
            <tr>
                <td><?= h($genero->nome . ($genero->brinde_necessidades_especiais == 1 ? " (PNE)" : null)) ?> </td>
                <td><?= h($this->Boolean->convertBooleanToString($genero->equipamento_rti)) ?> </td>
                <td><?= h($this->Boolean->convertBooleanToString($genero->brinde_necessidades_especiais)) ?> </td>
                <td><?= h($this->Boolean->convertEnabledToString($genero->habilitado)) ?> </td>
                <td><?= h($this->Boolean->convertBooleanToString($genero->atribuir_automatico)) ?> </td>
                <td class="actions" style="white-space:nowrap">
                    <!-- Info -->

                    <?= $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                        ),
                        [
                            'action' => 'ver_detalhes',
                            $genero->id
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
                            'action' => 'editar_genero_brinde',
                            $genero->id
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
                            'data-message' => __(Configure::read('messageDeleteQuestion'), $genero->nome),
                            'data-action' => Router::url(
                                [
                                    'action' => 'delete', $genero->id,
                                    '?' =>
                                        [
                                        'genero_brinde_id' => $genero->id,
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
