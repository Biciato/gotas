<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src\Template\Brindes\view.ctp
 *
 * @since     2019-04-23
 *
 * Arquivo que exibe informações do brinde selecionado
 */
use Cake\I18n\Number;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$titleBrindesIndex = "";
$title = "";
if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) {

    $titleBrindesIndex = "Configurações IHM";
    $title = sprintf("Informações do IHM %s", $brinde["nome"]);
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
    $this->Breadcrumbs->add('Detalhes da Rede', array('controller' => 'redes', 'action' => 'verDetalhes', $redesId));
    $this->Breadcrumbs->add('Detalhes da Unidade', array("controller" => "clientes", "action" => "verDetalhes", $clientesId), ['class' => 'active']);
    $this->Breadcrumbs->add($titleBrindesIndex, array("controller" => "brindes", "action" => "index", $clientesId), array());
} else {
    $titleBrindesIndex = "Cadastro de Brindes";
    $title = sprintf("Informações do Brinde %s", $brinde["nome"]);

    $this->Breadcrumbs->add($titleBrindesIndex, array("controller" => "brindes", "action" => "index", $clientesId), array());
}

$this->Breadcrumbs->add("Informações do Brinde", array(), array('class' => 'active'));

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element("../Brindes/left_menu", array("manage" => 1)) ?>
<div class="brindes form col-lg-9 col-md-10 columns content">
    <legend><?= $title ?></legend>
    <?= $this->Form->create($brinde) ?>
    <fieldset>
        <div class="form-group row">
            <div class="col-lg-6">
                <label for="nome">Nome*</label>
                <input type="text"
                    name="nome"
                    required="required"
                    readonly="readonly"
                    placeholder="Nome..."
                    id="nome"
                    class="form-control"
                    value="<?= $brinde['nome'] ?>">
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
                        "readonly" => "readonly",
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
                            "readonly" => $editMode == 1 ? 'readonly' : '',
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
                    <input type="number"
                        name="codigo_primario"
                        id="codigo_primario"
                        class="form-control codigo-primario"
                        required="false"
                        readonly="readonly"
                        min="1"
                        max="99"
                        titleBrindesIndex="Código Primario de Equipamento RTI"
                        placeHolder="Código Primario..."
                        value="<?= $brinde["codigo_primario"] ?>">
                </div>
                <div class="col-lg-4">
                    <label for="tempo_uso_brinde"><?= $textoCodigoSecundario ?></label>
                    <input type="number"
                        name="tempo_uso_brinde"
                        id="tempo_uso_brinde"
                        required="required"
                        readonly="readonly"
                        placeholder="Tempo de Uso (minutos)..."
                        class="form-control tempo-uso-brinde"
                        min="0"
                        max="20"
                        titleBrindesIndex="Para Brindes que funcionam por tempo, informe valor em minutos"
                        value="<?= $brinde['tempo_uso_brinde'] ?>">
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
                        "class" => "tipo-venda",
                        "readonly" => $editMode == 1 ? 'readonly' : '',
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
                    readonly="readonly"
                    value="<?= $brinde['preco_padrao'] ?>">
            </div>
            <div class="col-lg-3">
                <label for="valor_moeda_venda_padrao">Preço Padrão Venda Avulsa (R$)*</label>
                <input type="text"
                    name="valor_moeda_venda_padrao"
                    required="required"
                    readonly="readonly"
                    placeholder="Preço Padrão de Venda Avulsa (R$)..."
                    id="valor_moeda_venda_padrao"
                    class="form-control"
                    value="<?= Number::currency($brinde['valor_moeda_venda_padrao'], 2) ?>">
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


    </fieldset>

    <?= $this->Form->end() ?>
</div>
