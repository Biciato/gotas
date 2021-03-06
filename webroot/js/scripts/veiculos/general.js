$(document).ready(function () {

    var populateData = function (data) {
        if (data != undefined) {
            $(".veiculos #placa").val(data.placa);
            $(".veiculos #modelo").val(data.modelo);
            $(".veiculos #fabricante").val(data.fabricante);
            $(".veiculos #ano").val(data.ano);
            $(".veiculos #placa_validation").text('Registro localizado.');
        }
        else {
            $(".veiculos #modelo").val(null);
            $(".veiculos #fabricante").val(null);
            $(".veiculos #ano").val(null);
            $(".veiculos #placa_validation").text('Registro não localizado, será adicionado novo registro.');
        }
    }
    $(".placa").mask("AAA9B99", {
        'translation': {
            A: {
                pattern: /[A-Za-z]/
            },
            9: {
                pattern: /[0-9]/
            },
            B: {
                pattern: /\D*/
            }
        },
        onKeyPress: function (value, event) {
            event.currentTarget.value = value.toUpperCase();
        }
    });
    $("#ano").mask("9999");
    $(".placa").on('keyup', function () {
        if (this.value.length == 7) {

            callLoaderAnimation();
            $.ajax({
                url: '/api/veiculos/get_veiculo_by_placa',
                type: 'POST',
                data: JSON.stringify({
                    'placa': this.value
                }),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json");
                    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                    xhr.setRequestHeader("IsMobile", true);
                },
                success: function (e) {
                    console.log(e);

                },
                error: function (e) {
                    console.log(e);
                    closeLoaderAnimation();
                }
            }).done(function (result) {
                closeLoaderAnimation();

                if (result.mensagem.status == 0) {
                    callModalError(result.mensagem.message, result.mensagem.errors);
                }
                populateData(result.veiculo);
            });
        }
    });
});
