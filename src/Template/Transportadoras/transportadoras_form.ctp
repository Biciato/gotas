<?php

/**
 * @description Arquivo para formulário de transportadoras
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Transportadoras/transportadoras_form.ctp
 * @date        28/08/2017
 *
 */

use Cake\Core\Configure;

if (!isset($transportadoraPath))
    $transportadoraPath = '';
?>

<div class="transportadora">

    <div class="form-group row">
        <div class="col-lg-12">
            <legend><?= __('Dados de Transportadora') ?></legend>
        </div>
    </div>

    <?= $this->Form->hidden($transportadoraPath . 'id', ['id' => 'id']) ?>

    <div class="form-group row">
        <div class="col-lg-12">
            <span>Informe o CNPJ, se já existir, iremos trazer os dados previamente cadastrados</span>
        </div>
    </div>
    <div class="form-group row">

        <div class="col-lg-4">
            <span id="cnpj_validation" class="text-danger validation-message"></span>
            <label for="cnpj">CNPJ*</label>
            <input type="text"
                name="<?= sprintf( "%s%s", $transportadoraPath , "cnpj") ?>"
                id="<?= sprintf( "%s%s", $transportadoraPath , "cnpj") ?>"
                required="required"
                placeholder="CNPJ..."
                class="form-control cnpj"
                value="<?= $transportadora['cnpj']?>"
                >
        </div>

        <div class="col-lg-4">
            <label for="nome_fantasia">Nome Fantasia</label>
            <input type="text"
                name="<?= sprintf( "%s%s", $transportadoraPath , "nome_fantasia") ?>"
                id="<?= sprintf( "%s%s", $transportadoraPath , "nome_fantasia") ?>"
                placeholder="Nome Fantasia..."
                class="form-control"
                value="<?= $transportadora['nome_fantasia']?>"
                >
        </div>

        <div class="col-lg-4">

            <label for="razao_social">Razão Social*</label>
            <input type="text"
                name="<?= sprintf("%s%s", $transportadoraPath, "razao_social") ?>"
                id="<?= sprintf("%s%s", $transportadoraPath, "razao_social") ?>"
                required="required"
                placeholder="Razão Social..."
                class="form-control razao_social"
                value="<?= $transportadora['razao_social']?>"
                >
        </div>
    </div>


    <div class="form-group row">
        <div class="col-lg-3">
            <label for="cep">CEP</label>
            <input type="cep"
                name="<?= sprintf("%s%s", $transportadoraPath, "cep") ?>"
                id="<?= sprintf("%s%s", $transportadoraPath, "cep") ?>"
                placeholder="CEP..."
                class="form-control cep_transportadoras"
                value="<?= $transportadora['cep']?>"
                >
        </div>
        <div class="col-lg-4">

            <label for="endereco">Endereço</label>
            <input type="endereco"
                name="<?= sprintf("%s%s", $transportadoraPath, "endereco") ?>"
                id="<?= sprintf("%s%s", $transportadoraPath, "endereco") ?>"
                placeholder="Endereço..."
                class="form-control endereco_transportadoras"
                value="<?= $transportadora['endereco']?>"
                >
        </div>

        <div class="col-lg-2">
            <label for="endereco_numero">Número</label>
            <input type="endereco_numero"
                name="<?= sprintf("%s%s", $transportadoraPath, "endereco_numero") ?>"
                id="<?= sprintf("%s%s", $transportadoraPath, "endereco_numero") ?>"
                placeholder="Número..."
                class="form-control endereco_numero_transportadoras"
                value="<?= $transportadora['endereco_numero']?>"
                >
        </div>

        <div class="col-lg-3">
            <label for="endereco_complemento">Complemento</label>
            <input type="endereco_complemento"
                name="<?= sprintf("%s%s", $transportadoraPath, "endereco_complemento") ?>"
                id="<?= sprintf("%s%s", $transportadoraPath, "endereco_complemento") ?>"
                placeholder="Complemento..."
                class="form-control endereco_complemento_transportadoras"
                value="<?= $transportadora['endereco_complemento']?>"
                >
        </div>

    </div>

    <div class="form-group row">
        <div class="col-lg-3">
            <label for="bairro">Bairro</label>
            <input type="bairro"
                name="<?= sprintf("%s%s", $transportadoraPath, "bairro") ?>"
                id="<?= sprintf("%s%s", $transportadoraPath, "bairro") ?>"
                placeholder="Bairro..."
                class="form-control bairro_transportadoras"
                value="<?= $transportadora['bairro']?>"
                >
        </div>

        <div class="col-lg-3">
            <label for="municipio">Município</label>
            <input type="municipio"
                name="<?= sprintf("%s%s", $transportadoraPath, "municipio") ?>"
                id="<?= sprintf("%s%s", $transportadoraPath, "municipio") ?>"
                placeholder="Município..."
                class="form-control municipio_transportadoras"
                value="<?= $transportadora['municipio']?>"
                >
        </div>


        <div class="col-lg-3">
            <label for="estado">Estado</label>
            <?= $this->Form->input(
                $transportadoraPath . 'estado',
                [
                    'id' => 'estado',
                    'empty' => true,
                    'class' => 'estado_transportadoras',
                    'type' => 'select',
                    'label' => false,
                    'options' => $this->Address->getStatesBrazil(),
                ]
            ); ?>
        </div>

        <div class="col-lg-3">
            <label for="pais">País</label>
            <input type="pais"
                name="<?= sprintf("%s%s", $transportadoraPath, "pais") ?>"
                id="<?= sprintf("%s%s", $transportadoraPath, "pais") ?>"
                placeholder="País..."
                class="form-control pais_transportadoras"
                value="<?= $transportadora['pais']?>"
                >
        </div>
    </div>

    <div class="form-group row">
        <div class="col-lg-3">
            <label for="tel_fixo">Telefone Fixo</label>
            <input type="tel_fixo"
                name="<?= sprintf("%s%s", $transportadoraPath, "tel_fixo") ?>"
                id="<?= sprintf("%s%s", $transportadoraPath, "tel_fixo") ?>"
                placeholder="Telefone Fixo..."
                class="form-control"
                value="<?= $transportadora['tel_fixo']?>"
                >
        </div>

        <div class="col-lg-3">
            <label for="tel_celular">Telefone Celular</label>
            <input type="tel_celular"
                name="<?= sprintf("%s%s", $transportadoraPath, "tel_celular") ?>"
                id="<?= sprintf("%s%s", $transportadoraPath, "tel_celular") ?>"
                placeholder="Telefone Celular..."
                class="form-control"
                value="<?= $transportadora['tel_celular']?>"
                >
        </div>
    </div>
</div>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/transportadoras/transportadoras_form') ?>
    <?= $this->Html->css('styles/transportadoras/add') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/transportadoras/transportadoras_form.min') ?>
    <?= $this->Html->css('styles/transportadoras/add.min') ?>
<?php endif; ?>


<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
