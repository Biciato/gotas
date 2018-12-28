<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/novo_preco_form.ctp
 * @date     09/08/2017
 */
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\I18n\Number;
use App\Custom\RTI\DebugUtil;

?>

<div class="form-group row">
    <div class="col-lg-6">
        <legend>Preço em Gotas (Via troca de pontos)</legend>
        <div class="col-lg-12">
            <?= $this->Form->input(
                "preco",
                [
                    "label" => "Preço Atual: ",
                    "value" => $ultimoPrecoAutorizadoGotas["preco"],
                    "id" => "preco_atual",
                    "readonly" => true,
                    "type" => "text"
                ]
            );
            ?>
        </div>
        <div class="col-lg-12">
            <?= $this->Form->input(
                "preco",
                [
                    "type" => "text",
                    "label" => "Preço (em gotas):",
                    "id" => "preco",
                    "required" => true
                ]
            ); ?>
        </div>
    </div>
    <div class="col-lg-6">
        <legend>Preço em Moeda (Para venda avulsa)</legend>
        <div class="col-lg-12">
            <?= $this->Form->input(
                "preco_atual_moeda",
                [
                    "label" => "Preço Atual: ",
                    "value" => is_null($ultimoPrecoAutorizadoVendaAvulsa) ? 0 : $ultimoPrecoAutorizadoVendaAvulsa->valor_moeda_venda,
                    "id" => "preco_atual_moeda",
                    "readonly" => true
                ]
            );
            ?>
        </div>
        <div class="col-lg-12">
            <?= $this->Form->input(
                "valor_moeda_venda",
                [
                    "type" => "decimal",
                    "label" => "Preço (R$ / venda avulsa):",
                    "id" => "valor_moeda_venda",
                    "required" => true
                ]
            ); ?>
        </div>
    </div>
</div>


<?php if (Configure::read("debug") == true) : ?>
    <?= $this->Html->script("scripts/clientes_has_brindes_habilitados_preco/preco_brinde_form") ?>
<?php else : ?>
    <?= $this->Html->script("scripts/clientes_has_brindes_habilitados_preco/preco_brinde_form.min") ?>
<?php endif; ?>

<?= $this->fetch("script") ?>
