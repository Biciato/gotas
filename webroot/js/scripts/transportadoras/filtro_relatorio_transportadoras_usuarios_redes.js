/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\transportadoras\filtro_relatorio_transportadoras_usuarios_redes.js
 * @date 20/03/2018
 *
 */

$(document).ready(function () {
    // ------------------------------------------------------------------
    // Métodos de inicialização
    // ------------------------------------------------------------------

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
