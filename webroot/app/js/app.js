angular.module("GotasApp"
    ,
    [
        "ngRoute",
        "ngSanitize",
        "toastr",
        "ui.bootstrap",
        "ui.mask",
        "ui.select",
        "ngFileSaver"
    ]
)
    // Configuração de http
    .factory('httpRequestInterceptor', function () {
        return {
            request: function (config) {
                config.headers["IsMobile"] = true;
                config.headers["Accept"] = "application/json";
                config.headers["Content-Type"] = "application/json";
                config.headers["ResponseType"] = "arrayBuffer";

                return config;
            }
        }
    })
    .config(function ($httpProvider) {
        $httpProvider.interceptors.push('httpRequestInterceptor');
    })
    // Configurações de componentes
    .config(function (toastrConfig) {
        angular.extend(toastrConfig, {
            autoDismiss: false,
            closeButton: true,
            containerId: 'toast-container',
            maxOpened: 0,
            newestOnTop: true,
            positionClass: 'toast-top-right',
            preventDuplicates: false,
            preventOpenDuplicates: false,
            timeOut: 5000,
            progressBar: true,
            target: 'body'
        });
    });
