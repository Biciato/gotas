<?php

use Cake\Core\Configure;

/**
 * form_tipos_brindes_rede.ctp
 *
 * Form para input de dados de tipos de brindes da rede
 *
 * @category View
 * @package App\Template\TiposBrindesRedes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 31/05/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.0.0
 *
 */

$valorTipoBrinde = ($usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["AdminDeveloperProfileType"]) ? 1 : 0;

$arrayTiposBrindes = array(
    0 => "Produtos / Serviços",
    1 => "Equipamento RTI",
);

$arrayTiposBrindes = array($valorTipoBrinde => $arrayTiposBrindes[$valorTipoBrinde]);

?>


<?= $this->Form->create($tipoBrinde) ?>
<fieldset>
    <legend><?= __($title) ?></legend>
    <div class="form-group row">
        <div class="col-lg-12">
            <label for="nome">Nome*</label>
            <input type="text"
                name="nome"
                id="nome"
                required="required"
                placeholder="Nome..."
                class="form-control"
                value="<?= $tipoBrinde['nome']?>">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-12">
            <?= $this->Form->control('brinde_necessidades_especiais', ["label" => "Tipo de Brinde Para Pessoas de Necessidades Especiais?"]); ?>
            <?= $this->Form->control('habilitado', ["label" => "Habilitado para Uso? "]); ?>

            <?php if ($usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["AdminDeveloperProfileType"]) : ?>
                <?= $this->Form->control(
                    'atribuir_automatico',
                    [
                        "label" => "Atribuir automaticamente na criação de nova unidade de rede? ",
                        "id" => " atribuir_automatico ",
                        "class " => "atribuir-automatico "
                    ]
                ); ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($valorTipoBrinde == 1) : ?>

    <div class="form-group row">

        <div class="col-lg-6">
            <label for="tipo_principal_codigo_brinde_default">Tipo Principal Codigo Brinde</label>
            <input type="number"
                name="tipo_principal_codigo_brinde_default"
                id="tipo_principal_codigo_brinde_default"
                placeholder="Tipo Principal Codigo Brinde..."
                min="0"
                max="9"
                step="1"
                class="form-control tipo-principal-codigo-brinde-default"
                value="<?= $tipoBrinde["tipo_principal_codigo_brinde_default"]?>"
                >

        </div>
        <div class="col-lg-6">
            <label for="tipo_secundario_codigo_brinde_default">Tipo Secundário Codigo Brinde</label>
            <input type="number"
                name="tipo_secundario_codigo_brinde_default"
                id="tipo_secundario_codigo_brinde_default"
                placeholder="Tipo Secundário Codigo Brinde..."
                min="0"
                max="9"
                step="1"
                class="form-control tipo_secundario_codigo_brinde_default"
                value="<?= $tipoBrinde["tipo_secundario_codigo_brinde_default"]?>"
                >
        </div>
    </div>

    <?php endif; ?>

</fieldset>
<div class="col-lg-12 text-right">
        <button type="submit" class="btn btn-primary botao-confirmar"><span class="fa fa-save"></span> Salvar</button>
        <a href="/tipos-brindes-redes/configurar-tipos-brindes-rede/<?php echo $rede["id"] ?>" class="btn btn-danger botao-cancelar"><span class="fa fa-window-close"></span> Cancelar</a>

</div>
<?= $this->Form->end() ?>


<?php if (Configure::read("debug")) {
    echo $this->Html->script("scripts/tipos_brindes_redes/form_tipos_brindes_redes");
    echo $this->Html->css("styles/tiposBrindesRedes/form");
} else {
    echo $this->Html->script("scripts/tipos_brindes_redes/form_tipos_brindes_redes.min");
    echo $this->Html->css("styles/tiposBrindesRedes/form.min");
}
echo $this->fetch("script");
echo $this->fetch("css");
?>
