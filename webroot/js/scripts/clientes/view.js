/**
 * Arquivo de funcionalidades do template webroot/view/clientes/view.tpl
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-11
 */

var clientesView = {
    init: async (id) => {
        let self = this;

        // @TODO conferir

        $("#codigo_equipamento_rti").mask("999");
        $("#codigo_equipamento_rti").on('blur', function () {
            var valueCheck = this.value;

            while (valueCheck.length < 3) {
                valueCheck = "0" + valueCheck;
            }
            this.value = valueCheck;
        });

        $("#cnpj").mask("").mask('99.999.999/9999-99');
        $("#cep").mask("").mask("99.999-999");

        $(document)
            .off("blur", "#form #tel-fixo")
            .off("keydown", "#form #tel-fixo")
            .on("blur", "#form #tel-fixo", function () {
                this.value = clientesView.setTelephoneFormat(this.value, 10);
            })
            .on("keyup", function (event) {
                event.target.value = event.target.value.replace(/\D/g, "");
            });
        $("#tel-celular")
            .off("blur", "#form #tel-celular")
            .off("keydown", "#form #tel-celular")
            .on("blur", "#form #tel-celular", function () {
                this.value = clientesView.setTelephoneFormat(this.value, 11);
            })
            .on("keyup", function (event) {
                event.target.value = event.target.value.replace(/\D/g, "");
            });
        $("#tel-fax")
            .off("blur", "#form #tel-fax")
            .off("keydown", "#form #tel-fax")
            .on("blur", "#form #tel-fax", function () {
                this.value = clientesView.setTelephoneFormat(this.value, 10);
            })
            .on("keyup", function (event) {
                event.target.value = event.target.value.replace(/\D/g, "");
            });

        try {
            let response = await clientesServices.getById(id);
        } catch (error) {

        }

        return self;
    },

    fillTimeBoards: function () {
        // @todo conferir
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
    },

    getById: async (id) => {

    },

    setTelephoneFormat: function (value, size) {
        let format = /^(\d{2})(\d{4})(\d{4})/g;

        if (size == 11) {
            format = /^(\d{2})(\d{5})(\d{4})/g;
        }

        value = value.replace(/\D/g, "");
        value = value.substring(0, size);

        return value.replace(format, "($1)$2-$3")
    },
};

// $(document).ready(function () {

//     initializeTimePicker("horario");
//     initializeDatePicker("data_nasc");



//     $("#quantidade_turnos").on("change", function (ev) {
//         var max = $("#quantidade_turnos").attr('max');

//         if (this.value > max) {
//             this.value = max;
//         }

//         preencheQuadroHorarios();
//     });

//     preencheQuadroHorarios();


//     $("#horario").on("blur", function (ev) {
//         preencheQuadroHorarios();
//     });

//     // Dispara atualização de quantidade de turnos se já tiver preenchido
//     $("#quantidade_turnos").blur();
// });
