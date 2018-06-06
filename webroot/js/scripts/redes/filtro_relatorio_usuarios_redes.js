/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\clientes\filtro_relatorio_usuarios_redes.js
 * @date 18/03/2018
 *
 */

$(document).ready(function() {
    // ------------------------------------------------------------------
    // Métodos de inicialização
    // ------------------------------------------------------------------

    $("#dataNascimentoInicio").datetimepicker({
        minView: 2,
        maxView: 4,
        clearBtn: true,
        format: "dd/mm/yyyy"
    });
    $("#dataNascimentoFim").datetimepicker({
        minView: 2,
        maxView: 4,
        clearBtn: true,
        format: "dd/mm/yyyy"
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
