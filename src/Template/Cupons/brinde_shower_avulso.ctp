<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/brinde_shower_avulso.ctp
 * @date     17/04/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$show_breadcrumbs = isset($show_breadcrumbs) ? $show_breadcrumbs : true;

if ($show_breadcrumbs) {

    $this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
    $this->Breadcrumbs->add('Escolher Brinde', ['controller' => 'cupons', 'action' => 'escolher_brinde']);
    $this->Breadcrumbs->add('Emissão de Cupom Smart Shower', [], ['class' => 'active']);

    echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);
}

$showMenu = isset($showMenu) ? $showMenu : true;

$title = __("Emissão de Cupom Smart Shower Avulso");

?>

<?php if ($showMenu) : ?>
    
    <?= $this->element(
        '../Cupons/left_menu',
        [
            'mode' => 'print',
            'controller' => 'Cupons',
            'action' => 'escolher_brinde'
        ]
    ) ?>

    <div class="col-lg-9 col-md-10 columns">

<?php else : ?> 
    <div class="col-lg-12 col-md-12 columns">
<?php endif; ?>

    <legend><?= __($title) ?></legend>

    <?= $this->Form->create(); ?>

        <!-- Id de Clientes -->
        <?= $this->Form->text('clientes_id', [
            'id' => 'clientes_id',
            'value' => $cliente->id,
            'style' => 'display: none;'
        ]); ?>

        <!-- Id de Usuários -->
        <?= $this->Form->text('usuarios_id_brinde_shower', [
            'id' => 'usuarios_id_brinde_shower',
            'class' => 'usuarios_id_brinde_shower',
            'value' => 'conta_avulsa',
            'style' => 'display: none;'
        ]); ?>
        
        <div class="form-group row">
            <?= $this->element('../Brindes/brindes_filtro_shower_ajax') ?>
        </div>
        
        <div class="gifts-query-region">
    
            <div class="form-group row">
                <div class="col-lg-6">
                        <!-- Sexo  -->
                    <?= $this->Form->input('sexo', [
                        'id' => 'sexo_brinde_shower',
                        'class' => 'sexo_brinde_shower',
                        'empty' => true,
                        'options' =>
                            [
                            '1' => 'Masculino',
                            '0' => 'Feminino'
                        ]
                    ]); ?>
                </div>

                <div class="col-lg-6">

                    <!-- Necessidades Especiais -->
                    <?= $this->Form->input('necessidades_especiais', [
                        'label' => 'Portador de Nec. Especiais?',
                        'id' => 'necessidades_especiais_brinde_shower',
                        'class' => 'necessidades_especiais_brinde_shower',
                        'empty' => true,
                        'options' => [
                            1 => 'Sim',
                            0 => 'Não',
                        ]
                    ]) ?>

                </div>
            
            </div>
            
            <div class="form-group row">
                <div class="col-lg-12">
                    <?= $this->Form->button(
                        __('{0} Imprimir', $this->Html->tag('i', '', ['class' => 'fa fa-print'])),
                        [
                            'type' => 'button',
                            'id' => 'print_gift',
                            'escape' => false,
                            'class' => 'print-gift-shower'
                        ]
                    ) ?>

                    <?= $this->Html->tag('div', '', ['class' => 'text-danger validation-message', 'id' => 'print-validation']) ?>
                    <?= $this->Html->tag('/div') ?>
                </div>
            </div>
            
        </div>

    <?= $this->Form->end(); ?>
</div>
<?= $this->element('../Cupons/impressao_shower_layout') ?>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/cupons/imprime_brinde_shower') ?>
    <?= $this->Html->css('styles/cupons/imprime_brinde_shower') ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/cupons/imprime_brinde_shower.min') ?>
    <?= $this->Html->css('styles/cupons/imprime_brinde_shower.min') ?>
<?php endif; ?>


<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
