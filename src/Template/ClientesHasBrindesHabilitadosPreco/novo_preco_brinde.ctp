<?php

/**
 * @var \App\View\AppView $this
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
        'action' => 'configurar_brindes_unidade', $clientesId
    ]
);

$this->Breadcrumbs->add('Configurar Brinde', ['controller' => 'clientes_has_brindes_habilitados', 'action' => 'configurar_brinde', $brindesId]);

$this->Breadcrumbs->add(__('Novo Preço de Brinde'), [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

// menu esquerdo
echo $this->element(
    '../ClientesHasBrindesHabilitadosPreco/left_menu',
    [
        'brindes_id' => $brindesId,
        'go_back_url' => [
            'controller' => 'clientes_has_brindes_habilitados',
            'action' => 'detalhes_brinde', $brindesId
        ]
    ]
) ?>
<div class="clientesHasBrindesHabilitados form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($novoPreco) ?>
    <fieldset>
        <legend><?= __('Adicionar novo preço para {0}', $brindeHabilitado->brinde->nome) ?></legend>
        <?= $this->element('../ClientesHasBrindesHabilitadosPreco/novo_preco_form') ?>
    </fieldset>
    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <button type="submit" class="btn btn-primary botao-confirmar"><i class="fa fa-save"> </i> Salvar</button>
            <a href="/clientesHasBrindesHabilitados/configurarBrinde/<?= $brindesId?>"
                class="btn btn-danger botao-cancelar">
                <i class="fa fa-window-close">
                </i>
                Cancelar
            </a>
        </div>
    </div>
</div>


