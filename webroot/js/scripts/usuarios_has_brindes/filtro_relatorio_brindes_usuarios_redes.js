/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\usuarios_has_brindes\filtro_relatorio_brindes_usuarios_redes.js
 * @date 16/03/2018
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
