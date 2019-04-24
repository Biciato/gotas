<?php
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesEstoque/brindes_estoque_form.ctp
 * @date     09/08/2017
 */

use Cake\Core\Configure;


$required_tipo_operacao = is_null($required_tipo_operacao) ? true : $required_tipo_operacao;
$required_data = is_null($required_data) ? true : $required_data;

$brinde = isset($brinde) ? $brinde : null;


if (!is_null($brinde)) {
	?>

<div class="form-group row">
    <div class='col-lg-4'>
        <?= $this->Form->input('nome', ['readonly' => true, 'value' => $brinde->brinde->nome, 'label' => 'Brinde Selecionado']); ?>
    </div>
    <div class='col-lg-4'>
        <?= $this->Form->input('preco', ['readonly' => true, 'value' => is_null($brinde->brinde_habilitado_preco_atual) ? null :
        $this->Number->precision($brinde->brinde_habilitado_preco_atual->preco, 2), 'label' => 'Preço (em gotas):']); ?>
    </div>
    <div class='col-lg-4'>
        <?= $this->Form->input('estoque', ['readonly' => true, 'value' => $this->Number->precision($brinde->estoque[0], 2), 'label' => 'Estoque Atual:']); ?>
    </div>
</div>
<div class="form-group row">
    <div class='col-lg-12'>
        <label for="quantidade">Quantidade*</label>
        <input type="number"
            name="quantidade"
            min="0"
            max="10000000"
            step="null"
            required="required"
            placeholder="Quantidade..."
            id="quantidade"
            class="form-control"
            value="<?= $brindeEstoque['quantidade'] ?>"
            >
    </div>
</div>

<?php
} else {
	echo $this->Form->input('quantidade', ['type' => 'number', 'id' => 'quantidade', 'min' => 0, 'step' => null]);
	echo $this->Form->hidden('tipo_operacao', ['required' => $required_tipo_operacao]);
	echo $this->Form->hidden('data', ['required' => $required_data]);
}
?>
