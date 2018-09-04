<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Redes/redes_form.ctp
 * @date     22/11/2017
 */

use Cake\Core\Configure;
?>

<div class="col-lg-12">
    <?= $this->Form->control('nome_rede', ['label' => 'Nome da Rede']); ?>
</div>
<div class="col-lg-12">
    <?= $this->Form->input(
        'nome_img',
        [
            'type' => 'file',
            'label' => 'Banner da Rede'
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

<div class="col-lg-12">
    <?php echo $this->Form->input(
        'quantidade_pontuacoes_usuarios_ida',
        array(
            "type" => "number",
            "min" => 1,
            "max" => 365,
            "default" => 3,
            "label" => "Máximo de Abastecimento de Gotas Diárias por Usuários"
        )
    ); ?>
</div>

<div class="col-lg-12">
    <?= $this->Form->control('ativado', ['label' => 'Rede Ativada']); ?>
</div>
<div class="col-lg-12">
    <?= $this->Form->input(
        'permite_consumo_gotas_funcionarios',
        [
            'type' => 'checkbox',
            'class' => 'form-check-input',
            'label' => 'Permite o Consumo de Brindes e Gotas por funcionários da Rede?'

        ]
    ); ?>
</div>

<div class="col-lg-12">
    <?= $this->Form->button(
        __('{0} Salvar', $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
        [
            'class' => 'btn btn-primary',
            'escape' => false
        ]
    ) ?>
</div>


<?php if (Configure::read('debug')) : ?>
    <?= $this->Html->script('scripts/redes/redes_form'); ?>
    <?= $this->Html->css("styles/redes/redes_form"); ?>
<?php else : ?>
    <?= $this->Html->script('scripts/redes/redes_form.min'); ?>
    <?= $this->Html->css("styles/redes/redes_form.min"); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>
<?= $this->fetch('css'); ?>
