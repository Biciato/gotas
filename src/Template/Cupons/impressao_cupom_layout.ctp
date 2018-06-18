<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/impressao_cupom_layout.ctp
 * @date     20/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<div class="impressao-cupom-comum">

	<div class="print_area">

		<div style="display: inline-flex;" class="header-cupom" >

            <?= $this->Html->image('icons/rti_cupom.png',[ 'class' => 'logo-rti-brinde' ]) ?>

            <div class="pull-right">
                <?= $this->Html->tag('span', 'Data emissão: ', ['class' => 'pull-right']) ?>
                <br />
                <?= $this->Html->tag('span', isset($data_impressao) ? $data_impressao : null, ['id' => 'print_data_emissao']) ?>
            </div>
        </div>

		<p class="text-center product">
			<?= $this->Html->tag('span', 'BRINDES'); ?>
		</p>

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

		<p>
			<div class="text-center">
				<?= __("Código de leitura para resgatar produto") ?>
			</div>


			<div text="<?= isset($cupom_emitido) ? $cupom_emitido : null ?>" class="hidden cupom_emitido"><?= isset($cupom_emitido) ? $cupom_emitido : null ?> </div>
			<div class="print_region">
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

<?php if (Configure::read('debug')) : ?>
		<?= $this->Html->css('styles/cupons/impressao_cupom_layout') ?>
		<?= $this->Html->script('scripts/cupons/impressao_cupom_layout') ?>

<?php else : ?>
		<?= $this->Html->css('styles/cupons/impressao_cupom_layout.min') ?>
		<?= $this->Html->script('scripts/cupons/impressao_cupom_layout.min') ?>

<?php endif; ?>

<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>
