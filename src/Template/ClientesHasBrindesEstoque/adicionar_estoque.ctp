<?php 
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesEstoque/adicionar_estoque.ctp
 * @date     09/08/2017
 */
// Referências
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

$this->Breadcrumbs->add(
    'Configurar Brinde',
    [
        'controller' => 'clientes_has_brindes_habilitados',
        'action' => 'configurar_brinde',
        $brindes_id
    ]
);

$this->Breadcrumbs->add(
    __('Gerenciar Estoque'),
    [
        'controller' => 'clientes_has_brindes_habilitados',
        'action' => 'gerenciar_estoque', $brindes_id
    ]
);

$this->Breadcrumbs->add(__('Adicionar Estoque de Brinde'), [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>

<?= $this->element(
    '../ClientesHasBrindesEstoque/left_menu',
    [
        'mode' => 'editStock',
    ]
) ?>

<div class="brindesEstoque form col-lg-9 col-md-10 columns content">
    <?= $this->Form->create($brinde_estoque) ?>
    <fieldset>
        <legend><?= __('Adicionar Estoque para Brinde') ?></legend>
        <?= $this->element('../ClientesHasBrindesEstoque/brindes_estoque_form', ['required_tipo_operacao' => false, 'required_data' => false]) ?>

        <div class='col-lg-12'>
            <?= $this->Form->button(
                __(
                    '{0} Salvar',
                    $this->Html->tag('i', '', ['class' => 'fa fa-save'])
                ),
                [
                    'class' => 'btn btn-primary',
                    'escape' => false
                ]

            ) ?>
        
        </div>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
