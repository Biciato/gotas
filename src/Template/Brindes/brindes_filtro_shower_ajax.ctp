<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/brindes_filtro_shower_ajax.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
?>

<div class="form col-lg-12">

    <div class="gifts-query-region">

        <h4>Selecione um brinde</h4>

        <?= $this->Form->text('restrict_query', ['id' => 'restrict_query', 'value' => true, 'style' => 'display: none;']); ?>

        <?= $this->Form->input('lista_brindes_rti_shower', ['type' => 'select', 'id' => 'lista_brindes_rti_shower', 'class' => 'form-control list-gifts', 'label' => 'Smart Shower', 'required' => true]) ?>

        <?= $this->Form->text('brindes_id', ['id' => 'brindes_id', 'style' => 'display: none;']); ?>

        <?= $this->Form->text('preco', ['readonly' => true, 'required' => false, 'label' => false, 'id' => 'preco_banho', 'style' => 'display:none;']) ?>

    </div>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/brindes/brindes_filtro_shower_ajax') ?>
    <?= $this->Html->css('styles/brindes/brindes_filtro_shower_ajax') ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/brindes/brindes_filtro_shower_ajax.min') ?>
    <?= $this->Html->css('styles/brindes/brindes_filtro_shower_ajax.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>