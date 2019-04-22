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

                    <?= $this->Form->create('Post', ['url' => ['controller' => $controller, 'action' => $action, $id]]) ?>

                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label for="nome">Nome:</label>
                            <input
                                type="text"
                                name="nome"
                                class="input-control form-control"
                                value="<?php echo !empty($dataPost['nome']) ? $dataPost["nome"] : null ?>"
                                placeholder="Nome"
                            />


                        </div>

                        <div class="col-lg-2">
                            <?= $this->Form->input(
                                "ilimitado",
                                array(
                                    "id" => "ilimitado",
                                    "type" => "select",
                                    "empty" => "<Todos>",
                                    "value" => !empty($dataPost["ilimitado"]) ? $dataPost["ilimitado"] : null,
                                    "options" => Configure::read("yesNoArray")
                                )
                            ); ?>
                        </div>

                        <div class="col-lg-2">
                            <label for="habilitado">Habilitado:</label>
                            <?= $this->Form->input(
                                "habilitado",
                                array(
                                    "id" => "habilitado",
                                    "type" => "select",
                                    "label" => false,
                                    "class" => "input-control",
                                    "value" => !empty($dataPost["habilitado"]) ? $dataPost["habilitado"] : null,
                                    "empty" => "<Todos>",
                                    "options" => Configure::read("yesNoArray")
                                )
                            ); ?>
                        </div>

                        <div class="col-lg-2">
                            <label for="preco_padrao_min">Preço Mín. Gotas:</label>
                            <input
                                type="text"
                                name="preco_padrao_min"
                                class="input-control form-control"
                                id="preco_padrao_min"
                                placeholder="Preço Mínimo em Gotas"
                                value="<?php echo !empty($dataPost["preco_padrao_min"]) ? $dataPost["preco_padrao_min"] : null?>"

                            />
                        </div>

                        <div class="col-lg-2">
                            <label for="preco_padrao_max">Preço Máx. Gotas:</label>
                            <input
                                type="text"
                                name="preco_padrao_max"
                                class="input-control form-control"
                                id="preco_padrao_max"
                                placeholder="Preço Máximo em Gotas"
                                value="<?php echo !empty($dataPost["preco_padrao_max"]) ? $dataPost["preco_padrao_max"] : null?>"

                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label for="preco_reais_min">Preço Min. Reais:</label>
                            <input
                                type="text"
                                name="preco_reais_min"
                                id="preco_reais_min"
                                class="input-control form-control"
                                placeholder="Preço Mínimo em Reais"
                                value="<?php echo !empty($dataPost["preco_reais_min"]) ? $dataPost["preco_reais_min"] : null?>"

                                />
                        </div>

                        <div class="col-lg-3">
                            <label for="preco_reais_max">Preço max. Reais:</label>
                            <input
                                type="text"
                                name="preco_reais_max"
                                id="preco_reais_max"
                                class="input-control form-control"
                                placeholder="Preço Máximo em Reais"
                                value="<?php echo !empty($dataPost["preco_reais_max"]) ? $dataPost["preco_reais_max"] : null?>"
                            />
                        </div>

                        <div class="col-lg-3">
                            <label for="tipo_venda">Tipo de Venda:</label>
                            <?= $this->Form->input(
                                "tipo_venda",
                                array(
                                    "id" => "tipo_venda",
                                    "type" => "select",
                                    "label" => false,
                                    "class" => "input-control",
                                    "empty" => "<Todos>",
                                    "value" => !empty($dataPost["tipo_venda"]) ? $dataPost["tipo_venda"] : null,
                                    "options" =>
                                    array(
                                        TYPE_SELL_FREE_TEXT,
                                        TYPE_SELL_DISCOUNT_TEXT,
                                        TYPE_SELL_CURRENCY_OR_POINTS_TEXT
                                    )
                                )
                            ); ?>
                        </div>

                        <?php if ($tipoPerfil <= PROFILE_TYPE_ADMIN_DEVELOPER) : ?>
                            <div class="col-lg-3">
                                <label for="tipo_equipamento">Tipo de Equipamento:</label>
                                <?= $this->Form->input(
                                    "tipo_equipamento",
                                    array(
                                        "id" => "tipo_equipamento",
                                        "type" => "select",
                                        "label" => false,
                                        "class" => "input-control",
                                        "empty" => "<Todos>",
                                        "value" => !empty($dataPost["tipo_equipamento"]) ? $dataPost["tipo_equipamento"] : null,

                                        "options" => array(
                                            TYPE_EQUIPMENT_RTI,
                                            TYPE_EQUIPMENT_PRODUCT_SERVICES
                                        )
                                    )
                                );
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12 text-right">
                            <button type="submit" class="btn btn-primary botao-confirmar">
                                <span class="fa fa-search"></span>
                                Pesquisar
                            </button>

                            <button type="button" class="btn btn-danger reset-form">
                                <span class="fa fa-window-close"></span>
                                Limpar
                            </button>
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
