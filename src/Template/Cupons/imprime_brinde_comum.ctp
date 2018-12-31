<?php
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/imprimir_brinde_comum.ctp
 * @date     28/01/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Unidades da Rede', ['controller' => 'redes', 'action' => 'escolher_unidade_rede', $redes_id]);

$this->Breadcrumbs->add('Escolha um Brinde para Resgatar', ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'escolher_brinde_unidade', $clientes_id]);

$this->Breadcrumbs->add('Confirmar Brinde para Resgate', ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'resgatar_brinde', $clientes_has_brindes_habilitados_id]);

$this->Breadcrumbs->add('Impressão do Cupom', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../ClientesHasBrindesHabilitados/left_menu', []) ?>

<div class="redes index col-lg-9 col-md-10 columns content">
    <legend>Impressão do Cupom para Resgatar os Brindes</legend>

    <div class="col-lg-4">
        <?= $this->element('../Cupons/impressao_cupom_layout') ?>

        <?= $this->Html->tag(
            'div',
            __(
                "{0} Imprimir",
                $this->Html->tag('i', '', ['class' => 'fa fa-print'])
            ),
            ['class' => 'btn btn-primary print-button']
        ) ?>
    </div>
</div>

<?php if (Configure::read('debug')) : ?>
    <?= $this->Html->script('scripts/cupons/imprimir_brinde_comum') ?>

<?php else : ?>
    <?= $this->Html->script('scripts/cupons/imprimir_brinde_comum.min') ?>

<?php endif; ?>

<?= $this->fetch('script') ?>
