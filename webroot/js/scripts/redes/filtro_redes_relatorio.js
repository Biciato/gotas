/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\clientes\filtro_redes_relatorio.js
 * @date 28/02/2018
 *
 */

$(document).ready(function() {
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
