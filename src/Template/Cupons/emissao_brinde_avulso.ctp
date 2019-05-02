<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/emissao_brinde_avulso.ctp
 * @date     21/06/2018
 */

use Cake\Core\Configure;

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Impressão de Cupom', [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Pages/left_menu', ['mode' => 'escolher_brinde', 'controller' => 'cupons', 'action' => 'print_gift', 'item_selected' => 'emissao_brinde_avulso']) ?>

<div class="col-lg-9 col-md-10 columns">

    <div class="container-emissao-cupom">
        <legend>Emissão de Venda Avulsa</legend>

        <input type="hidden" id="restrict_query" class="restrict_query" value="<?php $restrict_query ?>" />
        <input type="hidden" id="venda_avulsa" class="venda_avulsa" value="true" />
        <input type="hidden" name="tipo_pagamento" id="tipo-pagamento" class="tipo-pagamento" readonly="readonly" value="<?= TYPE_PAYMENT_MONEY?>">
        <input type="hidden" name="tipo_venda" id="tipo-venda" class="tipo-venda" readonly="readonly" value="<?= TYPE_SELL_CURRENCY_OR_POINTS_TEXT ?>">

        <!-- Id de Usuários -->
        <?= $this->Form->text(
            'usuarios_id',
            array(
                'id' => 'usuarios_id',
                'class' => 'usuarios_id',
                'style' => 'display: none;'
            )
        ); ?>

        <div class="form-group row">
            <?php echo $this->element("../Usuarios/filtro_usuarios_ajax", array("isVendaAvulsa" => 1)); ?>
        </div>

        <?php echo $this->Form->text(
            'clientes_id',
            array(
                'id' => 'clientes_id',
                'value' => $cliente->id,
                'style' => 'display: none;'
            )
        ); ?>

        <div class="form-group">
            <?php echo $this->element('../Brindes/brindes_filtro_ajax', array("usuarioVendaAvulsa" => true, "compraGotas" => false)) ?>
        </div>

        <div class="gifts-query-region">

            <div class="col-lg-12 text-right">
                <button type="button"
                    id="print_gift"
                    class="print-gift-shower btn btn-primary" >
                    <i class="fa fa-print"></i>
                    Imprimir
                </button>

                <button type="button"
                    class="print-gift-cancel btn btn-default" id="print-gift-cancel">
                <i class="fa fa-trash"></i>
                Limpar
                </button>

                <?= $this->Html->tag('div', '', ['class' => 'text-danger validation-message', 'id' => 'print-validation']) ?>
                <?= $this->Html->tag('/div') ?>
            </div>
        </div>

     <!-- Confirmação cupom -->
    <?php
    echo $this->element("../Cupons/confirmacao_emissao_cupom");
    ?>

    <!-- Confirmação canhoto -->
    <?php
    echo $this->element("../Cupons/confirmacao_canhoto");
    ?>
    </div>


</div>

<?php
echo $this->element('../Cupons/impressao_brinde_layout');
?>

<?php
echo $this->element("../Cupons/impressao_canhoto_layout");
?>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/cupons/imprime_brinde') ?>
    <?= $this->Html->css('styles/cupons/imprime_brinde') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/cupons/imprime_brinde.min') ?>
    <?= $this->Html->css('styles/cupons/imprime_brinde.min') ?>
<?php endif; ?>


<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
