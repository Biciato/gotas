/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\clientes_has_brindes_habilitados_estoque\filtro_relatorio_estoque_brindes_detalhado.js
 * @date 10/03/2018
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
