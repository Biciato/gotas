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

			<!-- <div style="width: 50%;"> -->
			<!-- <div>
			</div> -->
				<?= $this->Html->image('icons/rti_cupom.png', ['width' => '100%',
				'class' => 'logo-rti-shower']) ?>
			<div>
				
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
		</div>

		<p class="text-center product">
			<?= $this->Html->tag('span', 'RTI SHOWER'); ?>
		</p>
		<p >
			<div class="print_region">
			<center>
				<?= $this->Html->tag('span', '', ['id' => 'print_barcode_ticket']) ?>
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
		<?= $this->Html->css('styles/cupons/impressao_shower_layout') ?>
<?php else : ?> 
		<?= $this->Html->css('styles/cupons/impressao_shower_layout.min') ?>
<?php endif; ?>

<?= $this->fetch('css') ?>