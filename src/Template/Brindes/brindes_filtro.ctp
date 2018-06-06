<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/brindes_filtro.ctp
 * @date     09/08/2017
 */
use Cake\Core\Configure;
?>

<div class="form-group">


<div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading panel-heading-sm text-center"     
                data-toggle="collapse" 
                href="#collapse1"   
                data-target="#filter-coupons">
                <!-- <h4 class="panel-title"> -->
                    <div>
                        <span class="fa fa-search"></span>
                            Exibir / Ocultar Filtros
                    </div>
            
                <!-- </h4> -->
            </div>
            <div id="filter-coupons" class="panel-collapse collapse">
                <div class="panel-body">

				<?= $this->Form->create('Post', ['url' => ['controller' => $controller, 'action' => $action]]) ?>
				
					<div class="col-lg-8">
						<?= $this->Form->input(
							'parametro',
							[
								'id' => 'parametro',
								'class' => 'form-control col-lg-6',
								'label' => 'Parâmetro'
							]
					) ?> 
					</div>
					<div class="col-lg-2">
						
						<?= $this->Form->input('opcoes', [
							'type' => 'select',
							'id' => 'opcoes',
							'label' => 'Opções',
							'options' =>
								[
								'nome' => 'nome',
								'preco' => 'preco'
							],
							'class' => 'form-control col-lg-2'
						]) ?>
					</div>  

					<div class="col-lg-2 vertical-align">

						<?= $this->Form->button(
							"Pesquisar",
							[
								'class' => 'btn btn-primary btn-block',
								'id' => 'search_button'
							]
						) ?>
						</div>

				</div>
				<?= $this->Form->end() ?>

			</div>
		</div>
	</div>
</div>		

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/brindes/brindes_filtro') ?>
	<?php else : ?> 
    <?= $this->Html->script('scripts/brindes/brindes_filtro.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>