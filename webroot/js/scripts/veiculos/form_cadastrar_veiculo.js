/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\veiculos\form_cadastrar_veiculos.js
 * @date 24/10/2017
 *
 */

$(document).ready(function () {

    $("#placa").on('keyup', function () {
        $(this).val($(this).val().toUpperCase());
    });

    $("#placa").mask("AAA9999", {
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

    $("#placa").on('blur', function () {
        var data = {
            placa: $(this).val()
        }

        callLoaderAnimation();

        $.ajax({
            type: "POST",
            url: "/api/veiculos/get_veiculo_by_placa",
            data: JSON.stringify(data),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                xhr.setRequestHeader("IsMobile", true);
            },
            error: function (response) {
                console.log(response);
                closeLoaderAnimation();
            }
        }).done(function (result) {
            closeLoaderAnimation();
            if (result.mensagem.status == 0) {
                callModalError(result.mensagem.message, result.mensagem.errors);
            }
            populateVeiculosForm(result.veiculo);
        });
    });

    var populateVeiculosForm = function (data) {
        if (data == null) {
            $(".validation-message").text(null);
            $(".frozen-input-data").attr('disabled', false);

            $("#modelo").focus();
            $("#modelo").val(null);
            $("#fabricante").val(null);
            $("#ano").val(null);
        } else {
            $(".validation-message").text("Veículo existente, será vinculado à este usuário");

            $(".frozen-input-data").attr('disabled', true);
            $("#placa").val(data.placa);
            $("#modelo").val(data.modelo);
            $("#fabricante").val(data.fabricante);
            $("#ano").val(data.ano);
        }
    }
});
