<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/filtro_usuarios_ajax.ctp
 * @date     14/08/2017
 */
use Cake\Core\Configure;
?>

<div class="form-group">

    <div class="user-query-region">

    <h4>Selecione um cliente</h4>

    <!-- <div class="col-lg-2">
        <?= $this->Form->label('Pesquisar por') ?>
    </div> -->

    <div class="col-lg-3">

        <?= $this->Form->input(
            'opcoes',
            [
                'type' => 'select',
                'id' => 'opcoes',
                'class' => 'form-control col-lg-2 opcoes',
                'label' => 'Pesquisar Por',
                'options' => [
                    'nome' => 'nome',
                    'cpf' => 'cpf',
                    'doc_estrangeiro' => 'documento estrangeiro',
                    'placa' => 'placa'
                ],
                'default' => 'placa'
            ]
        ) ?>
    </div>
    <!-- <div class="col-lg-1">
        <?= $this->Form->label('Parâmetro') ?>
    </div> -->

    <div class="col-lg-7">
        <?= $this->Form->input(
            'parametro',
            [
                'id' => 'parametro',
                'label' => 'Parâmetro',
            // 'label' => false,
                'class' => 'form-control col-lg-5 parametro'
            ]
        ) ?>
    </div>

    <div class="col-lg-2 vertical-align">

        <?= $this->Form->button(__("{0} Pesquisar", '<i class="fa fa-search" aria-hidden="true"></i>'), ['class' => 'btn btn-primary btn-block', 'type' => 'button', 'id' => 'searchUsuario']) ?>
    </div>

    <span class="text-danger validation-message" id="userValidationMessage"></span>

    </div>

    <div class="user-result user-result-names" >
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


    <div class="user-result user-result-plates" >

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

    <div class="form user-result col-lg-12">

        <?= $this->Html->tag(
            'div',
            ' Pesquisar cliente',
            ['class' => 'col-lg-2 btn btn-primary fa fa-rotate-right', 'type' => 'button', 'id' => 'new-user-search']
        ) ?>

        <h4>Cliente selecionado</h4>

        <?= $this->Form->text('usuarios_id', [
            'id' => 'usuarios_id',
            'class' => "usuarios_id",
            'style' => 'display: none;'
        ]); ?>

        <div class='col-lg-1'>
            <?= $this->Form->label('Nome') ?>
        </div>
        <div class="col-lg-3 col-md-2">
            <?= $this->Form->input('nome', ['readonly' => true, 'required' => false, 'label' => false, 'id' => 'usuariosNome']) ?>
        </div>

        <div class='col-lg-2'>
            <?= $this->Form->label('Data Nascimento') ?>
        </div>

        <div class="col-lg-2 col-md-1">

            <?= $this->Form->input('data_nasc', ['readonly' => true, 'required' => false, 'label' => false, 'id' => 'usuariosDataNasc']) ?>
        </div>

        <div class='col-lg-1'>
            <?= $this->Form->label('Total Pontos') ?>
        </div>

        <div class="col-lg-3 col-md-2">

            <?= $this->Form->input('pontuacoes', ['readonly' => true, 'required' => false, 'label' => false, 'id' => 'usuariosPontuacoes']) ?>
        </div>

    </div>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/usuarios/filtro_usuarios_ajax') ?>
    <?= $this->Html->css('styles/usuarios/filtro_usuarios_ajax') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/usuarios/filtro_usuarios_ajax.min') ?>
    <?= $this->Html->css('styles/usuarios/filtro_usuarios_ajax.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
