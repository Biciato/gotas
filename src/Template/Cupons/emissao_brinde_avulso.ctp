<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/emissao_brinde_avulso.ctp
 * @date     21/06/2018
 */

use Cake\Core\Configure;

?>

<?= $this->element('../Pages/left_menu', ['mode' => 'escolher_brinde', 'controller' => 'cupons', 'action' => 'print_gift', 'item_selected' => 'emissao_brinde_avulso']) ?>

<div class="col-lg-9 col-md-10 columns">

    <div class="container-emissao-cupom">
        <legend>Emissão de Brinde Avulso</legend>

        <input type="hidden" id="restrict_query" class="restrict_query" value="<?php $restrict_query ?>" />

        <!-- Id de Usuários -->
        <?= $this->Form->text('usuarios_id', [
            'id' => 'usuarios_id',
            'class' => 'usuarios_id',
            'value' => 'conta_avulsa',
            'style' => 'display: none;'
        ]); ?>


        <?= $this->Form->text('clientes_id', ['id' => 'clientes_id', 'value' => $cliente->id, 'style' => 'display: none;']); ?>

        <?= $this->element('../Brindes/brindes_filtro_ajax', ["usuarioVendaAvulsa" => true]) ?>

        <div class="gifts-query-region">

            <div class="col-lg-12">
                <?= $this->Form->button(
                    __('{0} Imprimir', $this->Html->tag('i', '', ['class' => 'fa fa-print'])),
                    [
                        'type' => 'button',
                        'id' => 'print_gift',
                        'escape' => false,
                        'class' => 'print-gift-shower'
                    ]
                ) ?>

                <?= $this->Html->tag('div', '', ['class' => 'text-danger validation-message', 'id' => 'print-validation']) ?>
                <?= $this->Html->tag('/div') ?>
            </div>
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
