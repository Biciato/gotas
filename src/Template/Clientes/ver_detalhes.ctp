<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

$redesId = $cliente["redes_has_cliente"]["redes_id"];
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
$this->Breadcrumbs->add('Detalhes da Rede', ['controller' => 'Redes', 'action' => 'ver_detalhes', $cliente->redes_has_cliente->redes_id]);
$this->Breadcrumbs->add('Detalhes da Unidade', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);


$controller_voltar = null;
$action_voltar = null;

if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $controller_voltar = 'redes';
    $action_voltar = 'ver_detalhes';
    $id_voltar = $cliente->redes_has_cliente->redes_id;
} else {
    $controller_voltar = 'pages';
    $action_voltar = 'display';
}

?>

<?= $this->element(
    '../Clientes/left_menu',
    [
        'controller' => $controller_voltar,
        'action' => $action_voltar,
        'id' => $id_voltar,
        'view' => true,
        'configurations' => true
    ]
) ?>
<div class="clientes view col-lg-9 col-md-10">
    <legend>
        <?= h(__("{0} - {1}", $cliente->redes_has_cliente->rede->nome_rede, $cliente->nome_fantasia)) ?>
    </legend>

    <fieldset>
        <div class="form-group row">
            <div class="col-lg-6">
                <label for="codigo_equipamento_rti">Código Equipamento RTI</label>
                <input type="text" class="form-control" title="Código que será utilizado para impressão de senhas" id="codigo_equipamento_rti" name="codigo_equipamento_rti" readonly="readonly" value="<?php echo $cliente['codigo_equipamento_rti']; ?>" />
            </div>
            <div class="col-lg-6">
                <label for="tipo_unidade">Tipo da Unidade</label>
                <input type="text" class="form-control" title="Tipo da Unidade" id="tipo_unidade" name="tipo_unidade" readonly="readonly" value="<?php echo $this->ClienteUtil->getTypeUnity($cliente['tipo_unidade']); ?>" />
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-4">
                <label for="nome_fantasia">Nome Fantasia</label>
                <input type="text" class="form-control" title="Nome Fantasia" id="nome_fantasia" name="nome_fantasia" readonly="readonly" value="<?php echo $cliente['nome_fantasia']; ?>" />
            </div>
            <div class="col-lg-4">
                <label for="razao_social">Razão Social</label>
                <input type="text" class="form-control" title="Razão Social" id="razao_social" name="razao_social" readonly="readonly" value="<?php echo $cliente['razao_social']; ?>" />
            </div>
            <div class="col-lg-4">
                <label for="cnpj">CNPJ</label>
                <input type="text" class="form-control" title="CNPJ" id="cnpj" name="cnpj" readonly="readonly" value="<?php echo $this->NumberFormat->formatNumberToCNPJ($cliente['cnpj']); ?>" />
            </div>
        </div>

        <div class="form-group row">
            <div class='col-lg-2'>
                <label for="cep">CEP</label>
                <input type="text" class="form-control" title="CEP do local do cliente" id="cep" name="cep" readonly="readonly" value="<?php echo $this->Address->formatCEP($cliente['cep']); ?>" />
            </div>

            <div class="col-lg-5">
                <label for="endereco">Endereço</label>
                <input type="text" class="form-control" title="Endereço" id="endereco" name="endereco" readonly="readonly" value="<?php echo $cliente['endereco'] ?>" />
            </div>

            <div class="col-lg-2">
                <label for="endereco_numero">Número</label>
                <input type="text" class="form-control" title="Número" id="endereco_numero" name="endereco_numero" readonly="readonly" value="<?php echo $cliente['endereco_numero'] ?>" />
            </div>

            <div class="col-lg-3">
                <label for="endereco_complemento">Complemento</label>
                <input type="text" class="form-control" title="Complemento" id="endereco_complemento" name="endereco_complemento" readonly="readonly" value="<?php echo $cliente['endereco_complemento'] ?>" />
            </div>
        </div>

        <div class="form-group row">

            <div class="col-lg-3">
                <label for="bairro">Bairro</label>
                <input type="text" class="form-control" title="Bairro" id="bairro" name="bairro" readonly="readonly" value="<?php echo $cliente['bairro'] ?>" />
            </div>
            <div class="col-lg-3">
                <label for="municipio">Município</label>
                <input type="text" class="form-control" title="Município" id="municipio" name="municipio" readonly="readonly" value="<?php echo $cliente['municipio'] ?>" />
            </div>
            <div class="col-lg-3">
                <label for="estado">Estado</label>
                <input type="text" class="form-control" title="Estado" id="estado" name="estado" readonly="readonly" value="<?php echo $this->Address->getStatesBrazil($cliente['estado']) ?>" />
            </div>
            <div class="col-lg-3">
                <label for="pais">País</label>
                <input type="text" class="form-control" title="País" id="pais" name="pais" readonly="readonly" value="<?php echo $cliente['pais'] ?>" />
            </div>
        </div>


        <div class="form-group row">
            <div class="col-lg-6">
                <label for="latitude">Latitude</label>
                <input type="text" class="form-control" title="Latitude" id="latitude" name="latitude" readonly="readonly" value="<?php echo $cliente['latitude'] ?>" />
            </div>
            <div class="col-lg-6">
                <label for="longitude">Longitude</label>
                <input type="text" class="form-control" title="Longitude" id="longitude" name="longitude" readonly="readonly" value="<?php echo $cliente['longitude'] ?>" />
            </div>

        </div>
        <div class="form-group row">
            <div class="col-lg-4">
                <label for="tel_fixo">Telefone Fixo</label>
                <input type="text" class="form-control" title="Telefone Fixo" id="tel_fixo" name="tel_fixo" readonly="readonly" value="<?php echo $this->Phone->formatPhone($cliente['tel_fixo']) ?>" />
            </div>

            <div class="col-lg-4">
                <label for="tel_fax">Telefone Fax</label>
                <input type="text" class="form-control" title="Telefone Fax" id="tel_fax" name="tel_fax" readonly="readonly" value="<?php echo $this->Phone->formatPhone($cliente['tel_fax']) ?>" />
            </div>

            <div class="col-lg-4">
                <label for="tel_celular">Telefone Celular</label>
                <input type="text" class="form-control" title="Telefone Celular" id="tel_celular" name="tel_celular" readonly="readonly" value="<?php echo $this->Phone->formatPhone($cliente['tel_celular']) ?>" />
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
                    "title" => "Sim => Sistema Fiscal imprime CF de uma vez / Não => Sistema Fiscal imprime CF Linha a Linha",
                    "disabled" => true
                )); ?>
            </div>
            <div class="col-lg-6">
                <label for="delimitador_nota_impressao">Delimitador de Impressão da Nota Fiscal</label>
                <input type="text" name="delimitador_nota_impressao" id="delimitador-nota-impressao" class="form-control" value="<?= $cliente['delimitador_nota_impressao']?>" disabled="disabled"/>
            </div>

        </div>
        <div class="form-group row">
            <div class="col-lg-6">
                <label for="delimitador_nota_produtos_inicial">Delimitador Inicial de Produtos da Nota Fiscal </label>
                <input type="text" name="delimitador_nota_produtos_inicial" id="delimitador-nota-produtos-inicial" class="form-control" title="Conteúdo onde se inicia a lista de Produtos na NF"  value="<?= $cliente['delimitador_nota_produtos_inicial']?>" disabled="disabled"/>
            </div>

            <div class="col-lg-6">
                <label for="delimitador_nota_produtos_final">Delimitador Final de Produtos da Nota Fiscal </label>
                <input type="text" name="delimitador_nota_produtos_final" id="delimitador-nota-produtos-final" class="form-control" title="Conteúdo onde se finaliza a lista de Produtos na NF" value="<?= $cliente['delimitador_nota_produtos_final'] ?>" disabled="disabled"/>
            </div>
        </div>


        <h4>Quadro de Horários</h4>
        <?php if (sizeof($cliente["clientes_has_quadro_horarios"]) > 0) : ?>
            <div class="form-group row">
                <?php for ($i = 0; $i < sizeof($cliente["clientes_has_quadro_horarios"]); $i++) : ?>
                    <div class="col-lg-4">
                        <label for="<?= sprintf('%s%s', 'quadro_horario_', $i + 1); ?>">
                            <?= sprintf("<strong>%s %s</strong>", "Turno", $i + 1) ?>
                        </label>
                        <input type="text" class="form-control" id="<?= sprintf('%s%s', 'quadro_horario_', $i + 1); ?>" name="<?= sprintf('%s%s', 'quadro_horario_', $i + 1); ?>" readonly value="<?= $cliente["clientes_has_quadro_horarios"][$i]["horario"]->format("H:i") ?>" />

                    </div>
                <?php endfor; ?>
            </div>
        <?php else : ?>
            <div class="col-lg-12 text-center text-uppercase"><span class="text-bold"> Não há dados de quadro de horários!</span></div>
        <?php endif; ?>
    </fieldset>

    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <a onclick="history.go(-1); return false;" class="btn btn-primary botao-cancelar"> <i class="fa fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</div>