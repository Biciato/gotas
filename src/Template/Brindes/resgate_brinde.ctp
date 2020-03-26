<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/resgate_brinde.ctp
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

<?= $this->element('../Pages/left_menu', ['item_selected' => 'resgate_brinde']) ?>

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


<input type="hidden" name="tipo_pagamento" id="tipo-pagamento" class="tipo-pagamento" readonly="readonly" value="<?= TYPE_PAYMENT_POINTS ?>">
<input type="hidden" name="tipo_venda" id="tipo-venda" class="tipo-venda" readonly="readonly" value="<?php echo implode(",", [TYPE_SELL_CURRENCY_OR_POINTS_TEXT, TYPE_SELL_FREE_TEXT]) ?>">
<!-- <input type="hidden" name="tipo_venda" id="tipo-venda" class="tipo-venda" readonly="readonly" value="<?php echo implode(",", [TYPE_SELL_CURRENCY_OR_POINTS_TEXT]) ?>"> -->
<?php echo $this->element("../Cupons/form_emissao_brinde", array('showMenu' => false, 'show_breadcrumbs' => false, "tipoPagamento" => TYPE_PAYMENT_POINTS)); ?>

<?php

$extension = Configure::read("debug") ? ""  : ".min";
?>
<script src="/webroot/js/scripts/brindes/resgate_brinde<?= $extension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/css/styles/brindes/resgate_brinde<?= $extension ?>.css?<?= SYSTEM_VERSION ?>">

<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
