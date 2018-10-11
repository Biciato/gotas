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
            <div id="filter-coupons" class="panel-collapse collapse in">
                <div class="panel-body">

				<?= $this->Form->create('Post', ['url' => ['controller' => $controller, 'action' => $action]]) ?>

                    <div class="col-lg-2">
                    <?= $this->Form->input(
                        "nome",
                        array(
                            "id" => "nome",
                            "type" => "text",
                            "class" => "input-control",
                            "label" => "Nome"
                        )
                    );
                    ?>
                    </div>

                    <div class="col-lg-2">
                        <?= $this->Form->input(
                            "ilimitado",
                            array(
                                "id" => "ilimitado",
                                "type" => "select",
                                "empty" => "<Todos>",
                                "options" => Configure::read("yesNoArray")
                            )
                        ); ?>
                    </div>

                    <div class="col-lg-2">
                    <?= $this->Form->input("preco_padrao",
                    array(
                        "id" => "preco_padrao",
                        "type" => "text",
                        "class" => "input-control"
                    )
                    ); ?>
                    </div>
                    <div class="col-lg-2">
                    <?= $this->Form->input("valor_moeda_venda_padrao",
                    array(
                        "id" => "valor_moeda_venda_padrao",
                        "type" => "text",
                        "class" => "input-control"
                    )
                    ); ?>
                    </div>
                    <div class="col-lg-2">
                    <?= $this->Form->input("habilitado",
                    array(
                        "id" => "habilitado",
                        "type" => "select",
                        "label" => "Status",
                        "class" => "input-control",
                        "empty" => "<Todos>",
                        "options" => Configure::read("yesNoArray")
                    )
                    ); ?>
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
    <?= $this->Html->script('scripts/brindes/brindes_filtro_pesquisa_comum') ?>
	<?php else : ?>
    <?= $this->Html->script('scripts/brindes/brindes_filtro_pesquisa_comum.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
