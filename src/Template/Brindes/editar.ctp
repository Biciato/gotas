<?php
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/editar_brinde_rede.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\I18n\Number;

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$titleBrindesIndex = "";
$title = "";
$titleCurrentPage = "Editar Brinde";

if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) {

    $titleBrindesIndex = "Configurações de IHM";
    $title = "Cadastrar IHM";
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
    $this->Breadcrumbs->add('Detalhes da Rede', array('controller' => 'redes', 'action' => 'verDetalhes', $redesId));
    $this->Breadcrumbs->add('Detalhes da Unidade', array("controller" => "clientes", "action" => "verDetalhes", $clientesId), ['class' => 'active']);
    $this->Breadcrumbs->add($titleBrindesIndex, array("controller" => "brindes", "action" => "index", $clientesId), array());
} else if (in_array($usuarioLogado["tipo_perfil"], array(PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL))) {
    $title = "Cadastro de Brindes";
    $this->Breadcrumbs->add("Selecionar Unidade Para Configurar Brindes", array("controller" => "brindes", "action" => "escolherPostoConfigurarBrinde"));
    $this->Breadcrumbs->add($title, array("controller" => "brindes", "action" => "index", $clientesId), ['class' => 'active']);
} else {
    $title = "Cadastro de Brindes";
    $this->Breadcrumbs->add($title, array("controller" => "brindes", "action" => "index", $clientesId), ['class' => 'active']);
}

$this->Breadcrumbs->add($titleCurrentPage, array(), array('class' => 'active'));

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>

<nav class="col-lg-3 col-md-2 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Menu') ?></a></li>
    </ul>
</nav>
<div class="brindes form col-lg-9 col-md-10 columns content">
    <legend><?= $titleCurrentPage ?></legend>
    <?= $this->Form->create($brinde) ?>
    <fieldset>
    <?= $this->Form->hidden('clientes_id', ['value' => $brinde->clientes_id]); ?>
        <?= $this->Form->hidden("edit-mode", ["id" => null, "value" => $editMode]) ?>
        <div class="form-group row">
            <div class="col-lg-6">
                <label for="nome">Nome*</label>
                <input type="text" name="nome" required="required" placeholder="Nome..." id="nome" class="form-control" value="<?= $brinde['nome'] ?>">
            </div>

            <div class="col-lg-6">
                <label for="tipo_codigo_barras">Código de Barras*</label>
                <?= $this->Form->input(
                    "tipo_codigo_barras",
                    array(
                        "id" => "tipo_codigo_barras",
                        "value" => $brinde["tipo_codigo_barras"],
                        "required" => "required",
                        "class" => "tipo-codigo-barras",
                        "readonly" => $editMode == 1 ? 'readonly' : '',
                        "label" => false,
                        "selected" => TYPE_BARCODE_QRCODE,
                        "options" => array(
                            TYPE_BARCODE_CODE128 => TYPE_BARCODE_CODE128,
                            TYPE_BARCODE_PDF417 => TYPE_BARCODE_PDF417,
                            TYPE_BARCODE_QRCODE => TYPE_BARCODE_QRCODE,
                        )
                    )
                ); ?>
            </div>
        </div>

        <?php if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) : ?>
            <div class="form-group row">
                <div class="col-lg-4">
                    <label for="tipo_equipamento">Tipo de Equipamento*</label>
                    <?= $this->Form->input(
                        "tipo_equipamento",
                        array(
                            "id" => "tipo_equipamento",
                            "value" => !empty($brinde["tipo_equipamento"]) ? $brinde['tipo_equipamento'] : 0,
                            "required" => "required",
                            "class" => "tipo-equipamento",
                            // "readonly" => $editMode == 1 ? 'readonly' : '',
                            "label" => false,
                            "empty" => true,
                            "options" => array(
                                TYPE_EQUIPMENT_PRODUCT_SERVICES => TYPE_EQUIPMENT_PRODUCT_SERVICES,
                                TYPE_EQUIPMENT_RTI => TYPE_EQUIPMENT_RTI
                            )
                        )
                    ); ?>
                </div>
                <div class="col-lg-4">
                    <label for="codigo_primario">Código Primário*</label>
                    <input type="number" name="codigo_primario" id="codigo_primario" class="form-control codigo-primario" required="false" min="1" max="99" title="Código Primario de Equipamento RTI" placeHolder="Código Primario..." value="<?= $brinde["codigo_primario"] ?>">
                </div>
                <div class="col-lg-4">
                    <label for="tempo_uso_brinde"><?= $textoCodigoSecundario ?></label>
                    <input type="number" name="tempo_uso_brinde" id="tempo_uso_brinde" required="required" placeholder="Tempo de Uso (minutos)..." class="form-control tempo-uso-brinde" min="0" max="99" title="Para Brindes que funcionam por tempo, informe valor em minutos" value="<?= $brinde['tempo_uso_brinde'] ?>">
                </div>
            </div>
        <?php else : ?>
            <input type="hidden" name="tipo_equipamento" id="tipo_equipamento" value="<?php echo TYPE_EQUIPMENT_PRODUCT_SERVICES ?>">
        <?php endif; ?>

        <div class="form-group row">
            <div class="col-lg-2">
                <label for="ilimitado">Ilimitado*</label>
                <?= $this->Form->input(
                    "ilimitado",
                    array(
                        "id" => "ilimitado",
                        "value" => isset($brinde['ilimitado']) ? $brinde["ilimitado"] : null,
                        "required" => "required",
                        "class" => "ilimitado",
                        // "readonly" => $editMode == 1 ? 'readonly' : '',
                        "label" => false,
                        "options" => array(
                            1 => "Sim",
                            0 => "Não",
                        )
                    )
                ); ?>
            </div>
            <div class="col-lg-2">
                <label for="habilitado">Habilitado*</label>
                <?= $this->Form->input(
                    "habilitado",
                    array(
                        "id" => "habilitado",
                        "value" => $brinde['habilitado'],
                        "required" => "required",
                        "class" => "habilitado",
                        "readonly" => $editMode == 1 ? 'readonly' : '',
                        "label" => false,
                        "options" => array(
                            1 => "Sim",
                            0 => "Não",
                        )
                    )
                ); ?>
            </div>
            <div class="col-lg-2">
                <label for="tipo_venda">Tipo de Venda*</label>
                <?= $this->Form->input(
                    "tipo_venda",
                    array(
                        "id" => "tipo_venda",
                        "value" => $brinde['tipo_venda'],
                        "required" => "required",
                        "class" => "tipo-venda disabled",
                        // "disabled",
                        // "readonly" => $editMode == 1 ? 'readonly' : '',
                        "label" => false,
                        "empty" => true,
                        "options" => array(
                            TYPE_SELL_FREE_TEXT => TYPE_SELL_FREE_TEXT,
                            TYPE_SELL_DISCOUNT_TEXT => TYPE_SELL_DISCOUNT_TEXT,
                            TYPE_SELL_CURRENCY_OR_POINTS_TEXT => TYPE_SELL_CURRENCY_OR_POINTS_TEXT
                        )
                    )
                ); ?>
            </div>

            <div class="col-lg-3">
                <label for="preco_padrao">Preço Padrão Gotas*</label>
                <input type="text"
                    name="preco_padrao"
                    required="required"
                    placeholder="Preço Padrão em Gotas..."
                    id="preco_padrao"
                    class="form-control"
                    value="<?= $brinde['preco_padrao'] ?>">
            </div>
            <div class="col-lg-3">
                <label for="valor_moeda_venda_padrao">Preço Padrão Venda Avulsa (R$)*</label>
                <input type="text"
                    name="valor_moeda_venda_padrao"
                    required="required"
                    placeholder="Preço Padrão de Venda Avulsa (R$)..."
                    id="valor_moeda_venda_padrao"
                    class="form-control"
                    value="<?= $brinde['valor_moeda_venda_padrao'] ?>"
                    >
            </div>
                <!-- value="<?= Number::currency($brinde['valor_moeda_venda_padrao'], 2) ?>" -->
        </div>
        <?php if ($brinde["nome_img"]) : ?>
            <div class="form-group row">
                <div class="col-lg-12">
                    <label>Imagem Atual do Brinde</label>
                    <div><img src="<?= $imagemExibicao ?>"
                        alt="Imagem do Brinde"
                        class="imagem-brinde"
                        width="400px"
                        height="300px"></div>
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
                    <h5 style="font-weight: bold;">Imagem para Exibição:</h5>
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
                    <span class="fa fa-save">
                    </span>
                    Salvar
                </button>
                <a href="<?= sprintf("/brindes/index/%s", $clientesId)?>" class="btn btn-danger botao-cancelar">
                    <span class="fa fa-window-close"></span>
                    Cancelar
                </a>
            </div>
        </div>
    </fieldset>

    <?= $this->Form->end() ?>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/brindes/brindes_form') ?>
    <?= $this->Html->css("styles/brindes/brindes_form") ?>
<?php else : ?>
    <?= $this->Html->script('scripts/brindes/brindes_form.min') ?>
    <?= $this->Html->css("styles/brindes/brindes_form.min") ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>

