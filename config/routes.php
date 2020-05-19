<?php

/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Http\Client\Request;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */

    $routes->fallbacks(DashedRoute::class);
});

Router::scope("/api", function ($routes) {

    // $routes->extensions(['json', 'xml']);
    $routes->extensions(['json']);

    $routes->resources(
        "Brindes",
        [
            'map' => [
                // Ajax. Irá mudar
                "findBrindes" => [
                    "action" => "findBrindes",
                    "method" => Request::METHOD_POST,
                    "path" => "/findBrindes"
                ],
                "getBrindesList" => [
                    "action" => "getBrindesListAPI",
                    "method" => "GET",
                    "path" => "get_brindes_list"
                ],
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "getBrindesUnidadeAPI" => [
                    "action" => "getBrindesUnidadeAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/get_brindes_unidade"
                ],
                "getBrindesUnidadesParaTopBrindesAPI" => [
                    "action" => "getBrindesUnidadesParaTopBrindesAPI",
                    "method" => "GET",
                    "path" => "/get_brindes_unidades_para_top_brindes"
                ]
            ]
        ]
    );

    $routes->resources(
        "CategoriasBrindes",
        [
            "map" =>
            [
                "getCategoriaBrinde" => [
                    "action" => "getCategoriaBrindeAPI",
                    "method" => "GET",
                    "path" => "/get_categoria_brinde"
                ],
                "getCategoriasBrindes" => [
                    "action" => "getCategoriasBrindesAPI",
                    "method" => "GET",
                    "path" => "/get_categorias_brindes"
                ],
                "setCategoriasBrindes" => [
                    "action" => "setCategoriasBrindesAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/set_categorias_brindes"
                ],
                "updateCategoriasBrindes" => [
                    "action" => "updateCategoriasBrindesAPI",
                    "method" => "PUT",
                    "path" => "/update_categorias_brindes"
                ],
                "updateStatusCategoriasBrindes" => [
                    "action" => "updateStatusCategoriasBrindesAPI",
                    "method" => "PUT",
                    "path" => "/update_status_categorias_brindes"
                ],
                "deleteCategoriasBrindes" => [
                    "action" => "deleteCategoriasBrindesAPI",
                    "method" => "DELETE",
                    "path" => "/delete_categorias_brindes"
                ]
            ]
        ]
    );

    $routes->resources("Clientes", array(
        "map" => array(
            "balancoGeralAPI" => [
                "action" => "balancoGeralAPI",
                "method" => Request::METHOD_GET,
                "path" => "/balanco_geral"
            ],
            "enviaImagemPropagandaAPI" => array(
                "action" => "enviaImagemPropagandaAPI",
                "method" => Request::METHOD_POST,
                "path" => "/envia_imagem_propaganda"
            ),
            // utilizado pelo Angular
            "getClientesListAPI" => array(
                "action" => "getClientesListAPI",
                "method" => "GET",
                "path" => "/get_clientes_list"
            ),
            "getPostoFuncionarioAPI" => array(
                "action" => "getPostoFuncionarioAPI",
                "method" => "GET",
                "path" => "/get_posto_funcionario"
            ),
            "rankingOperacoesAPI" => [
                "action" => "rankingOperacoesAPI",
                "method" => Request::METHOD_GET,
                "path" => "/ranking_operacoes"
            ],
            "clienteFinalAPI" => [
                "action" => "clienteFinalAPI",
                "method" => Request::METHOD_GET,
                "path" => "/cliente_final"
            ],
            "changeStatusAPI" => [
                "action" => "changeStatusAPI",
                "method" => Request::METHOD_PUT,
                "path" => "change-status/:id"
            ]
        )
    ));

    $routes->resources(
        "Cupons",
        [
            "map" => [
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "efetuarBaixaCupomAPI" => array(
                    "action" => "efetuarBaixaCupomAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/efetuar_baixa_cupom"
                ),
                // utilizado pelo APP Mobile.
                "efetuarEstornoCupomAPI" => array(
                    "action" => "efetuarEstornoCupomAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "efetuar_estorno_cupom"
                ),
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "resgatarCupomAPI" => [
                    "action" => "resgatarCupomAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/resgatar_cupom"
                ],
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "getCuponsUsuarioAPI" => [
                    "action" => "getCuponsUsuarioAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/get_cupons_usuario"
                ],
                // utilizado pela parte web
                "getResumoBrindeAPI" =>
                [
                    "action" => "getResumoBrindeAPI",
                    "method" => Request::METHOD_GET,
                    "path" => "get_resumo_brinde"
                ]
            ]
        ]
    );

    $routes->resources(
        "Gotas",
        [
            "map" => [
                "getGotasClientesAPI" => [
                    "action" => "getGotasClientesAPI",
                    "method" => Request::METHOD_GET,
                    "path" => "/get_gotas_clientes"
                ],
                "setGotasClientesAPI" => [
                    "action" => "setGotasClientesAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/set_gotas_clientes"
                ],
            ]
        ]
    );

    $routes->resources(
        "Pontuacoes",
        [
            "map" => [
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "getPontuacoesRedeAPI" => [
                    "action" => "getPontuacoesRedeAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/get_pontuacoes_rede"
                ],
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "getExtratoPontuacoesAPI" => array(
                    "action" => "getExtratoPontuacoesAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/get_extrato_pontuacoes"
                ),
                "getPontuacoesRelatorioEntradaSaidaAPI" => [
                    "action" => "getPontuacoesRelatorioEntradaSaidaAPI",
                    "method" => "GET",
                    "path" => "/get_pontuacoes_relatorio_entrada_saida"
                ],
                "getRelatorioMovimentacaoGotasAPI" => [
                    "action" => "getRelatorioMovimentacaoGotasAPI",
                    "method" => Request::METHOD_GET,
                    "path" => "/get_relatorio_movimentacao_gotas"
                ],
                "getResumoPontuacoesEstabelecimentoAPI" => [
                    "action" => "getResumoPontuacoesEstabelecimentoAPI",
                    "method" => Request::METHOD_GET,
                    "path" => "/get_resumo_pontuacoes_estabelecimento"
                ],
                "saldoPontosAPI" => [
                    "action" => "saldoPontosAPI",
                    "method" => Request::METHOD_GET,
                    "path" => "/saldo_pontos"
                ],
            ]
        ]
    );

    $routes->resources(
        "PontuacoesComprovantes",
        [
            "map" => [
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "getComprovantesFiscaisUsuarioAPI" => [
                    "action" => "getComprovantesFiscaisUsuarioAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/get_comprovantes_fiscais_usuario"
                ],
                "setGotasManualUsuarioAPI" => [
                    "action" => "setGotasManualUsuarioAPI",
                    "method" => "POST",
                    "path" => "/set_gotas_manual_usuario"
                ],
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "setComprovanteFiscalUsuarioAPI" => [
                    "action" => "setComprovanteFiscalUsuarioAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/set_comprovante_fiscal_usuario"
                ],
                "setComprovanteFiscalUsuarioManualAPI" => [
                    "action" => "setComprovanteFiscalUsuarioManualAPI",
                    "method" => "POST",
                    "path" => "/set_comprovante_fiscal_usuario_manual"
                ],
                // utilizado por clientes REST de Sistemas de Postos. Cuidado ao mexer
                "setPontuacoesUsuarioViaPostoAPI" => array(
                    "action" => "setPontuacoesUsuarioViaPostoAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "/set_pontuacoes_usuario_via_posto"
                ),
                "deleteComprovanteFiscalAPI" => [
                    "action" => "deleteComprovanteFiscalAPI",
                    "method" => Request::METHOD_DELETE,
                    "path" => "/delete_comprovante_fiscal"
                ],
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "removerPontuacoesDevAPI" => array(
                    "action" => "removerPontuacoesDevAPI",
                    "method" => "GET",
                    "path" => "/remover_pontuacoes_dev"
                ),
                "setComprovanteFiscalViaFuncionarioAPI" => array(
                    "action" => "setComprovanteFiscalViaFuncionarioAPI",
                    "method" => Request::METHOD_POST,
                    "path" => "set_comprovante_fiscal_via_funcionario"
                )
            ]
        ]
    );

    $routes->resources("Redes", [
        "map" =>
        [
            // "view" => [
            //     "action" => "view",
            //     "method" => Request::METHOD_GET,
            //     "path" => "view/:id"
            // ],
            "getRedeAPI" => [
                "action" => "getRedeAPI",
                "method" => Request::METHOD_GET,
                "path" => "/get_rede"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getRedesAPI" => [
                "action" => "getRedesAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_redes"
            ],
            "getRedesListAPI" => [
                "action" => "getRedesListAPI",
                "method" => Request::METHOD_GET,
                "path" => "/get_redes_list"
            ],
            "setImageNetworkAPI" => [
                "action" => "setImageNetworkAPI",
                "method" => Request::METHOD_POST,
                "path" => "/set_image_network"
            ],
            "enviaImagemPropagandaAPI" => array(
                "action" => "enviaImagemPropagandaAPI",
                "method" => Request::METHOD_POST,
                "path" => "/envia_imagem_propaganda"
            ),
            "changeStatusAPI" => [
                "action" => "changeStatusAPI",
                "method" => Request::METHOD_PUT,
                "path" => "change-status/:id"
            ]
            // ,
            // "delete" => [
            //     "action" => "delete",
            //     "method" => Request::METHOD_DELETE,
            //     "path" => ":id"
            // ]
        ]
    ]);

    $routes->resources("RedesCpfListaNegra");


    $routes->resources("RedesHasClientes", [
        "map" => array(
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getUnidadeRedeByIdAPI" => array(
                "action" => "getUnidadeRedeByIdAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_unidade_rede_by_id"
            ),
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getUnidadesRedesProximasAPI" => array(
                "action" => "getUnidadesRedesProximasAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_unidades_redes_proximas"
            ),
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getUnidadesRedesAPI" => array(
                "action" => "getUnidadesRedesAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_unidades_redes"
            )
        )

    ]);

    $routes->resources("TopBrindes", [
        "map" => [
            "deleteTopBrindesAPI" => [
                "action" => "deleteTopBrindesAPI",
                "method" => "DELETE",
                "path" => "delete_top_brindes"
            ],
            "getTopBrindesNacionalAPI" => [
                "action" => "getTopBrindesNacionalAPI",
                "method" => "GET",
                "path" => "get_top_brindes_nacional"
            ],
            "getTopBrindesPostoAPI" => [
                "action" => "getTopBrindesPostoAPI",
                "method" => "GET",
                "path" => "get_top_brindes_posto"
            ],
            "setTopBrindeNacionalAPI" => [
                "action" => "setTopBrindeNacionalAPI",
                "method" => Request::METHOD_POST,
                "path" => "set_top_brinde_nacional"
            ],
            "setTopBrindePostoAPI" => [
                "action" => "setTopBrindePostoAPI",
                "method" => Request::METHOD_POST,
                "path" => "set_top_brinde_posto"
            ],
            "setPosicoesTopBrindesAPI" => [
                "action" => "setPosicoesTopBrindesAPI",
                "method" => "PUT",
                "path" => "set_posicoes_top_brindes"
            ]
        ]
    ]);

    $routes->resources("Transportadoras", [
        "map" => [
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getTransportadoraByCNPJAPI" => [
                "action" => "getTransportadoraByCNPJAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_transportadora_by_cnpj"
            ],
            // Utilizado pelo angular
            "getTransportadorasUsuarioAPI" => array(
                "action" => "getTransportadorasUsuarioAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_transportadoras_usuario"
            )
        ]
    ]);

    $routes->resources("TransportadorasHasUsuarios", [
        "map" => [
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getTransportadorasUsuarioAPI" => [
                "action" => "getTransportadorasUsuarioAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_transportadoras_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "setTransportadorasUsuarioAPI" => [
                "action" => "setTransportadorasUsuarioAPI",
                "method" => Request::METHOD_POST,
                "path" => "/set_transportadoras_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "updateTransportadorasUsuarioAPI" => [
                "action" => "updateTransportadorasUsuarioAPI",
                "method" => Request::METHOD_POST,
                "path" => "/update_transportadoras_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "deleteTransportadorasUsuarioAPI" => [
                "action" => "deleteTransportadorasUsuarioAPI",
                "method" => Request::METHOD_POST,
                "path" => "/delete_transportadoras_usuario"
            ],
        ]
    ]);

    $routes->resources("Usuarios", [
        'map' => [
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "alterarSenhaAPI" => array(
                "action" => "alterarSenhaAPI",
                "method" => Request::METHOD_POST,
                "path" => "/alterar_senha"
            ),
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'setPerfilAPI' => [
                'action' => "setPerfilAPI",
                "method" => Request::METHOD_POST,
                "path" => "/set_perfil"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'esqueciMinhaSenha' => [
                'action' => 'esqueciMinhaSenhaAPI',
                'method' => 'POST',
                'path' => '/esqueci_minha_senha'
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'login' => [
                'action' => 'login',
                'method' => 'POST',
                'path' => '/login'
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'detalhesUsuario/:id' => [
                'action' => 'detalhesUsuario',
                'id' => '[0-9]+',
                'method' => 'GET',
                'path' => '/detalhes_usuario/:id'
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'loginAPI' => [
                'action' => 'loginAPI',
                'method' => 'POST',
                'path' => '/token'
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'logoutAPI' => [
                'action' => 'logoutAPI',
                'method' => 'GET',
                'path' => '/logout'
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'meuPerfilAPI' => [
                'action' => "meuPerfilAPI",
                "method" => "GET",
                "path" => "/meu_perfil"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'getUsuarioByCPF' => [
                'action' => 'getUsuarioByCPF',
                'method' => 'POST',
                'path' => '/get_usuario_by_cpf'
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'getUsuarioByEmail' => [
                'action' => 'getUsuarioByEmail',
                'method' => 'POST',
                'path' => '/get_usuario_by_email'
            ],
            // Usado pela parte WEB e API, para trazer clientes (usuário final) dos postos
            "getUsuariosFinaisAPI" => [
                "action" => "getUsuariosFinaisAPI",
                "method" => Request::METHOD_GET,
                "path" => "/get_usuarios_finais"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'registrarAPI' => [
                'action' => 'registrarAPI',
                'method' => 'POST',
                'path' => '/registrar'
            ],

            "validarAtualizacaoPerfilAPI" => array(
                "action" => "validarAtualizacaoPerfilAPI",
                "method" => "GET",
                "path" => "/validar_atualizacao_perfil"
            ),
            // utilizado pelo APP Mobile. Cuidado ao mexer
            // "testAPI" => array(
            //     "action" => "testAPI",
            //     "method" => "GET",
            //     "path" => "/test"
            // ),

            // utilizado pelo Angular
            "getUsuarioByIdAPI" => array(
                "action" => "getUsuarioByIdAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_usuario_by_id"
            ),

            "getUsuarioByDocEstrangeiroAPI" => array(
                "action" => "getUsuarioByDocEstrangeiroAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_usuario_by_doc_estrangeiro"
            ),

            "getFuncionariosListAPI" => array(
                "action" => "getFuncionariosListAPI",
                "method" => "GET",
                "path" => "/get_funcionarios_list"
            ),

            // utilizado pelo Angular
            "getUsuariosAssiduosAPI" => array(
                "action" => "getUsuariosAssiduosAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_usuarios_assiduos"
            ),

            // utilizado pelo Angular
            "generateExcelUsuariosAssiduosAPI" => array(
                "action" => "generateExcelUsuariosAssiduosAPI",
                "method" => Request::METHOD_POST,
                "path" => "/generate_excel_usuarios_assiduos"
            ),

            // utilizado pelo Angular
            "getUsuariosFidelizadosAPI" => array(
                "action" => "getUsuariosFidelizadosAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_usuarios_fidelizados"
            ),

            "getUsuariosFidelizadosRedeAPI" => [
                "action" => "getUsuariosFidelizadosRedeAPI",
                "method" => Request::METHOD_GET,
                "path" => "/get_usuarios_fidelizados_rede"
            ],

            // utilizado pelo Angular
            "generateExcelUsuariosFidelizadosAPI" => array(
                "action" => "generateExcelUsuariosFidelizadosAPI",
                "method" => Request::METHOD_POST,
                "path" => "/generate_excel_usuarios_fidelizados"
            ),
            "getProfileTypes" => [
                "action" => "getProfileTypes",
                "method" => Request::METHOD_GET,
                "path" => "get_profile_types"
            ],
            "carregarUsuarios" => [
                "action" => "carregarUsuarios",
                "method" => Request::METHOD_GET,
                "path" => "carregar-usuarios"
            ],
            "startManageUser" => [
                "action" => "startManageUser",
                "method" => Request::METHOD_POST,
                "path" => "start_manage_user"
            ],
            "finishManageUser" => [
                "action" => "finishManageUser",
                "method" => Request::METHOD_POST,
                "path" => "finish_manage_user"
            ]
        ]
    ]);

    $routes->resources("Veiculos", [
        "map" => [
            "getUsuariosByVeiculo" => [
                "action" => "getUsuariosByVeiculoAPI",
                "method" => Request::METHOD_GET,
                "path" => "/get_usuarios_by_veiculo"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getVeiculoByIdAPI" => array(
                "action" => "getVeiculoByIdAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_veiculo_by_id"
            ),
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getVeiculoByPlacaAPI" => [
                "action" => "getVeiculoByPlacaAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_veiculo_by_placa"
            ],
            // utilizado pelo Angular
            "getVeiculosUsuarioAPI" => array(
                "action" => "getVeiculosUsuarioAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_veiculos_usuario"
            )
        ]
    ]);

    $routes->resources("UsuariosHasVeiculos", [
        "map" => [
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getVeiculosUsuarioAPI" => [
                "action" => "getVeiculosUsuarioAPI",
                "method" => Request::METHOD_POST,
                "path" => "/get_veiculos_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "setVeiculosUsuarioAPI" => [
                "action" => "setVeiculosUsuarioAPI",
                "method" => Request::METHOD_POST,
                "path" => "/set_veiculos_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "deleteVeiculosUsuarioAPI" => [
                "action" => "deleteVeiculosUsuarioAPI",
                "method" => Request::METHOD_POST,
                "path" => "/delete_veiculos_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "updateVeiculosUsuarioAPI" => [
                "action" => "updateVeiculosUsuarioAPI",
                "method" => Request::METHOD_POST,
                "path" => "/update_veiculos_usuario"
            ]
        ]
    ]);

    $routes->resources("Sefaz", [
        "map" => [
            "getNFSefazQRCodeAPI" => [
                "action" => "getNFSefazQRCodeAPI",
                "method" => Request::METHOD_GET,
                "path" => "/get_nf_sefaz_qr_code"
            ]
        ]
    ]);
});

// Router::prefix('api', function ($routes) {
//     $routes->extensions(['json', 'xml']);
//     $routes->resources('Users');
//     Router::connect('/api/users/register', ['controller' => 'Users', 'action' => 'add', 'prefix' => 'api']);
//     $routes->fallbacks('InflectedRoute');
// });


/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
