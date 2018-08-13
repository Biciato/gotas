<?php

use Cake\Core\Configure;

/**
 * form_genero_brindes.ctp
 *
 * Form para input de dados de genero_brindes
 *
 * @category View
 * @package App\Template\GeneroBrindes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date 31/05/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.0.0
 *
 */
?>

<?= $this->Form->create($generoBrinde) ?>
<fieldset>
    <legend><?= __($title) ?></legend>
    <div class="form-group row">
        <div class="col-lg-12"><?= $this->Form->control('nome'); ?></div>
    </div>
    <div class="form-group row">
        <div class="col-lg-12">
            <?= $this->Form->control('equipamento_rti', ["label" => "Equipamento RTI?"]); ?>
            <?= $this->Form->control('brinde_necessidades_especiais', ["label" => "Para Pessoas de Necessidades Especiais?"]); ?>
            <?= $this->Form->control('habilitado', ["label" => "Gênero Habilitado para Uso?"]); ?>
            <?= $this->Form->control(
                'atribuir_automatico',
                [
                    "label" => "Atribuir automaticamente na criação de nova unidade de rede?",
                    "id" => "atribuir_automatico",
                    "class" => "atribuir-automatico"
                ]
            ); ?>
        </div>
    </div>
    <div class="form-group row">

        <h4>
            Se Atribuir Automático, informe os seguintes valores para configuração automática:
        </h4>
        <div class="col-lg-6">
            <?= $this->Form->control(
                "tipo_principal_codigo_brinde_default",
                [
                    "label" => "Tipo Principal Codigo Brinde Default",
                    "id" => "tipo_principal_codigo_brinde_default",
                    "class" => "tipo-principal-codigo-brinde-default",
                    "type" => "number",
                    "min" => 0,
                    "max" => 9,
                    "step" => 1
                ]
            ) ?>
        </div>
        <div class="col-lg-6">
            <?= $this->Form->control(
                "tipo_secundario_codigo_brinde_default",
                [
                    "label" => "Tipo Secundario Codigo Brinde Default",
                    "id" => "tipo_secundario_codigo_brinde_default",
                    "class" => "tipo-secundario-codigo-brinde-default",
                    "type" => "number",
                    "min" => 00,
                    "max" => 99,
                    "step" => 1
                ]
            ) ?>
        </div>
    </div>

</fieldset>
<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->button(
            __('{0} Salvar', $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
            [
                'class' => 'btn btn-primary save-button',
                'escape' => false
            ]
        ) ?>
    </div>
</div>
<?= $this->Form->end() ?>


<?php if (Configure::read("debug")) {

    echo $this->Html->script("scripts/genero_brindes/form_genero_brindes");
} else {
    echo $this->Html->script("scripts/genero_brindes/form_genero_brindes.min");
}
echo $this->fetch("script");
?>
