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
            <div class="col-lg-12 row">

                <div class="col-lg-4">
                    <?= $this->Form->input(
                        'codigo_rti_shower',
                        [
                            'id' => 'codigo_rti_shower',
                            'label' => 'Cód. Equip. para Smart Shower',
                            'title' => 'Código que será utilizado para impressão de senhas'
                        ]
                    ); ?>
                </div>

                <div class="col-lg-4">
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
        <div class="col-lg-5">
            <?= $this->Form->input('nome_fantasia'); ?>
        </div>
        <div class="col-lg-4">
            <?= $this->Form->input('razao_social', ['label' => 'Razão Social*']); ?>
        </div>
            <div class="col-lg-3">
            <?= $this->Form->input('cnpj', ['class' => 'form-control', 'id' => 'cnpj', 'label' => 'CNPJ*']); ?>
        </div>
            
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

        <div class="form-group">
            <div class="col-lg-6">
                <?= $this->Form->input('latitude', [
                    'label' => 'Latitude',
                    'placeHolder' => 'Latitude',
                    'id' => 'latitude',
                    'readonly' => true,
                    'title' => 'Valor de latitude adquirido pelo CEP'
                    ]); ?>
            </div>
            <div class="col-lg-6">
        
                <?= $this->Form->input('longitude', [
                    'label' => 'Longitude',
                    'placeHolder' => 'Longitude',
                    'id' => 'longitude',
                    'readonly' => true,
                    'title' => 'Valor de longitutde adquirido pelo CEP'
                ]); ?>
            </div>
        
        </div>

        <div class="col-lg-4">
            <?= $this->Form->input('tel_fixo'); ?>
        </div>
        
        <div class="col-lg-4">
            <?= $this->Form->input('tel_fax'); ?>
        </div>
        
        <div class="col-lg-4">
            <?= $this->Form->input('tel_celular'); ?>
        </div>
        
    </fieldset>
    <div class="col-lg-2">
        <?=
        $this->Form->button(
            __(
                '{0} Salvar',
                $this->Html->tag('i', '', ['class' => 'fa fa-save'])
            ),
            [
                'class' => 'btn-block', 'escape' => false
            ]
        )
        ?>
    </div>
    <?= $this->Form->end() ?>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/clientes/clientes_form'); ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/clientes/clientes_form.min'); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>