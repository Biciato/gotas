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

        <div class="col-lg-12">
            <h4>Preço em Gotas (Troca de Pontos)</h4>
        </div>
        <div class="col-lg-6">
            <label for="preco_atual">Preço Atual</label>
            <input type="text"
                name="preco_atual"
                id="preco_atual"
                class="form-control"
                placeholder="Preço Atual..."
                readonly="true"
                value="<?= $ultimoPreco['preco']?>">

        </div>
        <div class="col-lg-6">
            <label for="preco">Preço (em gotas)</label>
            <input type="text"
                name="preco"
                id="preco"
                class="form-control"
                placeholder="Preço (em gotas)..."
                value="<?= $novoPreco['preco']?>">
        </div>

    </div>
    <div class="col-lg-6">
        <div class="col-lg-12">
            <h4>Preço em Moeda (Para venda avulsa)</h4>
        </div>
        <div class="col-lg-6">
            <label for="preco_atual_moeda">Preço Atual</label>
            <input type="text"
                name="preco_atual_moeda"
                id="preco_atual_moeda"
                class="form-control"
                placeholder="Preço Atual..."
                readonly="true"
                value="<?= $ultimoPreco['valor_moeda_venda']?>">
        </div>
        <div class="col-lg-6">
            <label for="valor_moeda_venda">Preço (R$ / venda avulsa)</label>
            <input type="text"
                name="valor_moeda_venda"
                id="valor_moeda_venda"
                class="form-control"
                placeholder="Preço (R$ / venda avulsa)..."
                value="<?= $novoPreco['valor_moeda_venda']?>">

        </div>
    </div>
</div>

<input type="hidden"
    name="tipo_venda"
    id="tipo_venda"
    value="<?= $tipoVenda?>">


<?php if (Configure::read("debug") == true) : ?>
    <?= $this->Html->script("scripts/brindesPrecos/preco_brinde_form") ?>
<?php else : ?>
    <?= $this->Html->script("scripts/brindesPrecos/preco_brinde_form.min") ?>
<?php endif; ?>

<?= $this->fetch("script") ?>
