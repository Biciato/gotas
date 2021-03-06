<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/impressao_shower_layout.ctp
 * @date     20/08/2017
 */

use Cake\Core\Configure;
?>

<div class="impressao-cupom">

    <div class="print-area">

        <div style="display: inline-flex;">

            <!-- Imagem gota título esquerda -->
            <?= $this->Html->image('icons/rti_cupom.png', ['class' => 'logo-rti-shower']) ?>

            <!-- Texto descritivo título direita -->
            <!-- Se é banho, usa esta div -->
            <div class="is-cupom-shower">
                <div class="pull-right">
                    <?= $this->Html->tag('span', 'Impresso: ') ?>
                    <?= $this->Html->tag('span', '', ['id' => 'print_data_emissao']) ?>
                </div>
                <br />

                <div class="pull-right">
                    <?= $this->Html->tag('span', 'Banho de ') ?>
                    <?= $this->Html->tag('span', ' ', ['id' => 'rti_shower_minutos']) ?>
                    <?= $this->Html->tag('span', ' minutos') ?>
                </div>
                <br />
                <div class="pull-right">
                    <?= $this->Html->tag('span', 'Box ') ?>
                    <?= $this->Html->tag('span', '', ['id' => 'tipos_brinde_box']) ?>
                </div>
            </div>

            <!-- Se não é banho, usa esta div -->
            <div class="is-not-cupom-shower">
                <div class="pull-right">
                    <?= $this->Html->tag('span', 'Data emissão: ', ['class' => 'pull-right']) ?>
                    <br />
                    <?= $this->Html->tag('span', isset($data_impressao) ? $data_impressao : null, ['id' => 'print_data_emissao']) ?>
                    <br />
                    <?= "Validade: 24 horas" ?>
                </div>
            </div>
        </div>

        <!-- Se banho, usa este título -->
        <div class="is-cupom-shower text-center">
            <span>RTI SHOWER</span>
        </div>

        <!-- Se não é banho, usa este título -->
        <div class="is-not-cupom-shower text-center">
            <span>BRINDES</span>
        </div>

        <!-- <div class="text-center">
            <span>CÓDIGO PARA LEITURA</span>
        </div> -->

        <!-- Esta tabela só pode aparecer se o brinde não for banho -->

        <div class="is-not-cupom-shower">
            <table class="table table-bordered table-centered table-responsive table-condensed tabela-produtos">
                <thead>
                    <tr>
                        <td>Qtd.</td>
                        <td>Descricao.</td>
                        <td>Gotas</td>
                        <td>Reais</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($produtos)) : ?>
                        <?php foreach ($produtos as $key => $produto) : ?>
                            <tr>
                                <td>
                                    <?= $produto['qte'] ?>
                                </td>
                                <td>
                                    <?= $produto['nome'] ?>
                                </td>
                                <td>
                                    <?= $produto['valor_pago_gotas'] ?>
                                </td>
                                <td>
                                    <?= $produto['valor_pago_reais'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!--
        <p>
            <div class="print-barcode-ticket">
                <center>
                    <?= $this->Html->tag('span', '', ['id' => 'print_barcode_ticket']) ?>
                </center>
            </div>
        </p> -->

        <p>
            <span class="hidden cupom_emitido" id="cupom-emitido">
                <?= isset($cupom_emitido) ? $cupom_emitido : null ?>"
            </span>
            <div class="print-pdf417-ticket">
                <center>
                    <canvas id='canvas_origin'></canvas>
                    <div id='canvas_destination'></div>
                    <img id="canvas_img" src="" />
                </center>
            </div>
        </p>

        <div class="text-center" id="print-qrcode-ticket"></div>


        <div class="text-center saldo-gotas"><span>Saldo Disponível: </span><br /><span id="saldo-gotas"></span> <span> Gotas</span></div>

        <div class="text-center contact">

            <?= $this->Html->tag('span', 'contato@rtisolutions.com.br'); ?>
            <br />
            <?= $this->Html->tag('span', 'Telefone: (31) 3037 8592'); ?>

        </div>
    </div>

</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->css('styles/cupons/impressao_brinde_layout') ?>
    <?= $this->Html->script('scripts/cupons/impressao_brinde_layout') ?>
<?php else : ?>
    <?= $this->Html->css('styles/cupons/impressao_brinde_layout.min') ?>
    <?= $this->Html->script('scripts/cupons/impressao_brinde_layout.min') ?>
<?php endif; ?>

<?= $this->fetch('css') ?>
