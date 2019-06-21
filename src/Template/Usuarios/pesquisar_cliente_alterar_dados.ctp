<?php 

/**
 * @description Exibe tela de pesquisa de cliente, para atualizar os dados (view de Funcionário)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/pesquisar_cliente_alterar_dados.ctp
 * @date        16/02/2018
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'atualizar_cadastro_cliente']); ?> 

<div class="col-lg-9"> 
<legend>Atualizar Cadastro de Usuário</legend>

<?= $this->element('../Usuarios/filtro_usuarios_ajax') ?>

<!-- Restringir para somente usuários da rede (que tem pontos) -->
<?= $this->Form->text('restrict_query', ['id' => 'restrict_query', 'value' => true, 'style' => 'display: none;']); ?>

<h4>Opções</h4>

<div class="col-lg-4">

    <?= $this->Html->tag(
        'button',
        __('{0} Editar Cadastro de Usuário', $this->Html->tag('i', '', ['class' => 'fa fa-edit'])),
        [
            'class' => 'btn btn-default btn-block disabled open-cadastro-usuario',
            'id' => 'open-cadastro-usuario'
        ]
    ); ?>

</div>
<div class="col-lg-4">

    <?= $this->Html->tag(
        'button',
        __('{0} Editar Veículos de Usuário', $this->Html->tag('i', '', ['class' => 'fa fa-edit'])),
        [
            'class' => 'btn btn-default btn-block disabled open-cadastro-veiculos-usuario',
            'id' => 'open-cadastro-veiculos-usuario'
        ]
    ); ?>

</div>
<div class="col-lg-4">

    <?= $this->Html->tag(
        'button',
        __('{0} Editar Transportadoras de Usuário', $this->Html->tag('i', '', ['class' => 'fa fa-edit'])),
        [
            'class' => 'btn btn-default btn-block disabled open-cadastro-transportadoras-usuario',
            'id' => 'open-cadastro-transportadoras-usuario'
        ]
    ); ?>

</div>
 

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/usuarios/pesquisar_cliente_alterar_dados') ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/usuarios/pesquisar_cliente_alterar_dados.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>