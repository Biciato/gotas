/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\cupons\reimpressao_shower_modal.js
 * @date 21/12/2017
 *
 */

$(document).ready(function () {

    $(".print-ticket").on('click', function () {
        $(".temporary-info").val(this.attributes['value'].value);

        $("#sexo").val(null);
        $("#necessidades_especiais").val(null);
        $("#current_password").val(null);
    });

    $('#list').on('shown.bs.modal', function () {
        $('#current_password').focus();
    });

    $(".modal-confirm").on('click', function () {

        var sexo = $("#sexo").val();
        var necessidades = $("#necessidades_especiais").val();
        var senha = $("#current_password").val();

        var message = "";

        if (sexo.length == 0) {
            message = "É preciso selecionar o sexo. <br />";
        }

        if (necessidades.length == 0) {
            message += "É preciso informar se possui necessidades especiais. <br />";
        }

        if (senha.length == 0) {
            message += "É preciso que o usuário confirme sua senha.";
        }

        if (message.length > 0) {
            callModalError(message);
        } else {
            callLoaderAnimation();

            var arrayToPrepare = $(".temporary-info").val().split(',');

            var dataToSend = {};
            $.each(arrayToPrepare, function (index, value) {
                var idx = value.substr(0, value.indexOf("="));
                var valueAppend = value.substr(value.indexOf("=") + 1);

                dataToSend[idx] = valueAppend;
            });

            dataToSend['current_password'] = $("#current_password").val();

            if (dataToSend['id'] !== undefined && dataToSend['id'] > 0) {
                $.ajax({
                    type: "POST",
                    url: "/Cupons/reimprimirBrindeAjax",
                    data: JSON.stringify(dataToSend),
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Accept", "application/json");
                        xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                    },
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (response) {
                        console.log(response);
                        closeLoaderAnimation();
                    }
                }).done(function (result) {
                    closeLoaderAnimation();

                    if (result.status == "success") {
                        $("#print_clientes_nome").text(result.cliente.nome_fantasia);
                        $("#print_usuarios_nome").text(result.usuario.nome);

                        var date = new Date();
                        var year = date.getFullYear();
                        var month = date.getMonth();

                        var day = date.getDay();

                        if (month < 10) {
                            month = month + 1;
                        }

                        if (day.toString().length == 1) {
                            day = '0' + day;
                        }

                        if (month.toString().length == 1) {
                            month = '0' + month;
                        }

                        var date_formatted = day + '/' + month + '/' + year;
                        $("#print_data_emissao").text(date_formatted);

                        $("#rti_shower_minutos").text(result.tempo);

                        // data para código de barras
                        var year_code = year.toString().substr(2);
                        year_code = parseInt(year_code) + 10;

                        var month_code = parseInt(month) + 10;
                        var day_code = parseInt(day) + 10;

                        var date_code = year_code.toString() + month_code.toString() + day_code.toString();

                        var cupom_emitido = result.ticket.cupom_emitido;

                        // altera data do cupom emitido

                        var cupom_emitido_1 = cupom_emitido.substr(0, 2);
                        var cupom_emitido_2 = cupom_emitido.substr(6);

                        cupom_emitido = cupom_emitido_1 + date_code + cupom_emitido_2;

                        // altera o tipo de chuveiro (se trocado)

                        cupom_emitido_1 = cupom_emitido.substr(0, 8);
                        cupom_emitido_2 = cupom_emitido.substr(9, 5);

                        var tipo = 0;

                        if (sexo == 1) {
                            $("#tipos_brinde_box").text('Masculino');
                            if (necessidades == 1) {
                                tipo = 2;
                            } else {
                                tipo = 1;
                            }
                        } else {
                            $("#tipos_brinde_box").text('Feminino');
                            if (necessidades == 1) {
                                tipo = 4;
                            } else {
                                tipo = 3;
                            }
                        }

                        var cupom_emitido = cupom_emitido_1 + tipo + cupom_emitido_2;

                        $("#print_barcode_ticket").barcode(cupom_emitido, 'code128', {
                            barWidth: 1,
                            barHeight: 70,
                            showHRI: false,
                            output: 'bmp'
                        });

                        setTimeout($(".print_area").printThis({
                            importCss: false
                        }), 100);
                        $(".reemitir-shower-modal").modal('toggle');
                    } else {
                        callModalError(result.message);
                    }
                });
            }
        }
    });
});
