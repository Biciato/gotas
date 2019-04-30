<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Gotas/atribuir_gotas_form.ctp
 * @date     06/08/2017
 *
 * Arquivo para atribuir gotas de cliente na view de funcionário
 */
use Cake\Core\Configure;


// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Impressão de Cupom', [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'impressao_rapida']) ?>

<?php if (isset($clientes_id)) : ?>

<?= $this->Form->input('clientes_id', [
    'type' => 'text',
    'class' => 'hidden',
    'id' => 'clientes_id',
    'value' => $clientes_id,
    'label' => false
]) ?>
<?php endif; ?>

<?= $this->Form->input(
    'id',
    [
        'type' => 'hidden',
        'id' => 'funcionarios_id',
        'value' => $funcionario->id
    ]
) ?>

<?= $this->Form->input(
    'estado_funcionario',
    [
        'type' => 'hidden',
        'id' => 'estado_funcionario',
        'value' => $estado_funcionario
    ]
) ?>

<?= $this->Form->input(
    'image_name',
    [
        'type' => 'hidden',
        'id' => 'image_name'
    ]
) ?>


<?php echo $this->element("../Cupons/form_emissao_brinde", array('showMenu' => false, 'show_breadcrumbs' => false)); ?>


<?php if (Configure::read('debug') == true) : ?>
<?= $this->Html->script('scripts/brindes/impressao_rapida') ?>
<?= $this->Html->css('styles/brindes/impressao_rapida') ?>
<?php else : ?>
<?= $this->Html->script('scripts/brindes/impressao_rapida.min') ?>
<?= $this->Html->css('styles/brindes/impressao_rapida.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
