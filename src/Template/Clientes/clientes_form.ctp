<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/clientes_form.ctp
 * @date     18/11/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

?>


<?= $this->Form->create($cliente) ?>
<fieldset>
    <legend><?= $title ?></legend>
    <div class="form-group row">

        <div class="col-lg-6">
            <label for="codigo_equipamento_rti">Código Equipamento RTI*</label>
            <input type="text" maxLength="3" title="Código que será utilizado para impressão de senhas" placeholder="Código Equipamento RTI..." required="required" name="codigo_equipamento_rti" id="codigo_equipamento_rti" class="form-control" autofocus="true" value="<?= $cliente["codigo_equipamento_rti"] ?>">
        </div>

        <div class="col-lg-6">
            <label for="tipo_unidade">Tipo Unidade*</label>
            <?= $this->Form->input(
                'tipo_unidade',
                array(
                    'type' => 'select',
                    'required',
                    'empty' => true,
                    'label' => false,
                    'options' => array(
                        '0' => 'Loja',
                        '1' => 'Posto'
                    )
                )
            ); ?>
        </div>

    </div>

    <div class="form-group row">
        <div class="col-lg-4">
            <label for="nome_fantasia">Nome Fantasia</label>
            <input type="text" name="nome_fantasia" id="nome_fantasia" class="form-control" placeholder="Nome Fantasia..." value="<?= $cliente['nome_fantasia'] ?>">
        </div>
        <div class="col-lg-4">
            <label for="razao_social">Razão Social*</label>
            <input type="text" name="razao_social" placeholder="Razão Social..." required="required" value="<?= $cliente["razao_social"] ?>" class="form-control">
        </div>
        <div class="col-lg-4">
            <label for="cnpj">CNPJ*</label>
            <input type="text" name="cnpj" value="<?= $cliente['cnpj'] ?>" id="cnpj" class="form-control" required="required" placeholder="CNPJ...">
        </div>
    </div>

    <div class="form-group row">
        <div class='col-lg-2'>
            <label for="cep">CEP*</label>
            <input type="text" name="cep" required="required" id="cep" class="cep form-control" title='CEP do local do cliente (loja / posto)' value="<?= $cliente["cep"] ?>" placeholder="CEP...">
        </div>

        <div class="col-lg-5">
            <label for="endereco">Endereço*</label>
            <input type="text" name="endereco" placeholder="Endereço..." id="endereco" class="form-control endereco" required="required" value="<?= $cliente['endereco'] ?>">
        </div>

        <div class="col-lg-2">
            <label for="endereco_numero">Número</label>
            <input type="text" name="endereco_numero" placeholder="Número..." id="endereco_numero" class="form-control endereco_numero" value="<?= $cliente['endereco_numero'] ?>">
        </div>

        <div class="col-lg-3">
            <label for="endereco_complemento">Complemento</label>
            <input type="text" name="endereco_complemento" id="endereco_complemento" placeholder="Complemento..." class="form-control" value="<?= $cliente['endereco_complemento'] ?>">
        </div>
    </div>

    <div class="form-group row">

        <div class="col-lg-3">
            <label for="bairro">Bairro</label>
            <input type="text" name="bairro" id="bairro" placeholder="Bairro..." class="form-control bairro" value="<?= $cliente['bairro'] ?>">
        </div>
        <div class="col-lg-3">
            <label for="municipio">Município</label>
            <input type="text" name="municipio" id="municipio" placeholder="Município..." class="form-control municipio" value="<?= $cliente['municipio'] ?>">
        </div>
        <div class="col-lg-3">
            <label for="estado">Estado*</label>
            <?= $this->Form->input(
                'estado',
                [
                    'type' => 'select',
                    'empty' => true,
                    'label' => false,
                    'required' => 'required',
                    'class' => 'estado',
                    'title' => 'Informe o estado para configurar a importação de dados da SEFAZ',
                    'options' => $this->Address->getStatesBrazil()
                ]
            ); ?>

        </div>
        <div class="col-lg-3">
            <label for="pais">País</label>
            <input type="text" name="pais" id="pais" placeholder="País..." class="form-control pais" value="<?= $cliente['pais'] ?>">
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
            <label for="tel_fixo">Telefone Fixo</label>
            <input type="text" name="tel_fixo" placeholder="Telefone Fixo..." maxLength="10" id="tel-fixo" class="form-control" value="<?= $cliente['tel_fixo'] ?>">
        </div>

        <div class="col-lg-4">
            <label for="tel_fax">Fax</label>
            <input type="text" name="tel_fax" id="tel-fax" maxLength="10" placeholder="Fax..." class="form-control" value="<?= $cliente['tel_fax'] ?>">
        </div>

        <div class="col-lg-4">
            <label for="tel_celular">Celular*</label>
            <input type="text" name="tel_celular" id="tel-celular" placeholder="Celular..." required="required" maxLength="12" class="form-control" value="<?= $cliente['tel_celular'] ?>">
        </div>
    </div>

    <legend>Configurações de Software Fiscal</legend>

    <div class="form-group row">
        <div class="col-lg-6">
            <label for="impressao_sw_linha_continua">Impressão Continua de SW Fiscal?</label>
            <?= $this->Form->input("impressao_sw_linha_continua", array(
                "type" => "select",
                "options" => [
                    1 => "Sim",
                    0 => "Não"
                ],
                "default" => 1,
                "value" => $cliente["impressao_sw_linha_continua"],
                "id" => "impressao_sw_linha_continua",
                "label" => false,
                "title" => "Sim => Sistema Fiscal imprime CF de uma vez / Não => Sistema Fiscal imprime CF Linha a Linha"
            )); ?>
        </div>
        <div class="col-lg-6">
            <label for="delimitador_nota_impressao">Delimitador de Impressão da Nota Fiscal</label>
            <input type="text" name="delimitador_nota_impressao" id="delimitador-nota-impressao" class="form-control" value="<?= $cliente['delimitador_nota_impressao']?>"/>
        </div>

    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            <label for="delimitador_nota_produtos_inicial">Delimitador Inicial de Produtos da Nota Fiscal </label>
            <input type="text" name="delimitador_nota_produtos_inicial" id="delimitador-nota-produtos-inicial" class="form-control" title="Conteúdo onde se inicia a lista de Produtos na NF"  value="<?= $cliente['delimitador_nota_produtos_inicial']?>"/>
        </div>

        <div class="col-lg-6">
            <label for="delimitador_nota_produtos_final">Delimitador Final de Produtos da Nota Fiscal </label>
            <input type="text" name="delimitador_nota_produtos_final" id="delimitador-nota-produtos-final" class="form-control" title="Conteúdo onde se finaliza a lista de Produtos na NF" value="<?= $cliente['delimitador_nota_produtos_final'] ?>"/>
        </div>
    </div>

    <legend>Quadro de Horários da Unidade</legend>

    <div class="form-group row">
        <div class="col-lg-6">
            <label for="quantidade_turnos">Quantidade de Turnos</label>
            <?= $this->Form->input("quantidade_turnos", array(
                "type" => "select",
                "options" => array(
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    6 => 6
                ),
                "value" => $cliente["quantidade_turnos"],
                "id" => "quantidade_turnos",
                "label" => false
            )); ?>
        </div>
        <div class="col-lg-6">
            <label for="inicio_turno">Primeiro Turno do Dia*</label>
            <input type="time" min="2" max="4" value="<?= $cliente["horario"] ?>" class="form-control" name="horario" required="required" id="horario" />
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
        <button type="submit" class="btn btn-primary botao-confirmar">
            <span class="fa fa-save"></span>
            Salvar
        </button>

        <a href="<?php echo sprintf("/redes/ver_detalhes/%s", $redesId) ?>" class="btn btn-danger"><i class="fa fa-window-close"></i> Cancelar</a>
    </div>

</div>

<?= $this->Form->end() ?>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/clientes/clientes_form'); ?>
<?php else : ?>
    <?= $this->Html->script('scripts/clientes/clientes_form.min'); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>
