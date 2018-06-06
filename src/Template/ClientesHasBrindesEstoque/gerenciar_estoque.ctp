<?php 
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesEstoque/gerenciar_estoque.ctp
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

$this->Breadcrumbs->add('Configurar Brinde', ['controller' => 'clientes_has_brindes_habilitados', 'action' => 'configurar_brinde', $brindes_id]);

$this->Breadcrumbs->add(__('Gerenciar Estoque'), [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element(
    '../ClientesHasBrindesEstoque/left_menu',
    [
        'brindes_id' => $cliente_has_brinde_habilitado->id,
        'mode' => 'addStock',
    ]
) ?>


<div class="clientesHasBrindeHabilitados view col-lg-9 col-md-8 columns content">
    <legend><?= h(__("Gerenciar Estoque de Brinde {0}", $cliente_has_brinde_habilitado->brinde->nome)) ?></legend>
    <table class="table table-striped table-hover">
        <tr>
            <th scope="row"><?= __('Nome') ?></th>
            <td><?= $cliente_has_brinde_habilitado->brinde->nome ?></td>
        </tr>
       
        <tr>
            <th scope="row"><?= __('Estoque Atual') ?></th>
            <td><?= $cliente_has_brinde_habilitado->estoque[0] ?></td>
        </tr>
        
    </table>
    
</div>
