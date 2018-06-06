/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\pontuacoes_comprovantes\filtro_relatorio_pontuacoes_comprovantes_redes.js
 * @date 15/03/2018
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
