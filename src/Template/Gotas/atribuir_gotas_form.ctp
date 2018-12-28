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


        <div class="col-lg-12 group-video-capture-gotas video-gotas-capture-container">
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
    <?= $this->element('../Gotas/gotas_input_form_sem_ocr') ?>
    </div>


<?php if (Configure::read('debug')) : ?>
    <?= $this->Html->css('styles/gotas/atribuir_gotas_form') ?>
    <?= $this->Html->script('scripts/gotas/atribuir_gotas_form') ?>
<?php else : ?>
    <?= $this->Html->css('styles/gotas/atribuir_gotas_form.min') ?>
    <?= $this->Html->script('scripts/gotas/atribuir_gotas_form.min') ?>
<?php endif; ?>

<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>
