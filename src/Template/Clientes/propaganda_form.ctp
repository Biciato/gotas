<?php

/**
 * Template para cadastro de propaganda e link de quando for rede e clientes
 *
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/propaganda_form.ctp
 * @since    2018/08/06
 */

use Cake\Core\Configure;
use Cake\Routing\Router;


?>

<div class="col-lg-12">
    <?= $this->Form->control('propaganda_link', ['label' => 'Link para Propaganda*', "id" => "propaganda_link", "required"]); ?>
</div>

<div class="col-lg-12">
    <?= $this->Form->input(
        'propaganda_img',
        [
            'type' => 'file',
            'label' => 'Propaganda para Exibição*',
            "id" => "propaganda_img",
            "accept" => ".png,.jpg",
            "required"
        ]
    ) ?>
</div>

<?php if ($imagemExistente) : ?>
    <div class="col-lg-12">

    <?php echo $this->Form->label("Imagem Atualmente Alocada") ?>
    <br />
    <img src="<?php echo $imagem ?>" class="imagem-rede" />
    </div>
    <?php endif; ?>

<div class="col-lg-12 img-crop-container">
    <div class="form-group row">
        <div class="col-lg-6">
            <h5 style="font-weight: bold;">Imagem da Propaganda para Exibição:</h5>
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


<div class="form-group row ">
    <div class="col-lg-12 text-right">
        <button type="submit"
            class="btn btn-primary botao-confirmar"
            >
            <span class="fa fa-save"></span>
            Salvar
        </button>
        <a onclick="history.go(-1); return false;"
            class="btn btn-danger botao-cancelar"
            >
            <span class="fa fa-window-close"></span>
            Cancelar
        </a>
    </div>
</div>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/clientes/propaganda_form'); ?>
    <?= $this->Html->css("styles/clientes/propaganda_form"); ?>
<?php else : ?>
    <?= $this->Html->script('scripts/clientes/propaganda_form.min'); ?>
    <?= $this->Html->css("styles/clientes/propaganda_form.min"); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>
<?= $this->fetch('css'); ?>
