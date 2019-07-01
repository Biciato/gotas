<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Gotas/atribuir_gotas_form.ctp
 * @date     06/08/2017
 *
 * Arquivo para atribuir gotas de cliente na view de funcionário
 */
use Cake\Core\Configure;

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'smart_shower_avulso']) ?>

<div class="col-lg-9">
    <div id="impressao-rapida-escolha" class="display-content">
        <legend>Emissão de Venda Avulsa: </legend>

        <div class="col-lg-12 col-md-11 columns">
            <div class="col-lg-6">
                <center>
                    <h3>Smart Shower</h3>

                    <?php $banhoImg = $this->Html->image('products/rti_shower.jpg', ['alt' => 'Smart Shower', 'class' => 'btn', 'title' => 'Emissão de Cupom Smart Shower']); ?>

                    <?= $this->Html->tag('div', $banhoImg, ['class' => 'impressao-rapida-escolha-rti-shower-btn']) ?>
                </center>
            </div>

            <div class="col-lg-6">
                <center>
                    <h3>Brindes Diversos</h3>

                    <?php $brindesImg = $this->Html->image('products/gifts.jpg', ['alt' => 'Brindes', 'class' => 'btn', 'title' => 'Emissão de Cupom de Brinde Comum']); ?>

                    <?= $this->Html->tag('div', $brindesImg, ['escape' => false, 'class' => 'impressao-rapida-escolha-brinde-comum-btn']) ?>

                </center>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-9">


    <div class="display-content impressao-rapida-escolha-rti-shower">
        <?= $this->element('../Cupons/brinde_shower_avulso', ['showMenu' => false, 'show_breadcrumbs' => false]) ?>
    </div>

    <div class="display-content impressao-rapida-escolha-brinde-comum">
        <?= $this->element('../Cupons/brinde_comum_avulso', ['showMenu' => false, 'show_breadcrumbs' => false]) ?>
    </div>

</div>

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

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/brindes/resgate_brinde') ?>
    <?= $this->Html->css('styles/brindes/resgate_brinde') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/brindes/resgate_brinde.min') ?>
    <?= $this->Html->css('styles/brindes/resgate_brinde.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
