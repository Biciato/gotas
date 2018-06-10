<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/brindes_form.ctp
 * @date     09/08/2017
 */

use Cake\Core\Configure;

?>

<?= $this->Form->hidden('clientes_id', ['value' => $clientesId]); ?>

<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->input('nome', ['id' => 'nome']); ?>
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-6">

        <?= $this->Form->input(
            'tempo_rti_shower',
            [
                'type' => 'number',
                'id' => 'tempo_rti_shower',
                'label' => 'Se for Smart Shower, informe o Tempo de Banho',
                'min' => 0,
                'max' => 20,
                'readonly' => false
            ]
        ) ?>
    </div>

<div class="col-lg-6">
    <?= $this->Form->input(
        'preco_padrao',
        [
            'type' => 'text',
            'id' => 'preco_padrao',
            'label' => 'Preço Padrão em Gotas'
        ]
    ); ?>
    </div>
</div>

<!-- Conferir o por que o botão está ficando desalinhado -->
<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->input(
            'ilimitado',
            [
                'type' => 'checkbox',
                'id' => 'ilimitado',
                'label' => false,
                'required' => false
            ]
        ); ?>
        <label for="ilimitado" class="form-check-label">
            Estoque de Brinde Ilimitado?
        </label>
    </div>
</div>

<!-- TODO: isto irá ser removido, pois agora os brindes de leitura de máquina serão todos vinculados a um gênero de brinde

    <div class="form-group">
    <label for="equipamento_rti_shower">Marque se este Brinde é um Smart Shower</label>
    <?= $this->Form->input(
        'equipamento_rti_shower',
        [
            'type' => 'checkbox',
            'id' => 'equipamento_rti_shower',
            'label' => false
        ]
    ) ?>
</div> -->

<div class="col-lg-12">
    <?= $this->Form->input(
        'nome_img',
        [
            'type' => 'file',
            'label' => 'Imagem do Brinde'
        ]
    ) ?>
</div>

<div class="col-lg-12 img-crop-container">
    <div class="form-group row">
        <div class="col-lg-6">
            <h5 style="font-weight: bold;">Imagem da Rede para Exibição:</h5>
            <img src="" id="img-crop" class="img-crop" name="img_crop"/>
        </div>

        <div class="col-lg-6">
            <h5 style="font-weight: bold;">Preview da Imagem:</h5>
            <div id="img-crop-preview" class="img-crop-preview" name="teste"></div>
        </div>
    </div>

    <div class="form-group row hidden">
        <div class="row">
            <div class="col-lg-12">
                <?= $this->Form->input('img-upload', ["type" => "text", "label" => false, "id" => "img-upload", "class" => "img-upload", "readonly" => true]) ?>
            </div>
        </div>
        <div class="row ">
            <div class="col-lg-2">
                <?= $this->Form->input('crop-height', ['type' => 'text', 'label' => 'crop height', 'id' => 'crop-height', 'readonly' => true]); ?>
            </div>
            <div class="col-lg-2">
                <?= $this->Form->input('crop-width', ['type' => 'text', 'label' => 'crop width', 'id' => 'crop-width', 'readonly' => true]); ?>
            </div>
            <div class="col-lg-2">
                <?= $this->Form->input('crop-x1', ['type' => 'text', 'label' => 'crop x', 'id' => 'crop-x1', 'readonly' => true]); ?>
            </div>
            <div class="col-lg-2">
                <?= $this->Form->input('crop-x2', ['type' => 'text', 'label' => 'crop x', 'id' => 'crop-x2', 'readonly' => true]); ?>
            </div>
            <div class="col-lg-2">
                <?= $this->Form->input('crop-y1', ['type' => 'text', 'label' => 'crop y', 'id' => 'crop-y1', 'readonly' => true]); ?>
            </div>
            <div class="col-lg-2">
                <?= $this->Form->input('crop-y2', ['type' => 'text', 'label' => 'crop y', 'id' => 'crop-y2', 'readonly' => true]); ?>
            </div>
        </div>
    </div>

</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/brindes/brindes_form') ?>
    <?= $this->Html->css("styles/brindes/brindes_form") ?>
<?php else : ?>
    <?= $this->Html->script('scripts/brindes/brindes_form.min') ?>
    <?= $this->Html->css("styles/brindes/brindes_form.min") ?>
<?php endif; ?>



<?= $this->fetch('script');
