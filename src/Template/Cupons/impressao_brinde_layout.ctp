<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/impressao_shower_layout.ctp
 * @date     20/08/2017
 */

use Cake\Core\Configure;
?>

<div class="impressao-cupom-shower">

    <div class="print_area">

		<div style="display: inline-flex;">

            <!-- Imagem gota título esquerda -->
            <?= $this->Html->image('icons/rti_cupom.png', ['width' => '100%', 'class' => 'logo-rti-shower']) ?>

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
					<?= $this->Html->tag('span', '', ['id' => 'genero_box']) ?>
				</div>
			</div>

            <!-- Se não é banho, usa esta div -->
            <div class="is-not-cupom-shower">
                <div class="pull-right">
                    <?= $this->Html->tag('span', 'Data emissão: ', ['class' => 'pull-right']) ?>
                    <br />
                    <?= $this->Html->tag('span', isset($data_impressao) ? $data_impressao : null, ['id' => 'print_data_emissao']) ?>
                </div>
            </div>
		</div>

		<p class="text-center product">
            <!-- Se banho, usa este título -->
            <div class="is-cupom-shower">
			    <?= $this->Html->tag('span', 'RTI SHOWER'); ?>
            </div>

            <!-- Se não é banho, usa este título -->
            <div class="is-not-cupom-shower">
			    <?= $this->Html->tag('span', 'BRINDES'); ?>
            </div>
		</p>

        <!-- Esta tabela só pode aparecer se o brinde não for banho -->

        <div class="is-not-cupom-shower">
            <table class="table table-bordered table-centered table-responsive table-condensed tabela-produtos">
                <thead>
                    <tr>
                        <td>Qtd.</td>
                        <td>Descricao.</td>
                        <td>Valor</td>
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
                                    <?= $produto['valor_pago'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>


		<p class="is-cupom-shower">
			<div class="print_region">
			<center>
				<?= $this->Html->tag('span', '', ['id' => 'print_barcode_ticket']) ?>
			</center>
			</div>
		</p>

        <p class="is-not-cupom-shower">
			<div class="text-center">
				<?= __("Código de leitura para resgatar produto") ?>
			</div>


			<div text="<?= isset($cupom_emitido) ? $cupom_emitido : null ?>"
                class="hidden cupom_emitido">
                <?= isset($cupom_emitido) ? $cupom_emitido : null ?>
            </div>
			<div class="print-region">
				<center>
					<canvas id='canvas_origin'></canvas>

					<div id='canvas_destination'></div>

					<img id="canvas_img" >

				</center>
			</div>
		</p>

		<p class="text-center contact">

			<?= $this->Html->tag('span', 'contato@rtisolutions.com.br'); ?>
			<br />
			<?= $this->Html->tag('span', 'Telefone: (31) 3037 8592'); ?>

		</p>

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
