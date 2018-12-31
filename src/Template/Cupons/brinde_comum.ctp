<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/brinde_comum.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$show_breadcrumbs = isset($show_breadcrumbs) ? $show_breadcrumbs : true;

if ($show_breadcrumbs) {
    $this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

    $this->Breadcrumbs->add('Escolher Brinde', ['controller' => 'cupons', 'action' => 'escolher_brinde']);

    $this->Breadcrumbs->add('Emissão de Brinde Comum', [], ['class' => 'active']);

    echo $this->Breadcrumbs->render(
        ['class' => 'breadcrumb']
    );
}

$showMenu = isset($showMenu) ? $showMenu : true;

?>

<?php if ($showMenu) : ?>

    <?= $this->element('../Cupons/left_menu', ['mode' => 'print', 'controller' => 'Cupons', 'action' => 'escolher_brinde']) ?>

<?php endif; ?>

    <div class="container-emissao-cupom-comum">

        <div class="col-lg-9 col-md-10 columns">

            <legend><?= __("Emissão de Cupom Brinde Comum") ?></legend>

            <div class="brinde-comum-container">

                <?= $this->Form->create(); ?>

                    <?= $this->Form->text('restrict_query', ['id' => 'restrict_query', 'value' => true, 'style' => 'display: none;']); ?>

                    <div class="form-group">

                        <div class="brinde-comum user-query-region">

                        <div class="col-lg-12">
                            <h4>Selecione um cliente</h4>
                        </div>

                            <div class="col-lg-3">

                                <?= $this->Form->input('opcoes', [
                                    'type' => 'select',
                                    'id' => 'opcoes',
                                    'class' => 'form-control col-lg-2 brinde-comum opcoes',
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
                                        'id' => 'parametro-brinde-comum',
                                        'label' => 'Parâmetro',
                                        'class' => 'form-control col-lg-5 parametro-brinde-comum'
                                    ]
                                ) ?>
                            </div>

                            <div class="col-lg-2 vertical-align">

                                <?= $this->Form->button(
                                    __("{0} Pesquisar", '<i class="fa fa-search" aria-hidden="true"></i>'),
                                    [
                                        'class' => 'btn btn-primary btn-block',
                                        'type' => 'button',
                                        'id' => 'search_usuario_brinde_comum'
                                    ]
                                ) ?>
                            </div>

                            <span class="text-danger validation-message" id="user_validation_message_brinde_comum"></span>

                        </div>

                        <div class="brinde-comum user-result user-result-names" >
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


                        <div class="brinde-comum user-result user-result-plates" >

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

                        <div class="form brinde-comum user-result col-lg-12">

                            <?= $this->Html->tag(
                                'div',
                                ' Pesquisar cliente',
                                ['class' => 'col-lg-2 btn btn-primary fa fa-rotate-right brinde-comum new-user-search', 'type' => 'button']
                            ) ?>

                            <h4>Cliente selecionado</h4>

                            <?= $this->Form->text(
                                'usuarios_id',
                                [
                                    'id' => 'usuarios_id_brinde_comum',
                                    'style' => 'display: none;',
                                    'class' => 'usuarios-id-brinde-comum'
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
                    </div>

                    <?= $this->Form->text('clientes_id', ['id' => 'clientes_id', 'value' => $cliente->id, 'style' => 'display: none;']); ?>

                    <div class="gifts-query-region">

                        <div class="col-lg-12">
                            <h4>Selecione um brinde</h4>
                        </div>

                        <div class="col-lg-6">
                            <?= $this->Form->input(
                                'lista_brindes_comum',
                                [
                                    'type' => 'select',
                                    'id' => 'lista_brindes_comum',
                                    'class' => 'form-control list-gifts-comum',
                                    'label' => 'Brindes',
                                    'required' => true
                                ]
                            ) ?>
                        </div>

                        <div class="col-lg-6">
                            <?= $this->Form->input('quantidade', [
                                'type' => 'number',
                                'readonly' => false,
                                'required' => true,
                                'label' => 'Quantidade',
                                'min' => 1,
                                'id' => 'quantidade',
                                'class' => 'quantidade-brindes',
                                'step' => 1.0,
                                'default' => 0,
                                'min' => 0
                            ]) ?>
                        </div>

                        <?= $this->Form->text(
                            'brindes_id',
                            [
                                'id' => 'brindes_id',
                                'style' => 'display: none;'
                            ]
                        ); ?>

                        <?= $this->Form->text(
                            'preco',
                            [
                                'readonly' => true,
                                'required' => false,
                                'label' => false,
                                'id' => 'preco_banho',
                                'style' => 'display:none;'
                            ]
                        ) ?>

                        <div class="col-lg-12">
                            <?= $this->Form->input(
                                'current_password',
                                [
                                    'type' => 'password',
                                    'id' => 'current_password',
                                    'class' => 'current_password',
                                    'label' => 'Confirmar senha do usuário'
                                ]
                            ) ?>

                        </div>

                        <div class="col-lg-12">
                            <?= $this->Form->button(
                                __('{0} Imprimir', $this->Html->tag('i', '', ['class' => 'fa fa-print'])),
                                [
                                    'type' => 'button',
                                    'id' => 'print_gift',
                                    'escape' => false,
                                    'class' => 'print-gift-comum'
                                ]
                            ) ?>

                        </div>

                    </div>

                <?= $this->Form->end(); ?>

            </div>
        </div>
    </div>

    <div class="container-confirmacao-cupom-comum">
        <legend>Confirmação de Impressão do Canhoto</legend>

        <h4>O canhoto foi emitido com sucesso?</h4>
        <div class="form-group row">
            <div class="col-lg-2"></div>
            <div class="col-lg-3">
                <!-- Confirmação de impressão do canhoto SMART Shower -->
                <?= $this->Html->link(
                    __("{0} Sim", $this->Html->tag("i", '', ['class' => 'fa fa-check'])),
                    ['controller' => 'brindes', 'action' => 'impressao_rapida'],
                    ['escape' => false, 'class' => 'btn btn-primary btn-block']
                ); ?>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-3">
                <!-- Reimprime canhoto smart shower -->
                <?= $this->Html->tag('button', __("{0} Não, Reimprimir", $this->Html->tag('i', '', ['class' => 'fa fa-remove'])), [
                    'id' => 'reimpressao-canhoto-shower',
                    'class' => 'reimpressao-canhoto-shower btn btn-danger btn-block'
                ]) ?>
                </div>
            <div class="col-lg-2"></div>
        </div>
    </div>

<div class="hidden">
    <?= $this->element('../Cupons/impressao_cupom_layout') ?>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/cupons/imprime_brinde_comum') ?>
    <?= $this->Html->css('styles/cupons/imprime_brinde_comum') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/cupons/imprime_brinde_comum.min') ?>
    <?= $this->Html->css('styles/cupons/imprime_brinde_comum.min') ?>
<?php endif; ?>


<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
