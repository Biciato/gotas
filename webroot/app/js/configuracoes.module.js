'use strict';

(function () {

    var ENVIRONMENT = {
        PRODUCTION: "sistema",
        DEVEL: "sistema-devel",

    }

    var configuracoesModule = angular.module("configuracoesModule", []);

    configuracoesModule.constant("APP_CONFIG", {
        PROFILE_TYPES: {
            ADMIN_DEVEL: 0,
            ADMIN_NETWORK: 1,
            ADMIN_REGIONAL: 2,
            ADMIN_LOCAL: 3,
            MANAGER: 4,
            WORKER: 5,
            USER: 6,
            DUMMY_WORKER: 998,
            DUMMY_USER: 999,
        }
        ,
        ENVIRONMENT: ENVIRONMENT
    })

    ;



})();
