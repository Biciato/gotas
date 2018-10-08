/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\veiculos\filtro_relatorio_veiculos_usuarios_redes.js
 * @date 20/03/2018
 *
 */

$(document).ready(function () {
    // ------------------------------------------------------------------
    // Métodos de inicialização
    // ------------------------------------------------------------------

    $("#placa").mask("AAA9999", {
        'translation': {
            A: {
                pattern: /[A-Za-z]/
            },
            9: {
                pattern: /[0-9]/
            }
        },
        onKeyPress: function (value, event) {
            event.currentTarget.value = value.toUpperCase();
        }
    });
    $("#auditInsertInicio").datetimepicker({
        minView: 2,
        maxView: 4,
        clearBtn: true,
        format: "dd/mm/yyyy"
    });
    $("#auditInsertFim").datetimepicker({
        minView: 2,
        maxView: 4,
        clearBtn: true,
        format: "dd/mm/yyyy"
    });
});
