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
        <div class="col-lg-12"><?= $this->Form->control('nome'); ?></div>
    </div>
    <div class="form-group row">
        <div class="col-lg-12">

            <!-- <?php echo $this->Form->input(
                    "equipamento_rti",
                    array(
                        "type" => "select",
                        "id" => "equipamento_rti",
                        "class" => "equipamento_rti",
                        "label" => "Tipo de Prestação de Serviços",
                        "options" => $arrayTiposBrindes,
                        "value" => $valorTipoBrinde,
                        "disabled" => true,
                        "default" => null,
                        "required" => true
                    )
                ); ?> -->

            <?= $this->Form->control('brinde_necessidades_especiais', ["label" => "Tipo de Brinde Para Pessoas de Necessidades Especiais ?"]); ?>
            <?= $this->Form->control('habilitado', ["label" => "Habilitado para Uso ? "]); ?>

            <?php if ($usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["AdminDeveloperProfileType"]) : ?>
                <?= $this->Form->control(
                    'atribuir_automatico',
                    [
                        "label" => "Atribuir automaticamente na criação de nova unidade de rede ? ",
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
            <?= $this->Form->control(
                "tipo_principal_codigo_brinde_default",
                [
                    "label" => "Tipo Principal Codigo Brinde",
                    "id" => "tipo_principal_codigo_brinde_default",
                    "class" => "tipo-principal-codigo-brinde-default",
                    "type" => "number",
                    "min" => 0,
                    "max" => 9,
                    "step" => 1,
                    "required" => true
                ]
            ) ?>
        </div>
        <div class="col-lg-6">
            <?= $this->Form->control(
                "tipo_secundario_codigo_brinde_default",
                [
                    "label" => "Tipo Secundario Codigo Brinde",
                    "id" => "tipo_secundario_codigo_brinde_default",
                    "class" => "tipo-secundario-codigo-brinde-default",
                    "type" => "number",
                    "min" => 00,
                    "max" => 99,
                    "step" => 1,
                    "required" => true,
                ]
            ) ?>
        </div>
    </div>

    <?php endif; ?> 

</fieldset>
<div class="form-group row">
    <div class="col-lg-2">
        <button type="submit" class="btn btn-primary btn-block botao-confirmar"><span class="fa fa-save"></span> Salvar</button>
    </div>
    <div class="col-lg-2">
        <a href="/tipos-brindes-redes/configurar-tipos-brindes-rede/<?php echo $rede["id"] ?>" class="btn btn-danger btn-block"><span class="fa fa-window-close"></span> Voltar</a>
    </div>
        
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
