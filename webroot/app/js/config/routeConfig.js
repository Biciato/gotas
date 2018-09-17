/**
 * Arquivo de rota para AngularJS
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 07/09/2018
 */
angular.module("GotasApp").config(function ($routeProvider, $locationProvider) {
    // $routeProvider.when("/relatorios", {
    //     templateUrl: "/webroot/app/pages/relatorios/usuarios/relatorios.php",
    // });
    $routeProvider
        .when("/usuarios/relatorios/relUsuariosAssiduos", {
            templateUrl: "/webroot/app/pages/relatorios/usuarios/relUsuariosAssiduos.php",
            controller: "relUsuariosAssiduosController"
        })
        .when("/usuarios/relatorios/relUsuariosFidelizados", {
            templateUrl: "/webroot/app/pages/relatorios/usuarios/relUsuariosFidelizados.php",
            controller: "relUsuariosFidelizadosController"
        })
        ;

    $locationProvider.html5Mode(true);
    $locationProvider.hashPrefix('');

})
