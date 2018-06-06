<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/escolher_brinde.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;

use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Escolher Brinde', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
	['class' => 'breadcrumb']
);
?>

<?= $this->element('../Cupons/left_menu', ['mode' => 'escolher_brinde', 'controller' => 'pages', 'action' => 'display']) ?>

<div class="col-lg-9 col-md-10 columns">
	<legend><?= __("Escolha o tipo de Brinde à ser Emitido") ?></legend>
	
	<div class="col-lg-6">
		<center>
			<h3>Smart Shower</h3>

			<?php $banhoImg = $this->Html->image('products/rti_shower.jpg', ['alt' => 'Smart Shower', 'class' => 'btn', 'title' => 'Emissão de Cupom Smart Shower']); ?>

			<?= $this->Html->link($banhoImg, ['controller' => 'cupons', 'action' => 'brinde_shower'], ['escape' => false]) ?></div>
		</center>

<div class="col-lg-6">
	<center>
		<h3>Brindes Diversos</h3>

			<?php $brindesImg = $this->Html->image('products/gifts.jpg', ['alt' => 'Brindes', 'class' => 'btn', 'title' => 'Emissão de Cupom de Brinde Comum']); ?>
			
			<?= $this->Html->link($brindesImg, ['controller' => 'cupons', 'action' => 'brinde_comum'], ['escape' => false]) ?></div>
			
	</center>
</div>
</div>

<?php if (Configure::read('debug') == true) : ?>
	<?= $this->Html->css('styles/cupons/escolher_brinde') ?>
	
<?php else : ?> 
    <?= $this->Html->css('styles/cupons/escolher_brinde.min') ?>
<?php endif; ?>


<?= $this->fetch('css') ?>