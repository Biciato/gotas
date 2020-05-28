/**
 * @author Gustavo Souza Gonçalves
 * @date 12/07/2017
 * @
 *
 */

$(document).ready(function () {

    $("#codigo_equipamento_rti").mask("999");
    $("#codigo_equipamento_rti").on('blur', function () {
        var valueCheck = this.value;

        while (valueCheck.length < 3) {
            valueCheck = "0" + valueCheck;
        }
        this.value = valueCheck;
    });

    $("#cnpj").mask('99.999.999/9999-99');
    $("#cep").mask("99.999-999");

    $("#tel-fixo")
        .on("blur", function(){
            $("#tel-fixo").mask("(99)9999-9999");
        })
        .on("keyup", function(event){
            this.value = clearNumbers(event.target.value);
        });
    $("#tel-celular")
        .on("blur", function(){
            $("#tel-celular").mask("(99)99999-9999");
        })
        .on("keyup", function(event){
            this.value = clearNumbers(event.target.value);
        });
    $("#tel-fax")
        .on("blur", function(){
            $("#tel-fax").mask("(99)9999-9999");
        })
        .on("keyup", function(event){
        this.value = clearNumbers(event.target.value);
    });

    initializeTimePicker("horario");
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

                turno.proximaHora = horaTurno.toString().length == 1 ? "0" + horaTurno : horaTurno;
                turno.proximaMinuto = minuto.toString().length == 1 ? "0" + minuto : minuto;

                horaTemp = horaTurno;

                turnos.push(turno);
            }

            $(".horariosContent").empty();
            $.each(turnos, function (index, value) {
                $(".horariosContent").append("<strong>Turno " + (value.id + 1) + ": </strong> " + value.hora + ":" + value.minuto + " até " + value.proximaHora + ":" + value.proximaMinuto + ".<br />");
            });
        }
    }

    $("#quantidade_turnos").on("change", function (ev) {
        var max = $("#quantidade_turnos").attr('max');

        if (this.value > max) {
            this.value = max;
        }

        preencheQuadroHorarios();
    });

    preencheQuadroHorarios();


    $("#horario").on("blur", function (ev) {
        preencheQuadroHorarios();
    });

    // Dispara atualização de quantidade de turnos se já tiver preenchido
    $("#quantidade_turnos").blur()
});
