<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/impressao_canhoto_layout.ctp
 * @date     20/04/2018
 */

use Cake\Core\Configure;
?>

<div class="impressao-canhoto">

	<div class="print-area">

		<div>
			<center>
				<?= $this->Html->image('icons/rti_cupom.png', ["class" => "logo-rti-shower"]) ?>
			</center>
		</div>

		<div class="text-center product impressao-canhoto-titulo">

            <center>
                <div class="is-cupom-shower">
                    <span>CANHOTO SMART SHOWER</span>
                </div>
                <div class="is-not-cupom-shower">
                    <span>CANHOTO DE EMISSÃO DE BRINDES</span>
                </div>
            </center>
        </div>
        <br />
		<div>
			<center>
				Cliente:
				<?= $this->Html->tag('span', '', ['class' => 'usuarios-nome']) ?>
			</center>
        </div>

        <div>
			<center>

				<div>
					<?= $this->Html->tag('span', 'Impresso: ') ?>
					<?= $this->Html->tag('span', '', ['id' => 'print_data_emissao']) ?>
				</div>
			</center>
        </div>

        <div class="is-cupom-shower">
            <center>
                <div>
                    <?= $this->Html->tag('span', 'Banho de ') ?>
                    <?= $this->Html->tag('span', ' ', ['id' => 'rti_shower_minutos']) ?>
                    <?= $this->Html->tag('span', ' minutos') ?>
                </div>
            </center>
        </div>

        <div class="is-not-cupom-shower">
            <p>
            <table class="table table-bordered table-centered table-responsive table-condensed tabela-produtos">
                    <thead>
                        <tr>
                            <td>Qte.</td>
                            <td>Brinde</td>
                            <td>Valor </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </p>
        </div>

        <br />

		<p class="text-center contact">

			<?= $this->Html->tag('span', 'contato@rtisolutions.com.br'); ?>
			<br />
			<?= $this->Html->tag('span', 'Telefone: (31) 3037 8592'); ?>

		</p>

	</div>
</div>

<?php if (Configure::read('debug') == true) : ?>
		<?= $this->Html->css('styles/cupons/impressao_canhoto_layout') ?>
<?php else : ?>
		<?= $this->Html->css('styles/cupons/impressao_canhoto_layout.min') ?>
<?php endif; ?>

<?= $this->fetch('css') ?>
