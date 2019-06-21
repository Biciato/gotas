<?php 

/**
 * @description Exibe tela de pesquisa de cliente, para atualizar os dados (view de Funcionário)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/PontuacoesComprovantes/pesquisar_cliente_final_pontuacoes.ctp
 * @date        20/02/2018
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'consulta_pontuacoes']); ?> 

<div class="col-lg-9"> 
<legend>Pesquisar Pontuações de Usuário</legend>

<?= $this->element('../Usuarios/filtro_usuarios_ajax') ?>

<!-- A pesquisa será em cima dos pontos dos clientes, então não há necessidade de restringir -->
<?= $this->Form->text('restrict_query', ['id' => 'restrict_query', 'value' => false, 'style' => 'display: none;']); ?>

<h4>Opções</h4>

    <div class="col-lg-4">

        <?= $this->Html->tag(
            'button',
            __('{0} Exibir Pontuações de Usuário', $this->Html->tag('i', '', ['class' => 'fa fa-edit'])),
            [
                'class' => 'btn btn-default btn-block disabled open-cliente-final-pontuacoes',
                'id' => 'open-cliente-final-pontuacoes'
            ]
        ); ?>

    </div>
 

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/pontuacoes_comprovantes/pesquisar_cliente_final_pontuacoes') ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/pontuacoes_comprovantes/pesquisar_cliente_final_pontuacoes.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>