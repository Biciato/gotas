<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/clientes_form.ctp
 * @date     18/11/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;


$redesId = !empty($cliente["rede_has_cliente"]["redes_id"]) ? $cliente["rede_has_cliente"]["redes_id"] : null;

?>


   <?= $this->Form->create($cliente) ?>
    <fieldset>
        <legend><?= $title ?></legend>
        <div class="form-group row">

            <div class="col-lg-6">
                <?= $this->Form->input(
                    'codigo_equipamento_rti',
                    [
                        'id' => 'codigo_equipamento_rti',
                        'label' => 'Código Equipamento RTI',
                        'title' => 'Código que será utilizado para impressão de senhas'
                    ]
                ); ?>
            </div>

            <div class="col-lg-6">
                <?= $this->Form->input(
                    'tipo_unidade',
                    [
                        'type' => 'select',
                        'options' =>
                            [
                            '' => '',
                            '0' => 'Loja',
                            '1' => 'Posto'
                        ]
                    ]
                ); ?>
            </div>

        </div>

        <div class="form-group row">
            <div class="col-lg-4">
                <?= $this->Form->input('nome_fantasia'); ?>
            </div>
            <div class="col-lg-4">
                <?= $this->Form->input('razao_social', ['label' => 'Razão Social*']); ?>
            </div>
            <div class="col-lg-4">
                <?= $this->Form->input('cnpj', ['class' => 'form-control', 'id' => 'cnpj', 'label' => 'CNPJ*']); ?>
            </div>
        </div>

        <div class="form-group row">
            <div class='col-lg-2'>
                <?= $this->Form->input(
                    'cep',
                    [
                        'label' => 'CEP*',
                        'id' => 'cep',
                        'class' => 'cep',
                        'title' => 'CEP do local do cliente. Digite-o para realizar a busca.'
                    ]
                ); ?>
            </div>

            <div class="col-lg-5" >
                <?= $this->Form->input('endereco', ['label' => 'Endereço*', 'class' => 'endereco']); ?>
            </div>

            <div class="col-lg-2">
                <?= $this->Form->input('endereco_numero', ['class' => 'form-control endereco_numero', 'type' => 'text', 'label' => 'Número']); ?>
            </div>

            <div class="col-lg-3">
                <?= $this->Form->input('endereco_complemento', ['class' => 'form-control', 'label' => 'Complemento']); ?>
            </div>
        </div>

        <div class="form-group row">

            <div class="col-lg-3">
                <?= $this->Form->input('bairro', ['label' => 'Bairro', 'class' => 'bairro']); ?>

            </div>
            <div class="col-lg-3">
                <?= $this->Form->input(
                    'municipio',
                    [
                        'class' => 'municipio'
                    ]
                ); ?>

            </div>
            <div class="col-lg-3">
                <?= $this->Form->input(
                    'estado',
                    [
                        'type' => 'select',
                        'empty' => true,
                        'label' => 'Estado*',
                        'class' => 'estado',
                        'title' => 'Informe o estado para configurar a importação de dados da SEFAZ',

                        'options' => $this->Address->getStatesBrazil()
                    ]
                ); ?>

            </div>
            <div class="col-lg-3">
                <?= $this->Form->input(
                    'pais',
                    [
                        'label' => 'País',
                        'id' => 'pais',
                        'class' => 'pais'
                    ]
                ); ?>

            </div>
        </div>


        <div class="form-group row">
            <div class="col-lg-6">
                <?= $this->Form->input('latitude', [
                    'label' => 'Latitude',
                    'placeHolder' => 'Latitude',
                    'id' => 'latitude',
                    'title' => 'Valor de latitude adquirido pelo CEP'
                ]); ?>
            </div>
            <div class="col-lg-6">

                <?= $this->Form->input('longitude', [
                    'label' => 'Longitude',
                    'placeHolder' => 'Longitude',
                    'id' => 'longitude',
                    'title' => 'Valor de longitutde adquirido pelo CEP'
                ]); ?>
            </div>

        </div>
        <div class="form-group row">
            <div class="col-lg-4">
            <?= $this->Form->input('tel_fixo'); ?>
            </div>

            <div class="col-lg-4">
            <?= $this->Form->input('tel_fax'); ?>
            </div>

            <div class="col-lg-4">
            <?= $this->Form->input('tel_celular'); ?>
            </div>
        </div>

    <legend>Quadro de Horários da Unidade</legend>

    <div class="form-group row">
        <div class="col-lg-6">
            <label for="quantidade_turnos">Quantidade de Turnos</label>
            <input type="number"
                min="2"
                max="6"
                step="1"
                value="<?= $cliente["quantidade_turnos"] ?> "
                class="form-control"
                name="quantidade_turnos"
                id="quantidade_turnos" />
        </div>
        <div class="col-lg-6">
        <label for="inicio_turno">Primeiro Turno do Dia</label>
            <input type="time"
                min="2"
                max="4"
                value="<?= $cliente["horario"] ?> "
                class="form-control"
                name="horario"
                id="horario" />
        </div>

    </div>

    <h4>Detalhamento Quadro de Horários</h4>

    <h5>Os horários serão cadastrados da seguinte forma:</h5>

    <div class="form-group row">
        <div class="col-lg-12">
            <span class="horariosContent"></span>
        </div>
    </div>
    </fieldset>
    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <button type="submit"
                class="btn btn-primary botao-confirmar">
                <span class="fa fa-save"></span>
                Salvar
            </button>

            <a href="<?php echo sprintf("/redes/ver_detalhes/%s", $redesId) ?>"  class="btn btn-danger"><i class="fa fa-window-close"></i> Cancelar</a>
        </div>

    </div>

    <?= $this->Form->end() ?>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/clientes/clientes_form'); ?>
<?php else : ?>
    <?= $this->Html->script('scripts/clientes/clientes_form.min'); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>
