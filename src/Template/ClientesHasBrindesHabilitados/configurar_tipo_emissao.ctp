<?php

/**
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date 2018/05/15
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// Menu de navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] <= (int)Configure::read('profileTypes')['AdminRegionalProfileType']) {

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

$this->Breadcrumbs->add(__('Configurar Tipo de Emissão'), [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

// menu esquerdo
echo $this->element(
    '../ClientesHasBrindesHabilitadosPreco/left_menu',
    [
        'brindes_id' => $brindes_id
    ]
) ?>
<div class="clientesHasBrindesHabilitados form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($brinde_habilitado) ?>
    <fieldset>
        <legend><?= __('Configurar Tipo de Emissão para Brinde {0}', $brinde_habilitado->brinde->nome) ?></legend>
        <?php
        echo $this->Form->input(
            "tipo_codigo_barras",
            [
                "label" => "Tipo de Código de Barras:",
                "type" => "select",
                'empty' => "<Selecionar>",
                "options" => [
                    "QRCode" => "QRCode",
                    "Code128" => "Code128",
                    "PDF417" => "PDF417"
                ]
            ]
        );
        ?>
    </fieldset>
    <?= $this->Form->button(__('{0} Salvar', $this->Html->tag('i', '', ['class' => 'fa fa-save'])), ['escape' => false]) ?>
    <?= $this->Form->end() ?>
</div>


