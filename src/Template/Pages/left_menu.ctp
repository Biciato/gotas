<?php

use Cake\Routing\Router;
use Cake\Core\Configure;

$item_selected = isset($item_selected) ? $item_selected : null;
$mode_selected = isset($mode_selected) ? $mode_selected : null;

// $tipoPerfil = isset($usuarioLogado) ? $usuarioLogado["tipo_perfil"] : Configure::read("profileTypes")["UserProfileType"];


// @todo Ajustar este menu
// $usuarioAdministrar = $this->request->session()->read("Usuario.Administrar");

// if (!empty($usuarioAdministrar)) {
//     $usuarioLogado = $usuarioAdministrar;
//     $tipoPerfil = $usuarioLogado["tipo_perfil"];
// }
//
?>

<nav class="col-lg-3 col-md-4 columns" id="actions-sidebar">


    <?php
    // if ($tipoPerfil >= Configure::read("profileTypes")["AdminDeveloperProfileType"]
    // && $tipoPerfil <= Configure::read("profileTypes")["AdminRegionalProfileType"]) :
    ?>

    <!-- <ul class="nav nav-pills nav-stacked list-group">
            <li class="list-group-item active">
                    <?= __('Menu') ?>
            </li>
            <li class="list-group-item active">
                    <?= __('Ações') ?>
            </li>
            <li>
                <?= $this->Html->link(__('Nova Transportadora'), ['controller' => 'Transportadoras', 'action' => 'adicionar_transportadora_usuario_final', $usuarioLogado["id"]]) ?>
            </li>
        </ul>  -->
    <?php
    //  elseif ($tipoPerfil == Configure::read("profileTypes")["AdminLocalProfileType"] || $tipoPerfil == Configure::read("profileTypes")["ManagerProfileType"]) :
    ?>
    <?php if ($usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["AdminLocalProfileType"] || $usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["ManagerProfileType"]) : ?>

        <ul class="nav nav-pills nav-stacked list-group">
            <li class="list-group-item active">
                <?= __('Menu') ?>
            </li>
        </ul>
    <?php elseif ($usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["WorkerProfileType"]) : ?>
        <ul class="nav nav-pills nav-stacked list-group">
            <li class="list-group-item active">
                <?= __('Menu') ?>
            </li>
            <li class="list-group-item active">
                <?= __('Operacional') ?>
            </li>
            <?php if ($item_selected == 'atribuir_gotas') : ?>
                <li class="list-group-item-success">
                <?php else : ?>
                <li>
                <?php endif; ?>

                <?= $this->Html->link(__('Pontuar Gotas'), ['controller' => 'Gotas', 'action' => 'atribuir_gotas']) ?>
                </li>
                <?php if ($item_selected == 'resgate_brinde') : ?>
                    <li class="list-group-item-success">
                    <?php else : ?>
                    <li>
                    <?php endif; ?>
                    <?= $this->Html->link(__('Resgate de Brinde'), ['controller' => 'Brindes', 'action' => 'resgate_brinde']) ?>
                    </li>
                    <?php if ($item_selected == 'validarBrinde') : ?>
                        <li class="list-group-item-success">
                        <?php else : ?>
                        <li>
                        <?php endif; ?>
                        <?= $this->Html->link(__('Validação de Brinde'), ['controller' => 'Cupons', 'action' => 'validarBrinde']) ?>
                        </li>
                        <!-- Emissão de Banho Smart Shower Avulso -->
                        <?php if ($item_selected == 'emissao_brinde_avulso') : ?>
                            <li class="list-group-item-success">
                            <?php else : ?>
                            <li>
                            <?php endif; ?>
                            <?= $this->Html->link(__('Venda Avulsa'), ['controller' => 'Cupons', 'action' => 'emissao_brinde_avulso']) ?>
                            </li>

                            <li class="list-group-item active">
                                <?= __('Cadastros') ?>
                            </li>
                            <?php if ($item_selected == 'cadastrar_cliente') : ?>
                                <li class="list-group-item-success">
                                <?php else : ?>
                                <li>
                                <?php endif; ?>
                                <?= $this->Html->link(__('Cadastrar Cliente'), ['controller' => 'Usuarios', 'action' => 'adicionar_conta']) ?>
                                </li>

                                <!-- @todo gustavosg: 2018-02-26: Desabilitado devido demanda https://trello.com/c/QDWbmYC9
                <?php if ($item_selected == 'atualizar_cadastro_cliente') : ?>
                <li class="list-group-item-success">
            <?php else : ?>
                <li>
            <?php endif; ?>
                <?= $this->Html->link(__('Atualizar Cad. Cliente'), ['controller' => 'Usuarios', 'action' => 'pesquisar_cliente_alterar_dados']) ?>
            </li> -->

                                <li class="list-group-item active">
                                    <?= __('Relatórios') ?>
                                </li>

                                <li class="<?= $item_selected == 'relatorio_caixa_funcionario' ? 'list-group-item-success' : '' ?> ">
                                    <a href="/cupons/relatorioCaixaFuncionario/">Relatório de Caixa</a>
                                </li>
                                <li class="<?= $item_selected == 'rel_gestao_gotas' ? 'list-group-item-success' : '' ?> ">
                                    <a href="/pontuacoes/relGestaoGotas">Gestão de Gotas</a>
                                </li>




                                <?php if ($mode_selected == 'atualizar_cadastro_cliente_veiculos') : ?>
                                    <nav class="columns" id="actions-sidebar">
                                        <ul class="nav nav-pills nav-stacked list-group">
                                            <li><?= $this->Html->link(__('Gerenciar Veículos de Usuário'), ['controller' => 'Veiculos', 'action' => 'veiculos_usuario_final', $usuarios_id]) ?></li>

                                            <nav class="columns" id="actions-sidebar">

                                                <ul class="nav nav-pills nav-stacked list-group">
                                                    <li><?= $this->Html->link(__('Novo Veículo'), ['controller' => 'Veiculos', 'action' => 'adicionar_veiculo_usuario_final', $usuarios_id]) ?></li>
                                                </ul>
                                            </nav>
                                        </ul>
                                    </nav>
                                <?php elseif ($mode_selected == 'atualizar_cadastro_cliente_transportadoras') : ?>
                                    <nav class="columns" id="actions-sidebar">
                                        <ul class="nav nav-pills nav-stacked list-group">
                                            <li><?= $this->Html->link(__('Gerenciar Transportadoras de Usuário'), ['controller' => 'Transportadoras', 'action' => 'transportadorasUsuario', $usuarios_id]) ?></li>

                                            <nav class="columns" id="actions-sidebar">

                                                <ul class="nav nav-pills nav-stacked list-group">
                                                    <li><?= $this->Html->link(__('Nova Transportadora'), ['controller' => 'Transportadoras', 'action' => 'adicionar_transportadora_usuario_final', $usuarios_id]) ?></li>
                                                </ul>
                                            </nav>
                                        </ul>
                                    </nav>

                                <?php endif; ?>

                                <!-- <?php if ($item_selected == 'consulta_pontuacoes') : ?>
                <li class="list-group-item-success">
            <?php else : ?>
                <li>
            <?php endif; ?>
                <?= $this->Html->link(__('Consulta de Pontuações'), ['controller' => 'PontuacoesComprovantes', 'action' => 'pesquisar_cliente_final_pontuacoes']) ?>
            </li>

            <?php if ($mode_selected == 'exibir_cliente_final_pontuacoes') : ?>
                <nav class="columns" id="actions-sidebar">
                    <ul class="nav nav-pills nav-stacked list-group">
                        <li><?= $this->Html->link(__('Pontuações do Cliente Final'), ['controller' => 'PontuacoesComprovantes', 'action' => 'exibir_cliente_final_pontuacoes', $usuarios_id]) ?></li>

                    </ul>
                </nav>
            <?php endif; ?>

            <?php if ($item_selected == 'historico_brindes') : ?>
                <li class="list-group-item-success">
            <?php else : ?>
                <li>
            <?php endif; ?>
                <?= $this->Html->link(__('Histórico de Brindes'), ['controller' => 'UsuariosHasBrindes', 'action' => 'pesquisar_cliente_final_brindes']) ?>
            </li>

            <?php if ($mode_selected == 'exibir_cliente_final_brindes') : ?>
                <nav class="columns" id="actions-sidebar">
                    <ul class="nav nav-pills nav-stacked list-group">
                        <li><?= $this->Html->link(__('Brindes do Cliente Final'), ['controller' => 'UsuariosHasBrindes', 'action' => 'exibir_cliente_final_brindes', $usuarios_id]) ?></li>

                    </ul>
                </nav>
            <?php endif; ?> -->

        </ul>

    <?php endif; ?>
</nav>
