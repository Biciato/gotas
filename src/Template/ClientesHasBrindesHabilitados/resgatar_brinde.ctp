<?php 
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesHabilitados/resgatar_brinde.ctp
 * @date     27/01/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\I18n\Number;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Unidades da Rede', ['controller' => 'redes', 'action' => 'escolher_unidade_rede', $redes_id]);

$this->Breadcrumbs->add('Escolha um Brinde para Resgatar', ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'escolher_brinde_unidade', $brinde_habilitado->clientes_id]);

$this->Breadcrumbs->add('Confirmar Brinde e Quantidade para Resgate', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?> 

<?= $this->element('../ClientesHasBrindesHabilitados/left_menu', []) ?> 

<div class="redes index col-lg-9 col-md-10 columns content">
    <legend>Confirmar Brinde e Quantidade para Resgate</legend>

    <?= $this->Form->create($brinde_habilitado, ['class' => 'form_resgate_brinde', 'enctype' => 'multipart/form-data']) ?>

    <?= $this->Form->control(
        'saldo_pontos',
        [
            'type' => 'text',
            'label' => 'Saldo de Pontos',
            'value' => Number::precision($saldo_atual, 2),
            'disabled' => true
        ]
    ) ?>

    <?= $this->Form->input('nome', [
        'type' => 'text',
        'label' => 'Nome:',
        'value' => $brinde_habilitado->brinde->nome,
        'disabled' => true

    ]) ?>
    
    <?= $this->Form->input('valor_unitario', [
        'type' => 'text',
        'label' => 'Valor Unitário:',
        'value' => $brinde_habilitado->brinde_habilitado_preco_atual->preco,
        'disabled' => true

    ]) ?>

    <?= $this->Form->control(
        'quantidade',
        [
            'type' => 'number',
            'label' => 'Quantidade:',
            'step' => 1.0,
            'default' => 0,
            'max' => $brinde_habilitado->estoque[0],
            'min' => 0

        ]
    ) ?>

    <?= $this->Form->button(
        __(
            "{0} Resgatar",
            $this->Html->tag('i', '', ['class' => 'fa fa-shopping-cart'])
        ),
        [
            'escape' => false
        ]

    ) ?>

<!-- <?= $this->Html->link(
        __(
            "{0} Resgatar",
            $this->Html->tag('i', '', ['class' => 'fa fa-shopping-cart'])
        ),
        '#',
        [
            'title' => 'Resgatar',
            'class' => 'btn btn-primary btn-confirm',
            'data-toggle' => 'modal',
            'data-target' => '#modal-confirm-with-message',
            'data-message' => __("Confirma a aquisição dos produtos e sua respectiva quantidade?"),
            'data-action' => Router::url(
                [
                    'action' => 'resgatar_brinde', $brinde_habilitado->id
                ]
            ),
            'escape' => false
        ],
        false
    ); ?> -->

    <?= $this->Form->end() ?>

    
</div>