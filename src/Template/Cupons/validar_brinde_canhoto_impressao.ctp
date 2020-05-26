<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/resgate_cupom_canhoto_impressao.ctp
 * @date     22/04/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>


<div class="impressao-resgate-cupom-canhoto-impressao">

    <div class="print_area">

        <div style="display: inline-flex;" class="header-cupom" >

                <?= $this->Html->image(
                    'icons/rti_cupom.png',
                    [
                        'class' => 'logo-rti-brinde'
                    ]
                ) ?>

                <div class="pull-right">
                    <?= $this->Html->tag('span', 'Data emissão: ', ['class' => 'pull-right']) ?>
                    <br />
                    <?= $this->Html->tag('span', isset($data_impressao) ? $data_impressao : null, ['id' => 'print_data_emissao', 'class' => 'print_data_emissao']) ?>
            </div>
        </div>

        <p class="text-center product">
            <?= $this->Html->tag('span', 'CANHOTO DE VALIDAÇÃO DE BRINDES'); ?>
        </p>

        <!-- Usuário Final -->
        <p>
            <center>
                <span>Cliente: </span>
                <span class="usuario-final"></span>
            </center>
        </p>

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


        <p class="text-center contact">

            <?= $this->Html->tag('span', 'contato@rtisolutions.com.br'); ?>
            <br />
            <?= $this->Html->tag('span', 'Telefone: (31) 3037 8592'); ?>

        </p>

    </div>
</div>

<?php if (Configure::read('debug')) : ?>
    <?= $this->Html->css('styles/cupons/validar_brinde_canhoto_impressao') ?>
<?php else : ?>
    <?= $this->Html->css('styles/cupons/validar_brinde_canhoto_impressao') ?>
<?php endif; ?>

<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>
