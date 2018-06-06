/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\clientes_has_brindes_habilitados_preco\filtro_relatorio_historico_preco_brindes_redes_detalhado.js
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
