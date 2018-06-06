/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\brindes\filtro_brindes_relatorio.js
 * @date 06/03/2018
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