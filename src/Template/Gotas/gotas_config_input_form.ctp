<?php

/**
 * @var \App\View\AppView $this
 *
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Gotas/gotas_config_input_form.ctp
 * @date     06/08/2017
 */

use Cake\Core\Configure;
?>

<?= $this->Form->create($gota) ?>
<fieldset>

    <div class="btn btn-default right-align pull-right call-modal-how-it-works" target-id="#explicacao-gotas">
        <span class=" fas fa-question-circle"> Ajuda</span>
    </div>
    <div class="form-group row">
        <legend>
            <?= __('Configurar Métrica de Gotas') ?>
        </legend>
    </div>

    <div class="form-group row">

        <div class="col-lg-4">
            <?= $this->Form->input(
                "clientes_id",
                array(
                    "label" => "Posto de Atendimento*",
                    "input" => "select",
                    "empty" => true,
                    "autofocus",
                    "options" => $unidades,
                    "value" => $unidadesId,
                    "required" => true
                )
            ); ?>
        </div>
        <div class="col-lg-4">
            <label for="nome_parametro">Nome do Parâmetro*</label>
            <input value="<?= $gota['nome_parametro'] ?>"
                type="text"
                class="form-control"
                name="nome_parametro"
                placeholder="Nome do Parâmetro..."
                id="nome_parametro"
                required
                />
        </div>
        <div class="col-lg-4">
            <label for="multiplicador_gota">Multiplicador de Gotas*</label>
            <input value="<?= $gota['multiplicador_gota'] ?>"
                type="text"
                class="form-control"
                name="multiplicador_gota"
                id="multiplicador_gota"
                placeholder="Multiplicador de Gotas..."
                maxlength="7"
                required
                />
                <!-- max="1000,00" -->

        </div>
    </div>
    <div class="col-lg-12 text-right">
        <button type="submit"
            class="btn btn-primary botao-confirmar">
            <i class="fa fa-save"></i>
            Salvar
        </button>

        <a href="/gotas/gotas-minha-rede/"
            class="btn btn-danger botao-cancelar">
            <span class="fa fa-window-close"></span>
            Cancelar
        </a>
    </div>

</fieldset>

<?= $this->Form->end() ?>


<div class="modal-how-it-works-parent hidden" id="explicacao-gotas">
    <div class="modal-how-it-works-title">
    Como funciona:
    </div>
    <div class="modal-how-it-works-body">
        <h4>Nome do Parâmetro</h4>
        <span>Aqui deve ser colocado o nome do Parâmetro de conversão, isto é, o nome do combustível. <br /> <strong>Nota: </strong> Deve-se atentar que este nome deve ser exatamente o que consta no Cupom Fiscal para adquirir os dados via QR Code (Código de barras do Cupom Fiscal)</span>
        <h4>Multiplicador de Gotas:</h4>
        <span>Uma gota é a conversão de milhas do cliente quando ele abastece em sua rede. A cada 1 litro de combustível abastecido, ele irá armazenar a informação de gotas cadastrada aqui.</span>
        <br />
        <span><strong>Exemplo:</strong></span>
        <div class="list-group">
            <div class="list-group-item">Cadastrando como 1,00, se o mesmo abastecer 100 litros, irá armazenar 100 gotas.</div>
            <div class="list-group-item">Cadastrando como 0,80, se o mesmo abastecer 100 litros, irá armazenar 80 gotas.</div>
        </div>

    </div>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/gotas/gotas_config_input_form') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/gotas/gotas_config_input_form.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
