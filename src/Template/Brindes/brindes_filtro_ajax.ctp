<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/brindes_filtro_ajax.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;

$usuarioVendaAvulsa = isset($usuarioVendaAvulsa) ? $usuarioVendaAvulsa : false;
$compraGotas = isset($compraGotas) ? $compraGotas : true;
$divComboBoxBrindes = $compraGotas ? "col-lg-4" : "col-lg-6";

?>
<div class="gifts-query-region">
    <h4>Selecione um brinde</h4>
    <form>
    <div class="row">

        <div class="<?= $divComboBoxBrindes ?>">
            <?= $this->Form->text(
                'restrict_query',
                array(
                    'id' => 'restrict_query',
                    'value' => true,
                    'style' => 'display: none;'
                )
            ); ?>
            <?= $this->Form->input(
                'lista_brindes',
                array(
                    'type' => 'select',
                    'id' => 'lista_brindes',
                    'class' => 'form-control list-gifts',
                    'label' => 'Brinde*',
                    "empty" => "<Selecionar>",
                    'required' => true
                )
            ) ?>
            <?= $this->Form->text(
                'brindes_id',
                [
                    'id' => 'brindes_id',
                    'style' => 'display: none;'
                ]
            ); ?>
            <?= $this->Form->text(
                'preco',
                [
                    'readonly' => true,
                    'required' => false,
                    'label' => false,
                    'id' => 'preco_banho',
                    'style' => 'display:none;'
                ]
            ) ?>
        </div>

        <?php if ($compraGotas) : ?>
        <div class="col-lg-2">
            <label for="current_password">Senha do usuário:*</label>
            <input type="password"
                class="current_password form-control"
                required
                id="current_password"
                />

        </div>

        <?php endif; ?>

        <div class="col-lg-6">
            <label for="gift-image">Imagem do Brinde</label>
            <br />
            <?= $this->Html->image(
                "/",
                array(
                    "name" => "gift-image",
                    "class" => "gift-image",
                    "label" => "Imagem do Brinde",
                )
            ) ?>
        </div>
    </div>

    </form>

</div>

<?php if (Configure::read('debug') == true) : ?>
<?= $this->Html->script('scripts/brindes/brindes_filtro_ajax') ?>
<?= $this->Html->css('styles/brindes/brindes_filtro_ajax') ?>
<?php else : ?>
<?= $this->Html->script('scripts/brindes/brindes_filtro_ajax.min') ?>
<?= $this->Html->css('styles/brindes/brindes_filtro_ajax.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
