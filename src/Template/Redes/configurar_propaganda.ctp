<?php

/**
 * Template para cadastro de propaganda e link de quando for rede e clientes
 *
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/redes/propaganda_form.ctp
 * @since    2018/08/06
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Escolher Unidade para Configurar Propagandas', array("controller" => "RedesHasClientes", "action" => "propaganda_escolha_unidades"));
$this->Breadcrumbs->add('Propaganda Para a Rede', array(), array("class" => "active"));

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../RedesHasClientes/left_menu') ?>

<div class="redes form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($rede, ['enctype' => 'multipart/form-data']) ?>
    <fieldset>
        <legend><?= __('Configurar Propaganda') ?></legend>
      

        <div class="col-lg-12">
            <label for="propaganda_link">Link para Propaganda*</label>
            <input type="text" name="propaganda_link" required="required" placeholder="Link para Propaganda..." id="propaganda_link" class="form-control" value="<?= $propaganda['propaganda_link'] ?>">
        </div>

        <div class="col-lg-12">
            <?= $this->Form->input(
                'propaganda_img',
                [
                    'type' => 'file',
                    'label' => 'Propaganda para Exibição*',
                    "id" => "propaganda_img",
                    "class" => "propaganda_img",
                    "accept" => ".png,.jpg",

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
                    <img src="" id="img-crop" class="img-crop" name="img_crop" />
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
                <button type="submit" class="btn btn-primary botao-confirmar">
                    <span class="fa fa-save"></span>
                    Salvar
                </button>
                <a onclick="history.go(-1); return false;" class="btn btn-danger botao-cancelar">
                    <span class="fa fa-window-close"></span>
                    Cancelar
                </a>
            </div>
        </div>
    </fieldset>

    <?= $this->Form->end() ?>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/redes/propaganda_form'); ?>
    <?= $this->Html->css("styles/redes/propaganda_form"); ?>
<?php else : ?>
    <?= $this->Html->script('scripts/redes/propaganda_form.min'); ?>
    <?= $this->Html->css("styles/redes/propaganda_form.min"); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>
<?= $this->fetch('css'); ?>