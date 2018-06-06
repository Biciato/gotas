<?php

/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// Menu de navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($user_logged['tipo_perfil'] <= (int)Configure::read('profileTypes')['AdminRegionalProfileType']) {

    $this->Breadcrumbs->add(
        'Escolher Unidade para Configurar os Brindes',
        [
            'controller' => 'clientes_has_brindes_habilitados',
            'action' => 'escolher_unidade_config_brinde'
        ]
    );
}

$this->Breadcrumbs->add(
    'Configurar um Brinde de Unidade',
    [
        'controller' => 'clientes_has_brindes_habilitados',
        'action' => 'configurar_brindes_unidade', $clientes_id
    ]
);

$this->Breadcrumbs->add('Configurar Brinde', ['controller' => 'clientes_has_brindes_habilitados', 'action' => 'configurar_brinde', $brindes_id]);

$this->Breadcrumbs->add(__('Novo Preço de Brinde'), [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

// menu esquerdo
echo $this->element(
    '../ClientesHasBrindesHabilitadosPreco/left_menu',
    [
        'brindes_id' => $brindes_id,
        'go_back_url' => [
            'controller' => 'clientes_has_brindes_habilitados',
            'action' => 'detalhes_brinde', $brindes_id
        ]
    ]
) ?>
<div class="clientesHasBrindesHabilitados form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($novo_preco) ?>
    <fieldset>
        <legend><?= __('Adicionar novo preço para {0}', $brinde_habilitado->brinde->nome) ?></legend>


        <?= $this->element('../ClientesHasBrindesHabilitadosPreco/novo_preco_form') ?>
    </fieldset>
    <?= $this->Form->button(__('{0} Salvar', $this->Html->tag('i', '', ['class' => 'fa fa-save'])), ['escape' => false]) ?>
    <?= $this->Form->end() ?>
</div>


