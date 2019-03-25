<?php

/**
  * @var \App\View\AppView $this
  *
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Gotas/gotas_input_form_sem_ocr.ctp
  * @date     18/09/2017
  */

  use Cake\Core\Configure;
?>

    <!-- <div class="col-lg-9 gotas-camera-manual-insert">
        <div class="col-lg-12">
            <h4 class="col-lg-11">
                <?= __('Inserção Manual de Gotas') ?>
            </h4>

            <div class="col-lg-1 btn btn-default right-align call-modal-how-it-works" target-id="#gotas-explicacao">
                <span class=" fas fa-question-circle"> Ajuda</span>
            </div>
        </div>

        <p>
            <span>O estado onde o posto se encontra não possui importação automática pelo site da SEFAZ, será necessário informar manualmente. Informe cada compra conforme a nota fiscal.</span>
            <br />
            <span><strong>Atenção: </strong> Esta inserção será auditada pelo seu Administrador no futuro.</span>
        </p>

        <h4>Dados Fiscais</h4>

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


        <div>

            <div class="col-lg-6">
                <h4>Parâmetro à ser inserido</h4>

                <?= $this->Form->input('list_parametros', [
                        'type' => 'select',
                        'id' => 'list_parametros',
                        'label' => 'Lista de Parâmetros Disponíveis'
                    ]) ?>

                    <?= $this->Form->input('gotas_id_insert', [
                        'id' => 'gotas_id_insert',
                        'label' => false,
                        'class' => 'form-control hidden',
                    ])?>

                        <?= $this->Form->input('quantidade_input', [
                        'type' => 'text',
                        'label' => 'Quantidade',
                        'id' => 'quantidade_input',
                        'class' => 'form-control readonly',
                        'disabled' => true
                    ])?>

                    <button class="btn btn-default add-parameter"><span class="fa fa-plus-circle"> Adicionar</span></button>

            </div>

            <div class="col-lg-6">
                <div class="gotas-products-table-container">
                    <h4>Consumo à Gravar</h4>
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
                <button class="btn btn-primary save-receipt-button"><span class="fa fa-save"> Salvar</span></button>


            </div>
        </div>

    </div>


    <div class="modal-how-it-works-parent hidden" id="gotas-explicacao">
        <div class="modal-how-it-works-title">
            Como funciona:
        </div>
        <div class="modal-how-it-works-body" style="max-height: 50vh; overflow: auto;">
            <h3>Dados Fiscais:</h3>
            <h4>Chave de Acesso</h4>
            <span>É a chave de acesso que consta em um Cupom Fiscal emitido após a compra do produto. É composto de 44 dígitos, se encontra na seção <strong>Emissão Normal</strong> de um Cupom Fiscal</span>
            <h3>Parâmetro à ser inserido</h3>
            <h4>Lista de Parâmetros Disponíveis</h4>
            <span>São os produtos que o cliente adquire em seu ponto de venda. Selecione aquele(s) que consta(m) na nota.</span>

            <h4>Quantidade</h4>
            <span>É a quantidade abastecida em litros.</span>

            <h3>Consumo à gravar</h3>
            <span>Esta tabela é a representação de todas as informações que serão enviadas ao servidor, ao apertar o botão <button class="btn btn-primary"><span class="fa fa-save"> Salvar</span></button>
            </span>
        </div>
    </div> -->

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/gotas/gotas_input_form_sem_ocr') ?>
    <?= $this->Html->css('styles/gotas/gotas_input_form_sem_ocr')?>
<?php else: ?>
    <?= $this->Html->script('scripts/gotas/gotas_input_form_sem_ocr.min') ?>
    <?= $this->Html->css('styles/gotas/gotas_input_form_sem_ocr.min') ?>
<?php endif; ?>


<?= $this->fetch('script')?>
<?= $this->fetch('css')?>
