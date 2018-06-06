<?php 

/**
 * @description Exibe tela de pesquisa de cliente, para buscar os brindes (view de Funcionário)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/UsuariosHasBrindes/pesquisar_cliente_final_brindes.ctp
 * @date        21/02/2018
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'historico_brindes']); ?> 

<div class="col-lg-9"> 
<legend>Pesquisar Brindes do Usuário</legend>

<?= $this->element('../Usuarios/filtro_usuarios_ajax') ?>

<!-- A pesquisa será em cima dos brindes dos clientes, então não há necessidade de restringir -->
<?= $this->Form->text('restrict_query', ['id' => 'restrict_query', 'value' => false, 'style' => 'display: none;']); ?>

<h4>Opções</h4>

    <div class="col-lg-4">

        <?= $this->Html->tag(
            'button',
            __('{0} Exibir Brindes de Usuário', $this->Html->tag('i', '', ['class' => 'fa fa-edit'])),
            [
                'class' => 'btn btn-default btn-block disabled open-cliente-final-brindes',
                'id' => 'open-cliente-final-brindes'
            ]
        ); ?>

    </div>
 

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/usuarios_has_brindes/pesquisar_cliente_final_brindes') ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/usuarios_has_brindes/pesquisar_cliente_final_brindes.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>