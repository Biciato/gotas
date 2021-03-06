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

    <input type="hidden" name="redes_id" id="redes-id" value="<?= $redes_id ?>" />
    <input type="hidden" name="id" id="funcionarios_id" value="<?= $funcionario['id'] ?>" />
    <input type="hidden" name="estado_funcionario" id="estado_funcionario" value="<?= $estado_funcionario ?>" />
    <input type="hidden" name="image_name" id="image_name" />

    <div class="col-lg-12 group-video-capture-gotas video-capture-gotas-user-select-container">

        <?= $this->element('../Usuarios/filtro_usuarios_ajax') ?>

        <input type="hidden" name="cria-cpf-pesquisa" id="cria-usuario-cpf-pesquisa" value="1" />

        <div class="col-lg-12 row-separator">

        </div>
        <div class="form-group row user-selected">
            <div class="col-lg-12">
                <div class="pull-right">
                    <button class="btn btn-default btn-block disabled user-btn-proceed" type="button">
                        <div class="fa fa-2x fa-arrow-right"></div>
                        <span>Prosseguir</span>
                    </button>
                </div>

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
                    'label' => 'QR Code*',
                    'placeholder' => 'QR Code...',
                    "required" => "required",
                    'id' => 'qr_code_reader',
                    'class' => 'qr_code_reader',
                    // 'autocomplete' => 'off'
                    'autocomplete' => 'new-password'
                ]
            ) ?>
        </div>

        <div class="col-lg-2 vertical-align">
            <div class="video-gotas-snapshot">
                <div class="btn btn-primary manual-input ">
                    <div class="fa fa-2x fa-edit"> </div>
                    <span>Manual </span>
                </div>
            </div>

        </div>



    </div>

    <div class="col-lg-12 group-video-capture-gotas video-receipt-capture-container">
        <div class="col-lg-12">
            <span>Capturar Cupom Fiscal</span>
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
            <span>Foto Capturada</span>
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
                <div class="store-receipt btn btn-primary ">
                    <div class="fa fa-2x fa-save"> </div>
                    <span>Gravar</span>
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
                <span class=" fas fa-question-circle"> Ajuda</span>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-6">
                <div class="form-group">
                    <h4>Dados Fiscais</h4>
                </div>

                <div class="form-group">
                    <?= $this->Form->input('chave_nfe', [
                        'type' => 'text',
                        'label' => 'Código',
                        'id' => 'chave_nfe',
                        'class' => 'form-control',
                        'title' => 'Código de consulta da Nota Fiscal Eletrônica'
                    ]) ?>

                    <span id="chave_nfe_validation" class="validation-message text-danger"></span>

                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <h4>Parâmetro à ser inserido</h4>

                        <?= $this->Form->input('list_parametros', [
                            'type' => 'select',
                            'id' => 'list_parametros',
                            'label' => 'Lista de Parâmetros Disponíveis*'
                        ]) ?>

                        <input type="hidden" name="gotas_id_insert" class="form-control hidden" id="gotas_id_insert" />
                        <label for="quantidade_input">Quantidade*</label>
                        <input type="text" name="quantidade_input" class="form-control readonly" disabled="true" id="quantidade_input">

                        <div class="hidden">
                            <label for="quantidade_input">Preço</label>
                            <input type="text" name="quantidade_input" class="form-control readonly" disabled="true" id="quantidade_input">
                        </div>


                        <div class="form-group row">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-default add-parameter text-right"><span class="fa fa-plus-circle"> Adicionar</span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">

                    <div class="gotas-products-table-container">
                        <h4>Lista de Gotas Inseridas</h4>
                        <div>
                            <label for="data_processamento">Data do Cupom</label>
                            <input type="text" name="data_processamento" id="data_processamento" class="form-control" required>
                            <input type="hidden" name="data_processamento_save" id="data_processamento_save" class="form-control hidden" required>
                        </div>
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
                </div>

                <div class="form-group row">
                    <div class="col-lg-12 text-right">
                        <button class="btn btn-default user-btn-proceed-picture-mg">
                            <div class="fa fa-arrow-right"></div> Prosseguir
                        </button>
                    </div>
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
                        <h5>
                            Lista de Parâmetros Disponíveis
                        </h5>
                    </li>
                    <span>
                        São os produtos que o cliente adquire em seu ponto de venda. Selecione aquele(s) que consta(m) na nota.
                    </span>
                    <li>
                        <h5>
                            Quantidade
                        </h5>
                    </li>
                    <span>
                        É a quantidade abastecida em litros.
                    </span>
                    <div class="hidden">
                        <li>
                            <h5>
                                Preço
                            </h5>
                        </li>
                        <span>Preço do combustível no momento do abastecimento.</span>
                    </div>
                </ul>
                <h4>Gotas à Gravar para Cliente</h4>
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
    <?php

    if ($estado_funcionario != "MG") {
        // echo $this->element('../Gotas/gotas_input_form_com_ocr');
    } else {
        echo $this->element('../Gotas/gotas_input_form_sem_ocr');
    }
    ?>
</div>


<?php

$extension = Configure::read("debug") ? ""  : ".min";
?>
<script src="/webroot/js/scripts/gotas/atribuir_gotas_form<?= $extension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/css/styles/gotas/atribuir_gotas_form<?= $extension ?>.css?<?= SYSTEM_VERSION ?>">
