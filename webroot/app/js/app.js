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
