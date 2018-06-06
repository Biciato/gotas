<?php

/**
  * @var \App\View\AppView $this
  *
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Gotas/gotas_config_input_form.ctp
  * @date     06/08/2017
  */

  use Cake\Core\Configure;
?>

    <?= $this->Form->create($gota) ?>
        <fieldset>
            <legend>
                <?= __('Configurar Métrica de Gotas') ?>
            </legend>

            <div class="btn btn-default right-align call-modal-how-it-works" target-id="#explicacao-gotas" ><span class=" fa fa-question-circle-o"> Ajuda</span></div>
            <?= $this->Form->hidden('clientes_id');?>
				<?= $this->Form->input('nome_parametro', 
					[
						'label' => 'Nome do Parâmetro',
						'id' => 'nome_parametro',
						'class' => 'form-control',
						'title' => 'Nome do Parâmetro'
					]
				); ?>
                    <?= $this->Form->input('multiplicador_gota', 
	                          [
	                          	'step' => '0.01',
	                          	'label' => 'Multiplicador de Gotas',
	                          	'id' => 'multiplicador_gota',
	                          	'class' => 'form-control',
	                          	'title' => 'Multiplicador de Gota'
	                          ]);?>
        </fieldset>
        <?= $this->Form->button(__('{0} Salvar',
        $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
        [
            'class' => 'btn btn-primary',
            'escape' => false
        ]
    
    ) ?>
		<?= $this->Form->end() ?>


		<div class="modal-how-it-works-parent hidden" id="explicacao-gotas">
			<div class="modal-how-it-works-title">
			Como funciona:
			</div>
			<div class="modal-how-it-works-body">
				<h4>Nome do Parâmetro</h4>
				<span>Aqui deve ser colocado o nome do Parâmetro de conversão, isto é, o nome do combustível. <br /> <strong>Nota: </strong> Deve-se atentar que este nome deve ser exatamente o que consta no Cupom Fiscal para adquirir os dados via QR Code (Código de barras do Cupom Fiscal)</span>
				<h4>Multiplicador de Gotas:</h4>
				<span>Uma gota é a conversão de milhas do cliente quando ele abastece em sua rede. A cada 1 litro de combustível abastecido, ele irá armazenar a informação de gotas cadastrada aqui.</span>
				<br />
				<span><strong>Exemplo:</strong></span>
				<div class="list-group">
					<div class="list-group-item">Cadastrando como 1,00, se o mesmo abastecer 100 litros, irá armazenar 100 gotas.</div>
					<div class="list-group-item">Cadastrando como 0,80, se o mesmo abastecer 100 litros, irá armazenar 80 gotas.</div>
				</div>
			
			</div>
		</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/gotas/gotas_config_input_form') ?>
<?php else: ?> 
    <?= $this->Html->script('scripts/gotas/gotas_config_input_form.min') ?>
<?php endif; ?>

<?= $this->fetch('script')?>
