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

<div class="transportadora col-lg-12">
        
    <legend><?= __('Dados de Transportadora') ?></legend>
    
    
    <?= $this->Form->hidden($transportadoraPath . 'id', ['id' => 'id']) ?>
    
    <div class="col-lg-12">
        <span>Informe o CNPJ, se já existir, iremos trazer os dados previamente cadastrados</span>
    </div>
    <div class="col-lg-6">
        <span id="cnpj_validation" class="text-danger validation-message"></span>
        <?= $this->Form->control($transportadoraPath . 'cnpj', ['title' => 'teste', 'label' => 'CNPJ', 'id' => 'cnpj']); ?>
    </div>
    <div class="row"></div>
    <div class="col-lg-6">
   
    <?= $this->Form->control($transportadoraPath . 'nome_fantasia', ['id' => 'nome_fantasia']); ?>
    </div>

    <div class="col-lg-6">
    
    <?= $this->Form->control($transportadoraPath . 'razao_social', ['id' => 'razao_social']); ?>
    </div>

    <div class="col-lg-3">

        <?= $this->Form->control($transportadoraPath . 'cep', ['id' => 'cep', 'label' => 'CEP', 'class' => 'cep_transportadoras']); ?>
    </div>
    <div class="col-lg-4">
    
        <?= $this->Form->control($transportadoraPath . 'endereco', ['id' => 'endereco', 'label' => 'Endereço', 'class' => 'endereco_transportadoras']); ?>
    </div>

    <div class="col-lg-2">
    
        <?= $this->Form->control($transportadoraPath . 'endereco_numero', ['id' => 'numero', 'label' => 'Número', 'class' => 'endereco_numero_transportadoras']); ?>
    </div>
    
    <div class="col-lg-3">
    
        <?= $this->Form->control($transportadoraPath . 'endereco_complemento', ['id' => 'endereco_complemento', 'label' => 'Complemento', 'class' => 'endereco_complemento_transportadoras']); ?>
    </div>
    
    <div class="col-lg-3">
    
        <?= $this->Form->control($transportadoraPath . 'bairro', ['id' => 'bairro', 'class' => 'bairro_transportadoras']); ?>
    </div>

    <div class="col-lg-3">
    
    <?= $this->Form->control($transportadoraPath . 'municipio', ['id' => 'municipio', 'class' => 'municipio_transportadoras']); ?>
    </div>


    <div class="col-lg-3">
    
    <?= $this->Form->input(
        $transportadoraPath . 'estado',
        [
            'id' => 'estado',
            'empty' => true,
            'class' => 'estado_transportadoras',
            'type' => 'select',
            'options' => $this->Address->getStatesBrazil(),
        ]
    ); ?>
    </div>

    <div class="col-lg-3">

        <?= $this->Form->input($transportadoraPath . 'pais', ['id' => 'pais', 'class' => 'pais_transportadoras', ]) ?>
    </div>
    <div class="col-lg-3">
    
        <?= $this->Form->control($transportadoraPath . 'tel_fixo', ['id' => 'tel_fixo']); ?>
    </div>

    <div class="col-lg-3">
    
        <?= $this->Form->control($transportadoraPath . 'tel_celular', ['id' => 'tel_celular']); ?>
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