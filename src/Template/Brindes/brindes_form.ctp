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
        <label for="tipos_brindes_redes_id">Tipo de Brinde*</label>
        <?= $this->Form->input(
            'tipos_brindes_redes_id',
            [
                "type" => "select",
                "id" => "tipos_brindes_redes_id",
                "label" => false,
                "required" => true,
                "autofocus",
                "empty" => true,
                "options" => $tiposBrindesCliente
            ]
        ) ?>
    </div>

    <div class="col-lg-4">
        <label for="nome">Nome do Brinde*</label>
        <input type="text"
            name="nome"
            required="required"
            placeholder="Nome do Brinde..."
            id="nome"
            class="form-control"
            value="<?= $brinde['nome']?>">
    </div>

    <div class="col-lg-4">
        <label for="nome">Tempo de Uso (minutos)*</label>
        <input type="number"
            name="tempo_uso_brinde"
            id="tempo_uso_brinde"
            required="required"
            placeholder="Tempo de Uso (minutos)..."
            class="form-control"
            min="0"
            max="20"
            title="Para Brindes que funcionam por tempo, informe valor em minutos"
            value="<?= $brinde['tempo_uso_brinde']?>">
    </div>
</div>


<div class="form-group row">
    <div class="col-lg-6">
        <input type="checkbox"
            name="brinde_isento"
            id="brinde_isento"
            value="<?= $brinde['brinde_isento']?>">
        <label for="brinde_isento">
            Brinde Isento?
        </label>
    </div>

    <div class="col-lg-6">
        <input type="checkbox"
            name="brinde_desconto"
            id="brinde_desconto"
            value="<?= $brinde['brinde_desconto']?>" />
        <label for="brinde_isento">
            Brinde com Desconto?
        </label>
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-6">
        <input type="checkbox"
            name="tipo_venda_gotas"
            id="tipo_venda_gotas"
            value="<?= $brinde['tipo_venda_gotas']?>" />
        <label for="tipo_venda_gotas">
            Brinde Vendido em Gotas?
        </label>
    </div>

    <div class="col-lg-6">
        <input type="checkbox"
            name="tipo_venda_reais"
            id="tipo_venda_reais"
            value="<?= $brinde['tipo_venda_reais']?>" />
        <label for="tipo_venda_reais">
            Brinde Vendido em Reais?
        </label>
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-6">
        <label for="preco_padrao">Preço Padrão em Gotas*</label>
        <input type="text"
            name="preco_padrao"
            required="required"
            placeholder="Preço Padrão em Gotas..."
            id="preco_padrao"
            class="form-control"
            value="<?= $brinde['preco_padrao']?>">
    </div>
    <div class="col-lg-6">
        <label for="valor_moeda_venda_padrao">Preço Padrão de Venda Avulsa (R$)*</label>
        <input type="text"
            name="valor_moeda_venda_padrao"
            required="required"
            placeholder="Preço Padrão de Venda Avulsa (R$)..."
            id="valor_moeda_venda_padrao"
            class="form-control"
            value="<?= $brinde['valor_moeda_venda_padrao']?>">
    </div>
</div>


<div class="form-group ">
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

<div class="form-group row ">
    <div class="col-lg-12 text-right">
        <button type="submit"
            class="btn btn-primary botao-confirmar"
            >
            <span class="fa fa-save"></span>
            Salvar
        </button>
        <a href="/brindes/brindes-minha-rede/"
            class="btn btn-danger botao-cancelar"
            >
            <span class="fa fa-window-close"></span>
            Cancelar
        </a>
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
