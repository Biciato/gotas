<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/brindes_filtro_ajax.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
?>

<div class="form-group ">


    <div class="gifts-query-region">

    <div class="row">
        <h4>Selecione um brinde</h4>
    </div>

    <div class="row">

    <div class="col-lg-6">

        <?= $this->Form->text('restrict_query', ['id' => 'restrict_query', 'value' => true, 'style' => 'display: none;']); ?>

        <?= $this->Form->input('lista_brindes', ['type' => 'select', 'id' => 'lista_brindes', 'class' => 'form-control list-gifts', 'label' => 'Brinde', 'required' => true]) ?>

            <?= $this->Form->text('brindes_id', ['id' => 'brindes_id', 'style' => 'display: none;']); ?>

            <?= $this->Form->text('preco', ['readonly' => true, 'required' => false, 'label' => false, 'id' => 'preco_banho', 'style' => 'display:none;']) ?>
        </div>

        <div class="col-lg-6" >
            <label for="gift-image">Imagem do Brinde</label>
            <br />
            <?= $this->Html->image("/", [
                "name" => "gift-image",
                "class" => "gift-image",
                "label" => "Imagem do Brinde",
                "style"=> "position: absolute;"

            ]) ?>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-4">
            <?= $this->Form->input(
                'current_password',
                [
                    'type' => 'password',
                    'id' => 'current_password',
                    'class' => 'current_password',
                    'label' => 'Confirmar senha do usuário'
                ]
            ) ?>

        </div>
    </div>
    </div>
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
