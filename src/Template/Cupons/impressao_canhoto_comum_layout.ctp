<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/impressao_canhoto_comum_layout.ctp
 * @date     20/04/2018
 */

use Cake\Core\Configure;
?>

<div class="impressao-canhoto-comum">


    <div>
        <center>
            <?= $this->Html->image('icons/rti_cupom.png', ['width' => '50%']) ?>
        </center>
    </div>

    <p class="text-center product">
        <?= $this->Html->tag('span', 'CANHOTO'); ?>
        <?= $this->Html->tag('span', 'BRINDES'); ?>
    </p>
    <p>
        <center>
            Cliente:
            <?= $this->Html->tag('span', '', ['class' => 'usuarios-nome']) ?>
        </center>
    </p>
    <p>
        <center>

            <div>
                <?= $this->Html->tag('span', 'Impresso: ') ?>
                <?= $this->Html->tag('span', '', ['id' => 'print_data_emissao']) ?>
            </div>
        </center>
    </p>
    <p>
        <table class="produtos">
            <thead>
                <tr>
                    <td>Qte.</td>
                    <td>Brinde</td>
                    <td>Valor </td>
                </tr>
            </thead>
        </table>
    </p>

    <p class="text-center contact">

        <?= $this->Html->tag('span', 'contato@rtisolutions.com.br'); ?>
        <br />
        <?= $this->Html->tag('span', 'Telefone: (31) 3037 8592'); ?>

    </p>

</div>

<?php if (Configure::read('debug') == true) : ?>
		<?= $this->Html->css('styles/cupons/impressao_comum_layout_canhoto') ?>
<?php else : ?>
		<?= $this->Html->css('styles/cupons/impressao_comum_layout_canhoto.min') ?>
<?php endif; ?>

<?= $this->fetch('css') ?>
