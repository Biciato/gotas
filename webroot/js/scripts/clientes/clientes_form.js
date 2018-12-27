/**
 * @author Gustavo Souza Gonçalves
 * @date 12/07/2017
 * @
 *
 */

$(document).ready(function () {

    $("#codigo_equipamento_rti").on('blur', function () {
        if (this.value.toString().length == 1) {
            this.value = '0' + this.value;
        }
    });

    $("#cnpj").mask('99.999.999/9999-99');
    $("#tel-fixo").mask("(99)9999-9999");
    $("#tel-celular").mask("(99)99999-9999");
    $("#tel-fax").mask("(99)9999-9999");
    $("#cep").mask("99.999-999");

    initializeDatePicker("data_nasc");

    var preencheQuadroHorarios = function () {
        var horas = $("#horario").val().match(/(\d{2})/gm);

        if (horas != undefined && horas.length > 0) {


            var hora = parseInt(horas[0]);
            var minuto = parseInt(horas[1]);

            var qteTurnos = $("#quantidade_turnos").val();

            var divisao = 24 / qteTurnos;
            var turnos = [];

            var horaTemp = hora;

            for (let i = 0; i < qteTurnos; i++) {

                var turno = {};

                turno.id = i;
                turno.hora = horaTemp.toString().length == 1 ? "0" + horaTemp : horaTemp;
                turno.minuto = minuto.toString().length == 1 ? "0" + minuto : minuto;
                var horaTurno = horaTemp + divisao;
                if (horaTurno > 23) {
                    horaTurno = horaTurno - 24;
                }

                horaTemp = horaTurno;

                turnos.push(turno);
            }

            $(".horariosContent").empty();
            $.each(turnos, function (index, value) {
                $(".horariosContent").append("<strong>Turno " + (value.id + 1) + ": </strong> " + value.hora + ":" + value.minuto + ". <br />");
            });
        }
    }

    $("#quantidade_turnos").on("blur", function (ev) {
        if (this.value > 4) {
            this.value = 4;
        }

        preencheQuadroHorarios();
    });


    $("#horario").on("blur", function (ev) {
        preencheQuadroHorarios();

    });


});
