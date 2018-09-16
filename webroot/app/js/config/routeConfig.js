/**
 * Arquivo de rota para AngularJS
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 07/09/2018
 */
angular.module("GotasApp").config(function ($routeProvider, $locationProvider) {
    // $routeProvider.when("/relatorios", {
    //     templateUrl: "/webroot/app/pages/relatorios/usuarios/relatorios.php",
    // });
    $routeProvider.when("/usuarios/relatorios/relUsuariosAtivos", {
        templateUrl: "/webroot/app/pages/relatorios/usuarios/relUsuariosAtivos.php",
        controller: "relUsuariosAtivos"
    }
    ).when("/usuarios/relatorios/relUsuariosFidelizados", {
        templateUrl: "/webroot/app/pages/relatorios/usuarios/relUsuariosFidelizados.php",
        controller: "relUsuariosFidelizadosController"
    });

    $locationProvider.html5Mode(true);
    $locationProvider.hashPrefix('');

})
