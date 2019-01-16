<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Gotas/atribuir_gotas_form.ctp
 * @date     06/08/2017
 */

use Cake\Core\Configure;


?>

    <div class="col-lg-9 col-md-8 group-video-capture-gotas">

    <legend>Atribuição de Gotas</legend>

        <?php if (isset($clientes_id)) : ?>

            <?= $this->Form->input('clientes_id', [
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'clientes_id',
                'value' => $clientes_id,
                'label' => false
            ]) ?>
            <?= $this->Form->input('clientesCNPJ', [
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'clientesCNPJ',
                'value' => $clientesCNPJ,
                'label' => false
            ]) ?>

           <?php endif; ?>

           <?= $this->Form->input(
                'id',
                [
                    'type' => 'hidden',
                    'id' => 'funcionarios_id',
                    'value' => $funcionario->id
                ]
            ) ?>

            <?= $this->Form->input(
                'estado_funcionario',
                [
                    'type' => 'hidden',
                    'id' => 'estado_funcionario',
                    'value' => $estado_funcionario
                ]
            ) ?>

        <?= $this->Form->input(
            'image_name',
            [
                'type' => 'hidden',
                'id' => 'image_name'
            ]
        ) ?>


        <div class="col-lg-12 group-video-capture-gotas video-capture-gotas-user-select-container">

            <?= $this->element('../Usuarios/filtro_usuarios_ajax') ?>

            <div class="col-lg-12 row-separator">

            </div>
            <div class="form-group user-result">

                <div class="col-lg-12">

                    <div class="col-lg-10">

                    </div>
                    <div class="col-lg-2">
                        <div>

                            <button class="btn btn-default btn-block disabled user-btn-proceed" type="button">
                                <div class="fa fa-2x fa-arrow-right">

                                </div>
                                <span>Prosseguir</span>
                            </button>
                        </div>

                    </div>

                    <!-- <div class="test-ajax btn btn-default">Test ajax</div> -->
                </div>
            </div>
        </div>

        <div class="col-lg-12 group-video-capture-gotas video-gotas-scanning-container">


            <div class="col-lg-12">
                <h4>Capturar Cupom Fiscal</h4>
            </div>
            <div class="col-lg-10">

                <?= $this->Form->input(
                    'qr_code',
                    [
                        // 'type' => 'text',
                        'type' => 'password',
                        'label' => 'QR Code',
                        'id' => 'qr_code_reader',
                        'class' => 'qr_code_reader',
                        // 'autocomplete' => 'off'
                        'autocomplete' => 'new-password'
                    ]
                ) ?>

            </div>

            <div class="col-lg-2 vertical-align">
                <div class="video-gotas-snapshot">
                    <div class="btn btn-primary take-scanner-snapshot ">
                        <div class="fa fa-2x fa-camera"> </div>
                        <span>Manual </span>
                    </div>
                </div>

            </div>



        </div>

        <div class="col-lg-12 group-video-capture-gotas video-receipt-capture-container">
            <div class="col-lg-12">
                <span>Capturar Cupom Fiscal com QR Code</span>
            </div>
            <div class="col-lg-10 ">
                <div class="video-cam-container-row">
                    <div class="video-cam-container">
                        <video id="video-receipt-capture" class="video-cam" autoplay="true"></video>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">

                <div class="video-receipt-snapshot">
                    <div class="btn btn-primary capture-receipt-snapshot">
                        <div class="fa fa-2x fa-camera"> </div>
                        <span>Tirar Foto </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 group-video-capture-gotas video-receipt-captured-region">
            <div class="col-lg-12">
                <span>Foto com QR Code Capturada</span>
            </div>
            <div class="col-lg-10">
                <div class="video-receipt-captured-row">
                    <div class="video-receipt-captured video-cam-container">
                        <canvas id="canvas-instascan-gotas" class="captured-cam" height="720" width="1280"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-2">

                <div class="buttons-region">

                    <div class="video-receipt-capture-again btn btn-default">

                        <div class="fa fa-2x fa-camera"></div>
                        <span>Recapturar</span>
                    </div>
                    <div class="store-receipt-image btn btn-primary ">
                        <div class="fa fa-2x fa-check"> </div>
                        <span>Processar</span>
                    </div>
                </div>

            </div>
        </div>


        <div class="col-lg-12 gotas-camera-manual-insert">

                <div class="form-group row">
                    <h4 class="col-lg-11">
                        <?= __('Inserção Manual de Gotas') ?>
                    </h4>

                    <div class="col-lg-1 btn btn-default right-align call-modal-how-it-works" target-id="#gotas-explicacao">
                        <span class=" fa fa-question-circle-o"> Ajuda</span>
                    </div>
                </div>



                <div class="form-group row">
                    <div class="col-lg-12">
                        <h4>Dados Fiscais</h4>
                    </div>

                    <div class="col-lg-12">
                    <?= $this->Form->input('chave_nfe', [
                        'type' => 'text',
                        'label' => 'Chave de Acesso',
                        'id' => 'chave_nfe',
                        'class' => 'form-control',
                        'title' => 'Chave para consulta da Nota Fiscal Eletrônica. Informe apenas números'
                    ]) ?>

                    <?= $this->Html->tag('span', "", [
                        'id' => 'chave_nfe_validation',
                        'class' => 'validation-message text-danger'
                    ]) ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-6">
                        <h4>Parâmetro à ser inserido</h4>

                        <?= $this->Form->input('list_parametros', [
                            'type' => 'select',
                            'id' => 'list_parametros',
                            'label' => 'Lista de Parâmetros Disponíveis'
                        ]) ?>

                        <input type="hidden" name="gotas_id_insert" class="form-control hidden" id="gotas_id_insert"/>
                        <label for="quantidade_input">Quantidade</label>
                        <input type="text" name="quantidade_input" class="form-control readonly" disabled="true" id="quantidade_input">

                        <div class="hidden">
                            <label for="quantidade_input">Preço</label>
                            <input type="text" name="quantidade_input" class="form-control readonly" disabled="true" id="quantidade_input">
                        </div>


                        <div class="col-lg-12 text-right">
                            <button class="btn btn-default add-parameter text-right"><span class="fa fa-plus-circle"> Adicionar</span></button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="gotas-products-table-container">
                            <h4>Lista de Gotas Inseridas</h4>
                            <table class="table table-hover table-responsive gotas-products-table">
                                <thead>
                                    <tr>
                                        <td class="row">Nome</td>
                                        <td>Quantidade</td>
                                        <td>Ações</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-12 text-right">
                            <button class="btn btn-primary save-receipt-button"><span class="fa fa-save"> Salvar</span></button>
                        </div>
                </div>

        </div>


        <div class="modal-how-it-works-parent hidden" id="gotas-explicacao">
            <div class="modal-how-it-works-title">
                Ajuda de Atribuição de Gotas:
            </div>
            <div class="modal-how-it-works-body" style="max-height: 50vh; overflow: auto;">
                <p>
                    <span>O estado onde o posto se encontra não possui importação automática pelo site da SEFAZ, será necessário informar manualmente. Informe cada compra conforme a nota fiscal.</span>
                    <br />
                    <span><strong>Atenção: </strong> Esta inserção será auditada pelo seu Administrador no futuro.</span>
                </p>
                <h4>Chave de Acesso</h4>
                <span>É a chave de acesso que consta em um Cupom Fiscal emitido após a compra do produto. É composto de 44 dígitos, se encontra na seção <strong>Emissão Normal</strong> de um Cupom Fiscal</span>
                <h4>Parâmetro à ser inserido</h4>
                <ul>
                        <li>
                            <h4>
                                Lista de Parâmetros Disponíveis
                            </h4>
                        </li>
                        <span>
                            São os produtos que o cliente adquire em seu ponto de venda. Selecione aquele(s) que consta(m) na nota.
                        </span>
                        <li>
                            <h4>
                                Quantidade
                            </h4>
                        </li>
                        <span>
                            É a quantidade abastecida em litros.
                        </span>
                        <div class="hidden">
                            <li>
                                <h4>
                                    Preço
                                </h4>
                            </li>
                            <span>Preço do combustível no momento do abastecimento.</span>
                        </div>

                </ul>



                <h3>Gotas à Gravar para Cliente</h3>
                <span>Esta tabela é a representação de todas as informações que serão enviadas ao servidor, ao apertar o botão <button class="btn btn-primary"><span class="fa fa-save"> Salvar</span></button>
                </span>
            </div>
        </div>

        </div>


        <div class="col-lg-12 group-video-capture-gotas video-gotas-capture-container hidden">
            <div class="col-lg-12">
                <span>Capturar Cupom Fiscal</span>
            </div>
            <div class="col-lg-10 ">
                <div class="video-cam-container-row">
                    <div class="video-cam-container">
                        <video id="video-gotas-capture" class="video-cam" autoplay="true"></video>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">

                <div class="video-gotas-snapshot">
                    <div class="btn btn-primary capture-gotas-snapshot">
                        <div class="fa fa-2x fa-camera"> </div>
                        <span>Tirar Foto </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 group-video-capture-gotas video-gotas-captured-region">
            <div class="col-lg-12">
                <span>Foto com Cupom Fiscal Capturada</span>
            </div>
            <div class="col-lg-10">
                <div class="video-gotas-captured-row">
                    <div class="video-gotas-captured video-cam-container">
                        <canvas id="canvas-cam-gotas" class="captured-cam" height="720" width="1280"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-2">

                <div class="buttons-region">

                    <div class="video-gotas-capture-again btn btn-default">

                        <div class="fa fa-2x fa-camera"></div>
                        <span>Recapturar</span>
                    </div>
                    <div>
                        <div class="store-gotas-image btn btn-primary ">
                            <div class="fa fa-2x fa-check"> </div>
                            <span>Processar</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div>
    <?= $this->element('../Gotas/gotas_input_form_com_ocr') ?>
    <?php
    // echo $this->element('../Gotas/gotas_input_form_sem_ocr');
    ?>
    </div>


<?php

$debug = Configure::read('debug');

$extensionJs = $debug ? ".js" : ".min.js";
$extensionCss = $debug ? ".css" : ".min.css";

echo $this->Html->css("styles/gotas/gotas_input_form_sem_ocr" . $extensionCss);
echo $this->Html->script("scripts/gotas/gotas_input_form_sem_ocr" . $extensionJs);

if (Configure::read('debug')) : ?>
    <?= $this->Html->css('styles/gotas/atribuir_gotas_form') ?>
    <?= $this->Html->script('scripts/gotas/atribuir_gotas_form') ?>
<?php else : ?>
    <?= $this->Html->css('styles/gotas/atribuir_gotas_form.min') ?>
    <?= $this->Html->script('scripts/gotas/atribuir_gotas_form.min') ?>
<?php endif; ?>

<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>
