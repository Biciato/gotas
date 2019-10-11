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
                    "method" => "POST",
                    "path" => "/findBrindes"
                ],
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "getBrindesUnidadeAPI" => [
                    "action" => "getBrindesUnidadeAPI",
                    "method" => "POST",
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
                    "method" => "POST",
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

    $routes->resources(
        "Cupons",
        [
            "map" => [
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "efetuarBaixaCupomAPI" => array(
                    "action" => "efetuarBaixaCupomAPI",
                    "method" => "POST",
                    "path" => "/efetuar_baixa_cupom"
                ),
                // utilizado pelo APP Mobile.
                "efetuarEstornoCupomAPI" => array(
                    "action" => "efetuarEstornoCupomAPI",
                    "method" => "POST",
                    "path" => "efetuar_estorno_cupom"
                ),
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "resgatarCupomAPI" => [
                    "action" => "resgatarCupomAPI",
                    "method" => "POST",
                    "path" => "/resgatar_cupom"
                ],
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "getCuponsUsuarioAPI" => [
                    "action" => "getCuponsUsuarioAPI",
                    "method" => "POST",
                    "path" => "/get_cupons_usuario"
                ]
            ]
        ]
    );

    $routes->resources("Gotas", [
        "map" => [
            "getGotasClientesAPI" => [
                "action" => "getGotasClientesAPI",
                "method" => Request::METHOD_GET,
                "path" => "/get_gotas_clientes"
            ]
        ]
    ]);

    $routes->resources(
        "Pontuacoes",
        [
            "map" => [
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "getPontuacoesRedeAPI" => [
                    "action" => "getPontuacoesRedeAPI",
                    "method" => "POST",
                    "path" => "/get_pontuacoes_rede"
                ],
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "getExtratoPontuacoesAPI" => array(
                    "action" => "getExtratoPontuacoesAPI",
                    "method" => "POST",
                    "path" => "/get_extrato_pontuacoes"
                )
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
                    "method" => "POST",
                    "path" => "/get_comprovantes_fiscais_usuario"
                ],
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "setComprovanteFiscalUsuarioAPI" => [
                    "action" => "setComprovanteFiscalUsuarioAPI",
                    "method" => "POST",
                    "path" => "/set_comprovante_fiscal_usuario"
                ],
                // utilizado por clientes REST de Sistemas de Postos. Cuidado ao mexer
                "setPontuacoesUsuarioViaPostoAPI" => array(
                    "action" => "setPontuacoesUsuarioViaPostoAPI",
                    "method" => "POST",
                    "path" => "/set_pontuacoes_usuario_via_posto"
                ),
                // utilizado pelo APP Mobile. Cuidado ao mexer
                "removerPontuacoesDevAPI" => array(
                    "action" => "removerPontuacoesDevAPI",
                    "method" => "GET",
                    "path" => "/remover_pontuacoes_dev"
                ),
                "setComprovanteFiscalViaFuncionarioAPI" => array(
                    "action" => "setComprovanteFiscalViaFuncionarioAPI",
                    "method" => "POST",
                    "path" => "set_comprovante_fiscal_via_funcionario"
                )
            ]
        ]
    );

    $routes->resources("Redes", [
        "map" => [
            "getRedeAPI" => [
                "action" => "getRedeAPI",
                "method" => Request::METHOD_GET,
                "path" => "/get_rede"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getRedesAPI" => [
                "action" => "getRedesAPI",
                "method" => "POST",
                "path" => "/get_redes"
            ],
            "getRedesListAPI" => [
                "action" => "getRedesListAPI",
                "method" => Request::METHOD_GET,
                "path" => "/get_redes_list"
            ],
            "enviaImagemPropagandaAPI" => array(
                "action" => "enviaImagemPropagandaAPI",
                "method" => "POST",
                "path" => "/envia_imagem_propaganda"
            )
        ]
    ]);

    $routes->resources("Clientes", array(
        "map" => array(
            "enviaImagemPropagandaAPI" => array(
                "action" => "enviaImagemPropagandaAPI",
                "method" => "POST",
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
            )
        )
    ));

    $routes->resources("RedesHasClientes", [
        "map" => array(
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getUnidadeRedeByIdAPI" => array(
                "action" => "getUnidadeRedeByIdAPI",
                "method" => "POST",
                "path" => "/get_unidade_rede_by_id"
            ),
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getUnidadesRedesProximasAPI" => array(
                "action" => "getUnidadesRedesProximasAPI",
                "method" => "POST",
                "path" => "/get_unidades_redes_proximas"
            ),
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getUnidadesRedesAPI" => array(
                "action" => "getUnidadesRedesAPI",
                "method" => "POST",
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
                "method" => "POST",
                "path" => "set_top_brinde_nacional"
            ],
            "setTopBrindePostoAPI" => [
                "action" => "setTopBrindePostoAPI",
                "method" => "POST",
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
                "method" => "POST",
                "path" => "/get_transportadora_by_cnpj"
            ],
            // Utilizado pelo angular
            "getTransportadorasUsuarioAPI" => array(
                "action" => "getTransportadorasUsuarioAPI",
                "method" => "POST",
                "path" => "/get_transportadoras_usuario"
            )
        ]
    ]);

    $routes->resources("TransportadorasHasUsuarios", [
        "map" => [
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getTransportadorasUsuarioAPI" => [
                "action" => "getTransportadorasUsuarioAPI",
                "method" => "POST",
                "path" => "/get_transportadoras_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "setTransportadorasUsuarioAPI" => [
                "action" => "setTransportadorasUsuarioAPI",
                "method" => "POST",
                "path" => "/set_transportadoras_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "updateTransportadorasUsuarioAPI" => [
                "action" => "updateTransportadorasUsuarioAPI",
                "method" => "POST",
                "path" => "/update_transportadoras_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "deleteTransportadorasUsuarioAPI" => [
                "action" => "deleteTransportadorasUsuarioAPI",
                "method" => "POST",
                "path" => "/delete_transportadoras_usuario"
            ],
        ]
    ]);

    $routes->resources("Usuarios", [
        'map' => [
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "alterarSenhaAPI" => array(
                "action" => "alterarSenhaAPI",
                "method" => "POST",
                "path" => "/alterar_senha"
            ),
            // utilizado pelo APP Mobile. Cuidado ao mexer
            'setPerfilAPI' => [
                'action' => "setPerfilAPI",
                "method" => "POST",
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
                "method" => "POST",
                "path" => "/get_usuario_by_id"
            ),

            "getUsuarioByDocEstrangeiroAPI" => array(
                "action" => "getUsuarioByDocEstrangeiroAPI",
                "method" => "POST",
                "path" => "/get_usuario_by_doc_estrangeiro"
            ),

            "getListaUsuariosRedeAPI" => array(
                "action" => "getListaUsuariosRedeAPI",
                "method" => "POST",
                "path" => "/get_lista_usuarios_rede"
            ),

            // utilizado pelo Angular
            "getUsuariosAssiduosAPI" => array(
                "action" => "getUsuariosAssiduosAPI",
                "method" => "POST",
                "path" => "/get_usuarios_assiduos"
            ),

            // utilizado pelo Angular
            "generateExcelUsuariosAssiduosAPI" => array(
                "action" => "generateExcelUsuariosAssiduosAPI",
                "method" => "POST",
                "path" => "/generate_excel_usuarios_assiduos"
            ),

            // utilizado pelo Angular
            "getUsuariosFidelizadosAPI" => array(
                "action" => "getUsuariosFidelizadosAPI",
                "method" => "POST",
                "path" => "/get_usuarios_fidelizados"
            ),

            // utilizado pelo Angular
            "generateExcelUsuariosFidelizadosAPI" => array(
                "action" => "generateExcelUsuariosFidelizadosAPI",
                "method" => "POST",
                "path" => "/generate_excel_usuarios_fidelizados"
            ),
        ]
    ]);

    $routes->resources("Veiculos", [
        "map" => [
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getVeiculoByIdAPI" => array(
                "action" => "getVeiculoByIdAPI",
                "method" => "POST",
                "path" => "/get_veiculo_by_id"
            ),
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getVeiculoByPlacaAPI" => [
                "action" => "getVeiculoByPlacaAPI",
                "method" => "POST",
                "path" => "/get_veiculo_by_placa"
            ],
            // utilizado pelo Angular
            "getVeiculosUsuarioAPI" => array(
                "action" => "getVeiculosUsuarioAPI",
                "method" => "POST",
                "path" => "/get_veiculos_usuario"
            )
        ]
    ]);

    $routes->resources("UsuariosHasVeiculos", [
        "map" => [
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "getVeiculosUsuarioAPI" => [
                "action" => "getVeiculosUsuarioAPI",
                "method" => "POST",
                "path" => "/get_veiculos_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "setVeiculosUsuarioAPI" => [
                "action" => "setVeiculosUsuarioAPI",
                "method" => "POST",
                "path" => "/set_veiculos_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "deleteVeiculosUsuarioAPI" => [
                "action" => "deleteVeiculosUsuarioAPI",
                "method" => "POST",
                "path" => "/delete_veiculos_usuario"
            ],
            // utilizado pelo APP Mobile. Cuidado ao mexer
            "updateVeiculosUsuarioAPI" => [
                "action" => "updateVeiculosUsuarioAPI",
                "method" => "POST",
                "path" => "/update_veiculos_usuario"
            ]
        ]
    ]);

    $routes->resources("Sefaz", [
        "map" => [
            "test" => [
                "action" => "test",
                "method" => Request::METHOD_GET,
                "path" => "/test"
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
