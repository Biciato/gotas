<?php
use Cake\Core\Configure;
use Cake\Routing\Router;

/**
 * Controla o menu à direita
 *
 */
$usuarioLogado = $this->request->session()->read('Auth.User');
$clienteAdministrar = $this->request->session()->read('Rede.PontoAtendimento');
$clienteGerenciado = $this->request->session()->read('Rede.PontoAtendimento');

$usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');

$rede = $this->request->session()->read('Rede.Grupo');

$usuarioAdministrar = null;
if (isset($usuarioAdministrador)) {
    $usuarioAdministrador = $usuarioLogado;
    $usuarioLogado = $this->request->session()->read('Usuario.Administrar');
}

if (empty($usuarioLogado)) {
    ?>

    <ul class="nav navbar-nav navbar-right">
        <li>
            <?php echo $this->Html->link('Registrar', ['controller' => 'Usuarios', 'action' => 'registrar']); ?>
        </li>
        <li>
            <?php echo $this->Html->link('Logar', ['controller' => 'Usuarios', 'action' => 'login']) ?>
        </li>
    </ul>

    <?php

} else {
    if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
        ?>

        <ul class="nav navbar-nav navbar-right">

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Operacional<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li>
                        <?php echo $this->Html->link('Usuários', ['controller' => 'Usuarios', 'action' => 'index']) ?>
                    </li>

                    <li role="separator" class="divider"></li>
                    <li>

                        <?php echo $this->Html->link('Redes', ['controller' => 'Redes', 'action' => 'index']) ?>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <?php echo $this->Html->link('Tipos de Brindes', ['controller' => 'TiposBrindesRedes', 'action' => 'index']) ?>
                    </li>

                    <li role="separator" class="divider"></li>

                    <li>
                        <?php echo $this->Html->link('Transportadoras', ['controller' => 'Transportadoras', 'action' => 'index']) ?>
                    </li>

                    <li role="separator" class="divider"></li>

                    <li>
                        <?php echo $this->Html->link('Veículos', ['controller' => 'Veiculos', 'action' => 'index']) ?>
                    </li>

                    <li role="separator" class="divider"></li>

                    <li>
                        <?php echo $this->Html->link(
                            __(
                                '{0} Remoção de pontuacoes',
                                $this->Html->tag('i', '', ['class' => 'fa fa-warning'])
                            ),
                            [
                                'controller' => 'pontuacoes_comprovantes', 'action' => 'remover_pontuacoes',
                            ],
                            [
                                'escape' => false,
                                'class' => 'bg-danger text-danger'
                            ]
                        ); ?>
                    </li>

                    <li role="separator" class="divider"></li>

                    <li>
                        <?php echo $this->Html->link(
                            __(
                                '{0} Alterar modo de visualização',
                                $this->Html->tag('i', '', ['class' => 'fa fa-warning'])
                            ),
                            [
                                'controller' => 'usuarios', 'action' => 'administrar_usuario',
                            ],
                            [
                                'escape' => false,
                                'class' => 'bg-danger text-danger'
                            ]
                        ); ?>
                    </li>
                </ul>
            </li>



             <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Relatórios<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li>
                        <li class="dropdown-submenu">
                            <a href="#" class="test dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Redes
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><?php echo $this->Html->link(__("Redes Cadastradas"), ['controller' => 'Redes', 'action' => 'relatorio_redes']) ?> </li>
                                <li><?php echo $this->Html->link(__("Unidades por Rede"), ['controller' => 'RedesHasClientes', 'action' => 'relatorio_unidades_redes']) ?> </li>
                                <li><?php echo $this->Html->link(__("Equipe por Rede"), ['controller' => 'usuarios', 'action' => 'relatorio_equipe_redes']) ?> </li>
                            </ul>
                        </li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-submenu">
                            <a href="#" class="test dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Brindes
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><?php echo $this->Html->link(__("Brindes Cadastrados por Rede"), ['controller' => 'Brindes', 'action' => 'relatorio_brindes_redes']) ?> </li>

                                <li><?php echo $this->Html->link(__("Brindes Habilitados de Unidades por Rede"), ['controller' => 'ClientesHasBrindesHabilitados', 'action' => 'relatorio_brindes_habilitados_redes']) ?> </li>

                                <li><?php echo $this->Html->link(__("Estoque de Brindes por Unidade de Rede"), ['controller' => 'ClientesHasBrindesEstoque', 'action' => 'relatorio_estoque_brindes_redes']) ?> </li>

                                <li><?php echo $this->Html->link(__("Histórico de Preços de Brinde "), ['controller' => 'ClientesHasBrindesHabilitadosPreco', 'action' => 'relatorio_historico_preco_brindes_redes']) ?> </li>

                            </ul>
                        </li>

                        <li role="separator" class="divider"></li>

                        <li class="dropdown-submenu">
                            <a href="#" class="test dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Gotas
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><?php echo $this->Html->link(__("Gotas de cada Rede"), ['controller' => 'Gotas', 'action' => 'relatorio_gotas_redes']) ?> </li>
                                <li><?php echo $this->Html->link(__("Consumo de Gotas por Usuários"), ['controller' => 'gotas', 'action' => 'relatorio_consumo_gotas_usuarios']) ?></li>
                            </ul>
                        </li>

                        <li role="separator" class="divider"></li>

                        <li class="dropdown-submenu">
                            <a href="#" class="test dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Pontuações
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                            <li>
                                <?php echo $this->Html->link(
                                    __("Pontuações por Rede/Unidades"),
                                    ['controller' => 'PontuacoesComprovantes', 'action' => 'relatorio_pontuacoes_comprovantes_redes']
                                ) ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link(
                                    __("Pontuações por Usuários de Redes"),
                                    ['controller' => 'PontuacoesComprovantes', 'action' => 'relatorio_pontuacoes_comprovantes_usuarios_redes']
                                ) ?>
                            </li>
                            </ul>
                        </li>

                        <li role="separator" class="divider"></li>

                        <li class="dropdown-submenu">
                            <a href="#" class="test dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Usuários
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><?php echo $this->Html->link(__("Usuários Cadastrados"), ['controller' => 'Usuarios', 'action' => 'relatorio_usuarios_cadastrados']) ?> </li>
                                <li><?php echo $this->Html->link(__("Usuários Por Redes"), ['controller' => 'Usuarios', 'action' => 'relatorio_usuarios_redes']) ?> </li>
                                <li><?php echo $this->Html->link(__("Brindes Adquiridos pelos Usuários "), ['controller' => 'UsuariosHasBrindes', 'action' => 'relatorio_brindes_usuarios_redes']) ?> </li>

                            </ul>
                        </li>

                        <li role="separator" class="divider"></li>

                        <li class="dropdown-submenu">
                            <a href="#" class="test dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Transportadoras
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><?php echo $this->Html->link(__("Transportadoras Cadastradas de Clientes das Redes"), ['controller' => 'Transportadoras', 'action' => 'relatorio_transportadoras_usuarios_redes']) ?> </li>
                            </ul>
                        </li>

                        <li role="separator" class="divider"></li>

                        <li class="dropdown-submenu">
                            <a href="#" class="test dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Veículos
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><?php echo $this->Html->link(__("Veiculos Cadastrados de Clientes das Redes"), ['controller' => 'Veiculos', 'action' => 'relatorio_veiculos_usuarios_redes']) ?> </li>
                            </ul>
                        </li>
                    </li>
                </ul>
            </li>

            <li>
                <?php echo $this->Html->link('Meu Cadastro', ['controller' => 'Usuarios', 'action' => 'meu_perfil']) ?>
            </li>
            <li>
                <?php echo $this->Html->link('Sair', ['controller' => 'Usuarios', 'action' => 'logout']) ?>
            </li>
        </ul>
<?php
    // Administrador de Rede ou Regional

    } else if ($usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {

        ?>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Operacional<span class="caret"></span></a>
                <ul class="dropdown-menu">


                    <!-- se o administrador não selecionou uma unidade para gerenciar
                    só permite ver os items de Cadastro de Usuários e Relatórios -->

                    <li>
                        <?php echo $this->Html->link('Usuários da Rede', ['controller' => 'Usuarios', 'action' => 'usuarios_rede']) ?>
                    </li>

                    <li role="separator" class="divider" />

                    <li>
                        <?php echo $this->Html->link('Atribuição de Gotas Por Consumo', ['controller' => 'gotas', 'action' => 'gotas_minha_rede']) ?>
                    </li>

                    <?php if ($usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["AdminNetworkProfileType"]): ?>
                        <li role="separator" class="divider"></li>

                        <li>
                            <a href="/tipos-brindes-redes/configurar-tipos-brindes-rede/<?php echo $rede['id']?>">Tipos de Brindes na sua Rede</a>
                        </li>

                        <li>
                            <a href="/tipos-brindes-clientes/selecionar-cliente-tipo-brinde/">Configurar Tipos de Brindes nos Pontos de Atendimento</a>
                        </li>

                    <?php endif;?>
                    <li role="separator" class="divider" />

                    <?php
                    //brinde só pode ser cadastrado por um Administrador da Rede
                    if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminNetworkProfileType']) {
                        ?>

                        <li>
                            <?php echo $this->Html->link('Cadastro de Brindes', ['controller' => 'Brindes', 'action' => 'brindes_minha_rede']) ?>
                        </li>

                        <?php

                    } ?>

                    <li>
                        <?php echo $this->Html->link('Configurar Brindes em Pontos de Atendimento', ['controller' => 'clientes_has_brindes_habilitados', 'action' => 'escolherUnidadeConfigBrinde']) ?>
                    </li>

                    <?php
                    //Preço de brinde pendente só pode ser autorizado por um administrador que seja pelo menos regional
                    if ($usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
                        ?>

                        <li>
                            <?php echo $this->Html->link('Brindes com Preços Pendentes de Autorização', ['controller' => 'clientes_has_brindes_habilitados_preco', 'action' => 'brindes_aguardando_aprovacao']) ?>
                        </li>

                        <?php

                    } ?>

                    <li>
                        <?php echo $this->Html->link('Histórico de Brindes', ['controller' => 'cupons', 'action' => 'historico_brindes']) ?>
                    </li>

                    <li role="separator" class="divider" />

                    <li>
                        <?php echo $this->Html->link('Relatório de Cupons', ['controller' => 'Pontuacoes', 'action' => 'cupons_minha_rede']) ?>
                    </li>

                    <li role="separator" class="divider" />
                    <li>
                        <?php
                        echo $this->Html->link('Meus Clientes', ['controller' => 'Usuarios', 'action' => 'meus_clientes'])
                        ?>
                    </li>
                    <li role="separator" class="divider" />
                    <li>
                        <?php echo $this->Html->link('Definição de Propaganda', ['controller' => 'redes_has_clientes', 'action' => 'propaganda_escolha_unidades']) ?>
                    </li>

                        <li class="divider"></li>

                        <!-- <li>
                            <a href="#!/relatorios">Relatórios</a>
                        </li> -->
                        <!-- Ativar relatórios aqui -->
                        <li>
                            <a href="/usuarios/relatorios">Relatórios</a>
                        </li>

                    </ul>
                </li>

            <li>
                <?php echo $this->Html->link('Meu Cadastro', ['controller' => 'Usuarios', 'action' => 'meu_perfil']) ?>
            </li>
            <li>
                <?php echo $this->Html->link('Sair', ['controller' => 'Usuarios', 'action' => 'logout']) ?>
            </li>
        </ul>


<?php

    }
        // Administrador da loja
        else if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminLocalProfileType']) {

            ?>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Operacional<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li>
                        <?php echo $this->Html->link('Usuários da Loja/Posto', ['controller' => 'Usuarios', 'action' => 'usuarios_rede']) ?>
                    </li>

                    <li role="separator" class="divider"></li>

                    <li>
                        <?php echo $this->Html->link('Atribuição de Gotas Por Consumo', ['controller' => 'gotas', 'action' => 'gotas_minha_loja']) ?>
                    </li>
                    <li role="separator" class="divider"></li>

                    <!-- <li>
                        <?php echo $this->Html->link('Brindes Habilitados da Loja', ['controller' => 'clientesHasBrindesHabilitados', 'action' => 'meus_brindes_ativados']) ?>
                    </li> -->
                    <li>
                        <?php echo $this->Html->link('Configurar Brindes no Ponto de Atendimento', ['controller' => 'clientes_has_brindes_habilitados', 'action' => 'configurar-brindes-unidade', $clienteGerenciado->id]) ?>
                    </li>

                    <li>
                        <?php echo $this->Html->link('Emissão de Cupom de Brindes - Troca por Gotas', ['controller' => 'brindes', 'action' => 'impressao_rapida']) ?>
                    </li>

                    <li>
                        <?php echo $this->Html->link('Emissão de Cupom de Brindes - Venda Avulsa', ['controller' => 'cupons', 'action' => 'emissao_brinde_avulso']) ?>
                    </li>

                    <li>
                        <?php echo $this->Html->link('Histórico de Brindes', ['controller' => 'cupons', 'action' => 'historico_brindes']) ?>
                    </li>

                    <li role="separator" class="divider" />

                    <li>
                        <?php echo $this->Html->link('Relatório de Cupons', ['controller' => 'Pontuacoes', 'action' => 'cupons_minha_rede']) ?>
                    </li>

                    <li role="separator" class="divider" />
                    <li>
                        <?php echo $this->Html->link('Meus Clientes', ['controller' => 'Usuarios', 'action' => 'meus_clientes']) ?>
                    </li>

                    <li role="separator" class="divider" />
                    <li>
                        <?php echo $this->Html->link('Definição de Propaganda', ['controller' => 'clientes', 'action' => 'configurar_propaganda']) ?>
                    </li>
                </ul>
            </li>

            <li>
                <?php echo $this->Html->link('Meu Cadastro', ['controller' => 'Usuarios', 'action' => 'meu_perfil']) ?>
            </li>
            <li>
                <?php echo $this->Html->link('Sair', ['controller' => 'Usuarios', 'action' => 'logout']) ?>
            </li>
        </ul>

<?php

    } else if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['ManagerProfileType']) {

        // Gerente

?>
        <ul class="nav navbar-nav navbar-right">

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Operacional<span class="caret"></span></a>
                <ul class="dropdown-menu">
                <li>
                    <?php echo $this->Html->link('Usuários da Loja/Posto', ['controller' => 'Usuarios', 'action' => 'usuarios_rede', $rede->id]) ?>
                </li>

                <li role="separator" class="divider"></li>

                <li>
                    <?php echo $this->Html->link('Emissão de Cupom de Brindes - Troca por Gotas', ['controller' => 'brindes', 'action' => 'impressao_rapida']) ?>
                </li>

                <li>
                    <?php echo $this->Html->link('Emissão de Cupom de Brindes - Venda Avulsa', ['controller' => 'cupons', 'action' => 'emissao_brinde_avulso']) ?>
                </li>

                <li>
                    <?php echo $this->Html->link('Histórico de Brindes', ['controller' => 'cupons', 'action' => 'historico_brindes']) ?>
                </li>

                <li role="separator" class="divider" />

                <li>
                    <?php echo $this->Html->link('Relatório de Cupons', ['controller' => 'Pontuacoes', 'action' => 'cupons_minha_rede']) ?>
                </li>

                <li role="separator" class="divider" />
                <li>
                    <?php echo $this->Html->link('Meus Clientes', ['controller' => 'Usuarios', 'action' => 'meus_clientes']) ?>
                </li>

            </ul>
            </li>

            <li>
                <?php echo $this->Html->link('Meu Cadastro', ['controller' => 'Usuarios', 'action' => 'meu_perfil']) ?>
            </li>
            <li>
                <?php echo $this->Html->link('Sair', ['controller' => 'Usuarios', 'action' => 'logout']) ?>
            </li>
        </ul>

<?php

    } else if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['WorkerProfileType']) {
    // Funcionário

?>
        <ul class="nav navbar-nav navbar-right">


            <!-- <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Operacional<span class="caret"></span></a>
                <ul class="dropdown-menu">


                    <li>
                        <?php echo $this->Html->link('Verificação de Brindes Virtuais', ['controller' => 'Brindes', 'action' => 'verificarBrindeVirtual']) ?>
                    </li>



                </ul>
            </li> -->

            <li>
                <?php echo $this->Html->link('Meu Cadastro', ['controller' => 'Usuarios', 'action' => 'meu_perfil']) ?>
            </li>
            <li>
                <?php echo $this->Html->link('Sair', ['controller' => 'Usuarios', 'action' => 'logout']) ?>
            </li>
        </ul>

<?php

    } else {
        // Cliente

?>
        <ul class="nav navbar-nav navbar-right">


            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Acesso Rápido<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li>
                        <?php echo $this->Html->link('Meu Histórico de Cupons de Brindes', ['controller' => 'usuarios_has_brindes', 'action' => 'historico_brindes']) ?>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <?php echo $this->Html->link('Meu Histórico de Gotas', ['controller' => 'PontuacoesComprovantes', 'action' => 'historico_pontuacoes']) ?>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <?php echo $this->Html->link(__("Meus Veículos"), ['controller' => 'Veiculos', 'action' => 'meus_veiculos']) ?>
                    </li>
                </ul>
            </li>

            <li>
                <?php echo $this->Html->link('Meu Cadastro', ['controller' => 'Usuarios', 'action' => 'meu_perfil']) ?>
            </li>
            <li>
                <?php echo $this->Html->link('Sair', ['controller' => 'Usuarios', 'action' => 'logout']) ?>
            </li>
        </ul>

<?php

    }

}

?>
