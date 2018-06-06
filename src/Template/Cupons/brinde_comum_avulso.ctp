<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/brinde_comum.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$show_breadcrumbs = isset($show_breadcrumbs) ? $show_breadcrumbs : true;

if ($show_breadcrumbs) {
    $this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

    $this->Breadcrumbs->add('Escolher Brinde', ['controller' => 'cupons', 'action' => 'escolher_brinde']);

    $this->Breadcrumbs->add('Emissão de Brinde Comum', [], ['class' => 'active']);

    echo $this->Breadcrumbs->render(
        ['class' => 'breadcrumb']
    );
}

$showMenu = isset($showMenu) ? $showMenu : true;

$title = __("Emissão de Cupom Brinde Comum Avulso");

?>

<?php if ($showMenu) : ?>
    
    <?= $this->element('../Cupons/left_menu', ['mode' => 'print', 'controller' => 'Cupons', 'action' => 'escolher_brinde']) ?>

<?php endif; ?>
    <div class="col-lg-9 col-md-10 columns">

        <legend><?= $title ?></legend>

        <div class="brinde-comum-container">

            <?= $this->Form->create(); ?>

                <!-- Id de Clientes -->
                <?= $this->Form->text('clientes_id', [
                    'id' => 'clientes_id',
                    'value' => $cliente->id,
                    'style' => 'display: none;'
                ]); ?>

                <!-- Id de Usuários -->
                <?= $this->Form->text('usuarios_id_brinde_comum', [
                    'id' => 'usuarios_id_brinde_comum',
                    'class' => 'usuarios_id_brinde_comum',
                    'value' => 'conta_avulsa',
                    'style' => 'display: none;'
                ]); ?>
                
                <div class="gifts-query-region">

                    <div class="col-lg-12">
                        <h4>Selecione um brinde</h4>
                    </div>

                    <div class="col-lg-6">
                        <?= $this->Form->input(
                            'lista_brindes_comum',
                            [
                                'type' => 'select',
                                'id' => 'lista_brindes_comum',
                                'class' => 'form-control list-gifts-comum',
                                'label' => 'Brindes',
                                'required' => true
                            ]
                        ) ?>
                    </div>

                    <div class="col-lg-6">
                        <?= $this->Form->input('quantidade', [
                            'type' => 'number',
                            'readonly' => false,
                            'required' => true,
                            'label' => 'Quantidade',
                            'min' => 1,
                            'id' => 'quantidade',
                            'class' => 'quantidade-brindes',
                            'step' => 1.0,
                            'default' => 0,
                            'min' => 0
                        ]) ?>
                    </div>

                    <?= $this->Form->text(
                        'brindes_id',
                        [
                            'id' => 'brindes_id',
                            'style' => 'display: none;'
                        ]
                    ); ?>

                    <?= $this->Form->text(
                        'preco',
                        [
                            'readonly' => true,
                            'required' => false,
                            'label' => false,
                            'id' => 'preco_banho',
                            'style' => 'display:none;'
                        ]
                    ) ?>
                    
                    <div class="col-lg-12">
                        <?= $this->Form->button(
                            __('{0} Imprimir', $this->Html->tag('i', '', ['class' => 'fa fa-print'])),
                            [
                                'type' => 'button',
                                'id' => 'print_gift',
                                'escape' => false,
                                'class' => 'print-gift-comum'
                            ]
                        ) ?>
                        
                    </div>

                </div>
            
            <?= $this->Form->end(); ?>
            
        </div>    
    </div>

<div class="hidden">
    <?= $this->element('../Cupons/impressao_cupom_layout') ?>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/cupons/imprime_brinde_comum') ?>
    <?= $this->Html->css('styles/cupons/imprime_brinde_comum') ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/cupons/imprime_brinde_comum.min') ?>
    <?= $this->Html->css('styles/cupons/imprime_brinde_comum.min') ?>
<?php endif; ?>


<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
