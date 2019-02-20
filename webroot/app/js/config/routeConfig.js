/**
 * Arquivo de rota para AngularJS
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 07/09/2018
 */
// angular.module("GotasApp").config(function ($routeProvider, $locationProvider) {
'use strict';
angular.module("GotasApp").config(function ($routeProvider) {
    $routeProvider
        .when("/relatorios", {
            templateUrl: "/webroot/app/pages/relatorios/usuarios/relatorios.php",
        })
        .when("/relUsuariosFrequenciaMedia", {
            templateUrl: "/webroot/app/pages/relatorios/usuarios/relUsuariosFrequenciaMedia.php",
            controller: "relUsuariosFrequenciaMediaController"

        })
        // $routeProvider
        // .when("!#/usuarios/relatorios/relUsuariosAssiduos", {
        .when("/relUsuariosAssiduos", {
            templateUrl: "/webroot/app/pages/relatorios/usuarios/relUsuariosAssiduos.php",
            controller: "relUsuariosAssiduosController"
        })
        // .when("/usuarios/relatorios/relUsuariosFidelizados", {
        .when("/relUsuariosFidelizados", {
            templateUrl: "/webroot/app/pages/relatorios/usuarios/relUsuariosFidelizados.php",
            controller: "relUsuariosFidelizadosController"
        })
        ;

    // colocar quando migrar tudo para angular
    // $locationProvider.html5Mode(true);
    // $locationProvider.hashPrefix('');

})
