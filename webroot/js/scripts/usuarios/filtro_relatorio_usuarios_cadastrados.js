/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\usuarios\filtro_relatorio_usuarios_cadastrados.js
 * @date 17/03/2018
 * 
 */

$(document).ready(function () {

    // ------------------------------------------------------------------
    // Métodos de inicialização



    // ------------------------------------------------------------------
    // Métodos

    $(".opcoes").on('change', function () {
        $(".user-query-region .parametro").val(null);
        $(".user-query-region .parametro").unmask();
        if (this.value == 'cpf') {
            $(".user-query-region .parametro").mask('999.999.999-99');
        } else if (this.value == 'placa') {
            $(".user-query-region .parametro").mask("AAA9999", {
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
        }
    });

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