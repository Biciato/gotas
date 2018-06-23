<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/form_emissao_brinde.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$show_breadcrumbs = isset($show_breadcrumbs) ? $show_breadcrumbs : true;

if ($show_breadcrumbs) {

    $this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
    $this->Breadcrumbs->add('Escolher Brinde', ['controller' => 'cupons', 'action' => 'escolher_brinde']);
    $this->Breadcrumbs->add('Emissão de Cupom Smart Shower', [], ['class' => 'active']);

    echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);
}

$showMenu = isset($showMenu) ? $showMenu : true;

$urlRedirectConfirmacao = empty($urlRedirectConfirmacao) ? array("controller" => "pages", "action" => "display") : $urlRedirectConfirmacao;

?>

<?php if ($showMenu) : ?>

    <?= $this->element(
        '../Cupons/left_menu',
        [
            'mode' => 'print',
            'controller' => 'Cupons',
            'action' => 'emissao_brinde'
        ]
    ) ?>

    <div class="col-lg-9 col-md-10 columns">

<?php else : ?>
    <div class="col-lg-12 col-md-12 columns">
<?php endif; ?>

    <div class="container-emissao-cupom">
        <legend><?= __("Emissão de Cupom") ?></legend>

        <?= $this->Form->create(); ?>
            <?php echo $this->element("../Usuarios/filtro_usuarios_ajax") ?>
            <!-- <div class="form-group">

                <div class="brinde user-query-region">

                    <h4>Selecione um cliente</h4>

                    <div class="col-lg-3">

                        <?= $this->Form->input('opcoes', [
                            'type' => 'select',
                            'id' => 'opcoes',
                            'class' => 'form-control col-lg-2 brinde opcoes',
                            'label' => 'Pesquisar Por',
                            'options' => [
                                'nome' => 'nome',
                                'cpf' => 'cpf',
                                'doc_estrangeiro' => 'documento estrangeiro',
                                'placa' => 'placa'
                            ],
                            'default' => 'placa'
                        ]) ?>
                    </div>

                    <div class="col-lg-7">
                        <?= $this->Form->input(
                            'parametro',
                            [
                                'id' => 'parametro-brinde',
                                'label' => 'Parâmetro',
                                'class' => 'form-control col-lg-5'
                            ]
                        ) ?>
                    </div>

                    <div class="col-lg-2 vertical-align">

                        <?= $this->Form->button(
                            __("{0} Pesquisar", '<i class="fa fa-search" aria-hidden="true"></i>'),
                            [
                                'class' => 'btn btn-primary btn-block',
                                'type' => 'button',
                                'id' => 'search_usuario_brinde_shower'
                            ]
                        ) ?>
                    </div>

                    <span class="text-danger validation-message" id="user_validation_message_brinde_shower"></span>

                </div>

                <div class="brinde user-result user-result-names" >
                    <div class="col-lg-12">
                        <table class="table table-striped table-hover" id="user-result-names">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Data de Nascimento</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>

                <div class="brinde user-result user-result-plates" >

                    <div id="vehicle" class="col-lg-12">
                        <h4>Veículo Encontrado</h4>


                        <div class="col-lg-3 col-md-3">
                            <?= $this->Form->input('placa', ['readonly' => true, 'label' => 'Placa', 'id' => 'veiculosPlaca']) ?>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <?= $this->Form->input('modelo', ['readonly' => true, 'label' => 'Modelo', 'id' => 'veiculosModelo']) ?>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <?= $this->Form->input('fabricante', ['readonly' => true, 'label' => 'Fabricante', 'id' => 'veiculosFabricante']) ?>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <?= $this->Form->input('veiculosAno', ['readonly' => true, 'label' => 'Ano', 'id' => 'veiculosAno']) ?>
                        </div>

                    </div>

                    <table class="table table-striped table-hover" id="user-result-plates">
                        <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">Data de Nascimento</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>

                <div class="form brinde user-result col-lg-12">

                    <?= $this->Html->tag(
                        'div',
                        ' Pesquisar cliente',
                        ['class' => 'col-lg-2 btn btn-primary fa fa-rotate-right brinde new-user-search', 'type' => 'button']
                    ) ?>

                    <h4>Cliente selecionado</h4>

                    <?= $this->Form->text(
                        'usuarios_id',
                        [
                            'id' => 'usuarios_id_brinde_shower',
                            'style' => 'display: none;',
                            'class' => 'usuarios_id_brinde_shower'
                        ]
                    ); ?>

                    <div class='col-lg-1'>
                        <?= $this->Form->label('Nome') ?>
                    </div>
                    <div class="col-lg-3 col-md-2">
                        <?= $this->Form->input(
                            'nome',
                            [
                                'readonly' => true,
                                'required' => false,
                                'label' => false,
                                'id' => 'usuariosNome',
                                'class' => 'usuariosNome'

                            ]
                        ) ?>
                    </div>

                    <div class='col-lg-2'>
                        <?= $this->Form->label('Data Nascimento') ?>
                    </div>

                    <div class="col-lg-2 col-md-1">

                        <?= $this->Form->input(
                            'data_nasc',
                            [
                                'readonly' => true,
                                'required' => false,
                                'label' => false,
                                'id' => 'usuariosDataNasc',
                                'class' => 'usuariosDataNasc'
                            ]
                        ) ?>
                    </div>

                    <div class='col-lg-1'>
                        <?= $this->Form->label('Total Pontos') ?>
                    </div>

                    <div class="col-lg-3 col-md-2">

                        <?= $this->Form->input(
                            'pontuacoes',
                            [
                                'readonly' => true,
                                'required' => false,
                                'label' => false,
                                'id' => 'usuariosPontuacoes',
                                'class' => 'usuariosPontuacoes'
                            ]
                        ) ?>
                    </div>

                </div>
            </div> -->

            <?= $this->Form->text('clientes_id', ['id' => 'clientes_id', 'value' => $cliente->id, 'style' => 'display: none;']); ?>

            <?= $this->element('../Brindes/brindes_filtro_ajax') ?>

            <div class="gifts-query-region">

                <div class="col-lg-12">
                    <?= $this->Form->button(
                        __('{0} Imprimir', $this->Html->tag('i', '', ['class' => 'fa fa-print'])),
                        [
                            'type' => 'button',
                            'id' => 'print_gift',
                            'escape' => false,
                            'class' => 'print-gift-shower'
                        ]
                    ) ?>

                    <?= $this->Html->tag('div', '', ['class' => 'text-danger validation-message', 'id' => 'print-validation']) ?>
                    <?= $this->Html->tag('/div') ?>
                </div>
            </div>

        <?= $this->Form->end(); ?>
    </div>

    <!-- Confirmação cupom -->
    <?php
        echo $this->element("../Cupons/confirmacao_emissao_cupom");
    ?>

    <!-- Confirmação canhoto -->
    <?php
        echo $this->element("../Cupons/confirmacao_canhoto");
    ?>
</div>

<?php
echo $this->element('../Cupons/impressao_brinde_layout');
?>

<?php
echo $this->element("../Cupons/impressao_canhoto_layout");
?>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/cupons/imprime_brinde') ?>
    <?= $this->Html->css('styles/cupons/imprime_brinde') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/cupons/imprime_brinde.min') ?>
    <?= $this->Html->css('styles/cupons/imprime_brinde.min') ?>
<?php endif; ?>


<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
