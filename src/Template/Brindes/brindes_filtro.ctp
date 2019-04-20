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
            <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
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

                    <div class="form-group row">
                        <div class="col-lg-4">
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

                        <div class="col-lg-4">
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

                        <div class="col-lg-4">
                            <label for="preco_padrao">Preço em Gotas:</label>
                            <input type="text" name="preco_padrao" class="input-control form-control" id="preco_padrao" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label for="preco_reais">Preço em Reais:</label>
                            <input type="text" name="preco_reais" class="input-control form-control" id="preco_reais" />
                        </div>

                        <div class="col-lg-4">
                            <label for="habilitado">Habilitado:</label>
                            <?= $this->Form->input(
                                "habilitado",
                                array(
                                    "id" => "habilitado",
                                    "type" => "select",
                                    "label" => false,
                                    "class" => "input-control",
                                    "empty" => "<Todos>",
                                    "options" => Configure::read("yesNoArray")
                                )
                            ); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12 text-right">
                            <button type="submit" class="btn btn-primary botao-confirmar">
                                <span class="fa fa-search"></span>
                                Pesquisar
                            </button>

                            <a href="#" class="btn btn-danger reset-form"><i class="fa fa-window-close"></i> Limpar</a>
                        </div>

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
