<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transportadora $transportadora
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Transportadoras', array("controller" => "transportadoras", "action" => "index"));
$this->Breadcrumbs->add('Detalhe de Transportadora', array(), array('class' => 'active'));

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);


?>

<?= $this->element('../Transportadoras/left_menu', ['controller' => 'transportadoras', 'action' => 'index', 'mode' => 'edit']) ?>
<div class="transportadoras view col-lg-9 col-md-8 columns content">
    <h3><?= h($transportadora["nome_fantasia"]) ?></h3>
    <div class="transportadora">

        <div class="form-group row">
            <div class="col-lg-12">
                <legend><?= __('Dados de Transportadora') ?></legend>
            </div>
        </div>

        <?= $this->Form->hidden('id', ['id' => 'id']) ?>

        <div class="form-group row">

            <div class="col-lg-4">
                <span id="cnpj_validation" class="text-danger validation-message"></span>
                <label for="cnpj">CNPJ*</label>
                <input type="text"
                    name="cnpj"
                    id="cnpj"
                    required="required"
                    placeholder="CNPJ..."
                    readonly
                    class="form-control cnpj"
                    value="<?= $transportadora['cnpj']?>"
                    >
            </div>

            <div class="col-lg-4">
                <label for="nome_fantasia">Nome Fantasia</label>
                <input type="text"
                    name="nome_fantasia"
                    id="nome_fantasia"
                    placeholder="Nome Fantasia..."
                    readonly
                    class="form-control nome_fantasia"
                    value="<?= $transportadora['nome_fantasia']?>"
                    >
            </div>

            <div class="col-lg-4">
                <label for="razao_social">Razão Social*</label>
                <input type="text"
                    name="razao_social"
                    id="razao_social"
                    required="required"
                    readonly
                    placeholder="Razão Social..."
                    class="form-control razao_social"
                    value="<?= $transportadora['razao_social']?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-3">
                <label for="cep">CEP</label>
                <input type="cep"
                    name="cep"
                    id="cep"
                    placeholder="CEP..."
                    readonly
                    class="form-control cep_transportadoras"
                    value="<?= $transportadora['cep']?>"
                    >
            </div>
            <div class="col-lg-4">

                <label for="endereco">Endereço</label>
                <input type="endereco"
                    name="endereco"
                    id="endereco"
                    placeholder="Endereço..."
                    readonly
                    class="form-control endereco_transportadoras"
                    value="<?= $transportadora['endereco']?>"
                    >
            </div>

            <div class="col-lg-2">
                <label for="endereco_numero">Número</label>
                <input type="endereco_numero"
                    name="endereco_numero"
                    id="endereco_numero"
                    placeholder="Número..."
                    readonly
                    class="form-control endereco_numero_transportadoras"
                    value="<?= $transportadora['endereco_numero']?>"
                    >
            </div>

            <div class="col-lg-3">
                <label for="endereco_complemento">Complemento</label>
                <input type="endereco_complemento"
                    name="endereco_complemento"
                    id="endereco_complemento"
                    placeholder="Complemento..."
                    readonly
                    class="form-control endereco_complemento_transportadoras"
                    value="<?= $transportadora['endereco_complemento']?>"
                    >
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-3">
                <label for="bairro">Bairro</label>
                <input type="bairro"
                    name="bairro"
                    id="bairro"
                    placeholder="Bairro..."
                    readonly
                    class="form-control bairro_transportadoras"
                    value="<?= $transportadora['bairro']?>"
                    >
            </div>

            <div class="col-lg-3">
                <label for="municipio">Município</label>
                <input type="municipio"
                    name="municipio"
                    id="municipio"
                    placeholder="Município..."
                    readonly
                    class="form-control municipio_transportadoras"
                    value="<?= $transportadora['municipio']?>"
                    >
            </div>

            <div class="col-lg-3">
                <label for="estado">Estado</label>
                <?= $this->Form->input(
                     'estado',
                    [
                        'id' => 'estado',
                        'empty' => true,
                        'class' => 'estado_transportadoras',
                        'type' => 'select',
                        'label' => false,
                        'readonly',
                        'value' => $transportadora['estado'],
                        'options' => $this->Address->getStatesBrazil(),
                    ]
                ); ?>
            </div>

            <div class="col-lg-3">
                <label for="pais">País</label>
                <input type="pais"
                    name="pais"
                    id="pais"
                    placeholder="País..."
                    readonly
                    class="form-control pais_transportadoras"
                    value="<?= $transportadora['pais']?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-3">
                <label for="tel_fixo">Telefone Fixo</label>
                <input type="tel_fixo"
                    name="tel_fixo"
                    id="tel_fixo"
                    placeholder="Telefone Fixo..."
                    class="form-control tel_fixo"
                    readonly
                    value="<?= $transportadora['tel_fixo']?>"
                    >
            </div>

            <div class="col-lg-3">
                <label for="tel_celular">Telefone Celular</label>
                <input type="tel_celular"
                    name="tel_celular"
                    id="tel_celular"
                    placeholder="Telefone Celular..."
                    readonly
                    class="form-control tel_celular"
                    value="<?= $transportadora['tel_celular']?>"
                    >
            </div>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <a href="/transportadoras" class="btn btn-primary botao-cancelar"> <i class="fa fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</div>
