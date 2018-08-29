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

    $routes->extensions(['json', 'xml']);

    $routes->resources(
        "Brindes",
        [
            'map' => [
                "findBrindes" => [
                    "action" => "findBrindes",
                    "method" => "POST",
                    "path" => "/findBrindes"
                ]
            ]
        ]
    );

    $routes->resources(
        "ClientesHasBrindesHabilitados",
        [
            "map" => [
                "getBrindesUnidadeAPI" => [
                    "action" => "getBrindesUnidadeAPI",
                    "method" => "POST",
                    "path" => "/get_brindes_unidade"
                ]
            ]
        ]
    );

    $routes->resources(
        "Cupons",
        [
            "map" => [
                "efetuarBaixaCupomAPI" => array(
                    "action" => "efetuarBaixaCupomAPI",
                    "method" => "POST",
                    "path" => "/efetuar_baixa_cupom"
                ),
                "resgatarCupomAPI" => [
                    "action" => "resgatarCupomAPI",
                    "method" => "POST",
                    "path" => "/resgatar_cupom"
                ],
                "getCuponsUsuarioAPI" => [
                    "action" => "getCuponsUsuarioAPI",
                    "method" => "POST",
                    "path" => "/get_cupons_usuario"
                ]
            ]
        ]
    );

    $routes->resources(
        "TiposBrindesRedes",
        [
            "map" => [
                "getTiposBrindesRedeAPI" => [
                    "action" => "getTiposBrindesRedeAPI",
                    "method" => "POST",
                    "path" => "/get_tipos_brindes_rede"
                ]
            ]
        ]
    );
    $routes->resources(
        "Pontuacoes",
        [
            "map" => [
                "getPontuacoesRedeAPI" => [
                    "action" => "getPontuacoesRedeAPI",
                    "method" => "POST",
                    "path" => "/get_pontuacoes_rede"
                ],
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
                "getComprovantesFiscaisUsuarioAPI" => [
                    "action" => "getComprovantesFiscaisUsuarioAPI",
                    "method" => "POST",
                    "path" => "/get_comprovantes_fiscais_usuario"
                ],
                "setComprovanteFiscalUsuarioAPI" => [
                    "action" => "setComprovanteFiscalUsuarioAPI",
                    "method" => "POST",
                    "path" => "/set_comprovante_fiscal_usuario"
                ],
                "removerPontuacoesDevAPI" => array(
                    "action" => "removerPontuacoesDevAPI",
                    "method" => "GET",
                    "path" => "/remover_pontuacoes_dev"
                )
            ]
        ]
    );

    $routes->resources("Redes", [
        "map" => [
            "getRedesAPI" => [
                "action" => "getRedesAPI",
                "method" => "POST",
                "path" => "/get_redes"
            ]
        ]
    ]);

    $routes->resources("RedesHasClientes", [
        "map" => array(
            "getUnidadeRedeByIdAPI" => array(
                "action" => "getUnidadeRedeByIdAPI",
                "method" => "POST",
                "path" => "/get_unidade_rede_by_id"
            ),
            "getUnidadesRedesProximasAPI" => array(
                "action" => "getUnidadesRedesProximasAPI",
                "method" => "POST",
                "path" => "/get_unidades_redes_proximas"
            ),
            "getUnidadesRedesAPI" => array(
                "action" => "getUnidadesRedesAPI",
                "method" => "POST",
                "path" => "/get_unidades_redes"
            )
        )

    ]);

    $routes->resources("Transportadoras", [
        "map" => [
            "getTransportadoraByCNPJ" => [
                "action" => "getTransportadoraByCNPJ",
                "method" => "POST",
                "path" => "/get_transportadora_by_cnpj"
            ]
        ]
    ]);

    $routes->resources("TransportadorasHasUsuarios", [
        "map" => [
            "getTransportadorasUsuarioAPI" => [
                "action" => "getTransportadorasUsuarioAPI",
                "method" => "POST",
                "path" => "/get_transportadoras_usuario"
            ],
            "setTransportadorasUsuarioAPI" => [
                "action" => "setTransportadorasUsuarioAPI",
                "method" => "POST",
                "path" => "/set_transportadoras_usuario"
            ],
            "updateTransportadorasUsuarioAPI" => [
                "action" => "updateTransportadorasUsuarioAPI",
                "method" => "POST",
                "path" => "/update_transportadoras_usuario"
            ],
            "deleteTransportadorasUsuarioAPI" => [
                "action" => "deleteTransportadorasUsuarioAPI",
                "method" => "POST",
                "path" => "/delete_transportadoras_usuario"
            ],
        ]
    ]);

    $routes->resources("Usuarios", [
        'map' => [
            // Usuários
            'setPerfilAPI' => [
                'action' => "setPerfilAPI",
                "method" => "POST",
                "path" => "/set_perfil"
            ],
            'esqueciMinhaSenha' => [
                'action' => 'esqueciMinhaSenhaAPI',
                'method' => 'POST',
                'path' => '/esqueci_minha_senha'
            ],
            'login' => [
                'action' => 'login',
                'method' => 'POST',
                'path' => '/login'
            ],
            'detalhesUsuario/:id' => [
                'action' => 'detalhesUsuario',
                'id' => '[0-9]+',
                'method' => 'GET',
                'path' => '/detalhes_usuario/:id'
            ],
            'loginAPI' => [
                'action' => 'loginAPI',
                'method' => 'POST',
                'path' => '/token'
            ],
            'logoutAPI' => [
                'action' => 'logoutAPI',
                'method' => 'GET',
                'path' => '/logout'
            ],
            'meuPerfilAPI' => [
                'action' => "meuPerfilAPI",
                "method" => "GET",
                "path" => "/meu_perfil"
            ],
            'getUsuarioByCPF' => [
                'action' => 'getUsuarioByCPF',
                'method' => 'POST',
                'path' => '/get_usuario_by_cpf'
            ],
            'getUsuarioByEmail' => [
                'action' => 'getUsuarioByEmail',
                'method' => 'POST',
                'path' => '/get_usuario_by_email'
            ],
            'registrarAPI' => [
                'action' => 'registrarAPI',
                'method' => 'POST',
                'path' => '/registrar'
            ]
        ]
    ]);

    $routes->resources("Veiculos", [
        "map" => [
            "getVeiculoByIdAPI" => array(
                "action" => "getVeiculoByIdAPI",
                "method" => "POST",
                "path" => "/get_veiculo_by_id"
            ),
            "getVeiculoByPlacaAPI" => [
                "action" => "getVeiculoByPlacaAPI",
                "method" => "POST",
                "path" => "/get_veiculo_by_placa"
            ]
        ]
    ]);

    $routes->resources("UsuariosHasVeiculos", [
        "map" => [
            "getVeiculosUsuarioAPI" => [
                "action" => "getVeiculosUsuarioAPI",
                "method" => "POST",
                "path" => "/get_veiculos_usuario"
            ],
            "setVeiculosUsuarioAPI" => [
                "action" => "setVeiculosUsuarioAPI",
                "method" => "POST",
                "path" => "/set_veiculos_usuario"
            ],
            "deleteVeiculosUsuarioAPI" => [
                "action" => "deleteVeiculosUsuarioAPI",
                "method" => "POST",
                "path" => "/delete_veiculos_usuario"
            ],
            "updateVeiculosUsuarioAPI" => [
                "action" => "updateVeiculosUsuarioAPI",
                "method" => "POST",
                "path" => "/update_veiculos_usuario"
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
