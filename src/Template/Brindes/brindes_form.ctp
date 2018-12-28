<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/brindes_form.ctp
 * @date     09/08/2017
 */

use Cake\Core\Configure;

?>

<?= $this->Form->hidden('clientes_id', ['value' => $brinde->clientes_id]); ?>
<?= $this->Form->hidden("edit-mode", ["id" => "edit_mode", "value" => $editMode]) ?>
<div class="form-group row">
    <div class="col-lg-4">
        <?= $this->Form->input(
            'tipos_brindes_redes_id',
            [
                "type" => "select",
                "id" => "tipos_brindes_redes_id",
                "label" => "Tipo de Brinde*",
                "empty" => "<Selecionar>",
                "options" => $tiposBrindesCliente
            ]
        ) ?>
    </div>

    <div class="col-lg-4">
        <?= $this->Form->input('nome', array(
            'id' => 'nome',
            "label" => "Nome do Brinde*"
        )); ?>
    </div>

    <div class="col-lg-4">

        <?= $this->Form->input(
            'tempo_uso_brinde',
            [
                'type' => 'number',
                'id' => 'tempo_uso_brinde',
                'label' => 'Tempo de Uso (minutos)*',
                'title' => "Para Brindes que funcionam por tempo, informe valor em minutos",
                'min' => 0,
                'max' => 20,
                'readonly' => false,
                'required' => false
            ]
        ) ?>
    </div>

    <div class="col-lg-6">
        <?= $this->Form->input(
            'preco_padrao',
            [
                'type' => 'text',
                'id' => 'preco_padrao',
                'label' => 'Preço Padrão em Gotas*'
            ]
        ); ?>
    </div>
    <div class="col-lg-6">
        <?= $this->Form->input(
            'valor_moeda_venda_padrao',
            [
                'type' => 'text',
                'id' => 'valor_moeda_venda_padrao',
                'label' => 'Preço Padrão de Venda Avulsa (R$)*',
            ]
        ); ?>
    </div>
</div>

<!-- Conferir o por que o botão está ficando desalinhado -->
<div class="form-group">
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

<?php

$exibirImagemAtual = isset($imagemOriginal) ? true : false;

if ($exibirImagemAtual) :
?>

<div class="form-group row">
    <div class="col-lg-12">
        <label>Imagem Atual do Brinde</label>
        <div><img src="<?= $imagemOriginal ?>" alt="Imagem do Brinde" class="imagem-brinde"></div>
    </div>
</div>

<?php endif; ?>

<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->input(
            'nome_img',
            [
                'type' => 'file',
                'label' => 'Imagem do Brinde'
            ]
        ) ?>
    </div>
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

<div class="form-group row">
    <div class="col-lg-2">
        <button type="submit" 
            class="btn btn-primary btn-block botao-confirmar"
            >
            <span class="fa fa-save"></span>
            Salvar
        </button>
    </div>
    <div class="col-lg-2">
        <a href="/brindes/brindes-minha-rede/" 
            class="btn btn-danger btn-block botao-cancelar"
            >
            <span class="fa fa-window-close"></span>
            Cancelar
        </a>
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