<?php

/**
 * index.ctp
 *
 * View para tipos_brindes_redes/index
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TiposBrindesRedes[]|\Cake\Collection\CollectionInterface $tipos_brindes_redes
 *
 * @category View
 * @package App\Template\TiposBrindesRedes
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
$this->Breadcrumbs->add('Escolher Rede para Configurar Tipos de Brindes', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element(
    '../TiposBrindesRedes/left_menu',
    [
        'mode' => 'view',
        'show_reports' => false
    ]
) ?>
<div class="redes index col-lg-9 col-md-10 columns content">
    <legend><?= __('Escolher Rede para Configurar Tipos de Brindes') ?></legend>

    <?= $this->element(
        '../Redes/filtro_redes',
        [
            'controller' => 'tipos_brindes_redes',
            'action' => 'index'
        ]
    ) ?>

     <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('nome_rede') ?></th>
                <th scope="col" class="actions"><?= __('Ações') ?>
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
            <?php foreach ($redes as $rede) : ?>
            <tr>
                <td><?= h($rede->nome_rede) ?></td>
                <td class="actions">
                    <?= $this->Html->link(
                        __(
                            "{0}",
                            $this->Html->tag('i', '', ['class' => 'fa fa-cogs'])
                        ),
                        [
                            'controller' => 'tiposBrindesRedes',
                            'action' => 'configurar_tipos_brindes_rede', $rede->id

                        ],
                        [
                            'class' => 'btn btn-xs btn-primary',
                            'title' => 'Configurar',
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
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
</div>
