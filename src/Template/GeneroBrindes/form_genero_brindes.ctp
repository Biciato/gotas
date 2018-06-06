<?php

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

    <?= $this->Form->control('equipamento_rti', ["label" => "Equipamento RTI?"]); ?>
    <?= $this->Form->control('brinde_necessidades_especiais', ["label" => "Para Pessoas de Nec. Especiais?"]); ?>
    <?= $this->Form->control('habilitado', ["label" => "Gênero Habilitado para Atribuição?"]); ?>
    <?= $this->Form->control('atribuir_automatico', ["label" => "Atribuir automaticamente na criação de nova unidade de rede?"]); ?>
</fieldset>
<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->button(
            __('{0} Salvar', $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
            [
                'class' => 'btn btn-primary',
                'escape' => false
                ]
        ) ?>
    </div>
</div>
<?= $this->Form->end() ?>
