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

    initializeDatePicker("dataNascimentoInicio");
    initializeDatePicker("dataNascimentoFim");
    initializeDatePicker("auditInsertInicio");
    initializeDatePicker("auditInsertFim");



});
