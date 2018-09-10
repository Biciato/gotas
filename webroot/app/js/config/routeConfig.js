/**
 * Arquivo de rota para AngularJS
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 07/09/2018
 */
angular.module("GotasApp").config(function ($routeProvider) {
    $routeProvider.when("/relatorios", {
        templateUrl: "/webroot/app/pages/relatorios/usuarios/relatorios.php",
    });
    $routeProvider.when("/relUsuariosAtivos", {
        templateUrl: "/webroot/app/pages/relatorios/usuarios/relUsuariosAtivos.php",
        controller: "relUsuariosAtivos"
    })
    .when("/relUsuariosFidelizados", {
        templateUrl: "/webroot/app/pages/relatorios/usuarios/relUsuariosFidelizados.php",
        controller: "relUsuariosFidelizadosController"
    });
})
