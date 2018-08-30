/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\usuarios\filtro_usuarios_ajax.js
 * @date 11/08/2017
 *
 */

var contaAvulsa = $("#usuarios_id").val() == "conta_avulsa";

$(document).ready(function () {


    // ------------------------------------------------------------------
    // Métodos de inicialização

    // se há valor, significa que foi filtrado novamente (post de filtro)
    if ($("#usuarios_id").val() !== undefined && $("#usuarios_id").val().length > 0) {

        callLoaderAnimation();

        $.ajax({
            url: '/Usuarios/findUsuarioById',
            type: 'POST',
            data: JSON.stringify({
                usuarios_id: $("#usuarios_id").val(),
                clientes_id: $("#clientes_id").val(),
                _Token: document.cookie.substr(document.cookie.indexOf("csrfToken=") + "csrfToken=".length)
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            },
            error: function (e) {
                console.log(e);
                closeLoaderAnimation();
            },
            success: function (e) {
                console.log(e.user);
            }
        }).done(function (result) {
            closeLoaderAnimation();
            setUsuariosInfo(result.user, contaAvulsa);
        });
    }

    $(".validation-message").hide();

    $("#new-user-search").click(function () {
        $(".user-query-region").show();
        setUsuariosInfo(null, contaAvulsa);
        $(".user-result").hide();
    });

    $(".user-query-region").find(".parametro").on('keydown', function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }

    })
    $(".user-query-region").find(".parametro").on('keyup', function (event) {
        if (event.keyCode == 13) {
            searchUsuario();
        }
    });

    var initializeSelectClicks = function () {
        $(".select-button").on('click', function (data) {

            var a = arrayUsuarios.get();
            var id = parseInt($(this).attr('value'));
            var result = null;

            $.each(a, function (index, value) {

                if (value.id === id) {
                    result = value;
                    return false;
                }
            });
            setUsuariosInfo(result, contaAvulsa);
        });
    }

    // ------------------------------------------------------------------
    // Propriedades

    var arrayUsuarios = {
        array: [],
        get: function () {
            return this.array;
        },
        set: function (array) {
            this.array = array;
        }
    };

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

    $(".opcoes").change();

    var searchUsuario = function () {
        $(".user-result").hide();

        callLoaderAnimation();

        var data = {
            parametro: $(".user-query-region .parametro").val(),
            opcao: $("#opcoes").val(),
            clientes_id: $("#clientes_id").val(),
            restrict_query: $("#restrict_query").length > 0 ? $("#restrict_query").val() : null,
            _Token: document.cookie.substr(document.cookie.indexOf("csrfToken=") + "csrfToken=".length)

        };

        if (data.parametro.length <= 3) {
            callModalError("O tamanho do parâmetro deve ser maior ou igual a 3 dígitos");
        } else {
            $.ajax({
                url: '/Usuarios/findUsuario',
                type: 'POST',
                data: JSON.stringify(data),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json");
                    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                },
                error: function (e) {
                    console.log(e);
                    closeLoaderAnimation();

                },
                success: function (e) {
                    console.log(e.user);
                }
            }).done(function (result) {

                closeLoaderAnimation();

                if (result.error) {
                    callModalError(result.message);
                } else {

                    if ($("#opcoes").val() == "placa" && result.veiculoEncontrado === null) {
                        callModalError("Veículo não encontrado! Se usuário já está cadastrado, adicione um novo veículo para este usuário.");
                        return;
                    }
                    if (result.usuarios === null) {

                        callModalError("Cliente não encontrado!");
                        return;

                    } else {
                        $(".validation-message").hide();
                        if (result.count == 1) {
                            if (typeof (result.usuarios === 'object')) {
                                if (result.usuarios.length !== undefined) {
                                    setUsuariosInfo(result.usuarios[0], contaAvulsa);
                                } else {
                                    setUsuariosInfo(result.usuarios, contaAvulsa);

                                }
                            } else {
                                setUsuariosInfo(result.usuarios[0], contaAvulsa);

                            }

                        } else if (result.usuarios.length == 0) {
                            callModalError("Não foi(foram) encontrado usuário(s) com o parâmetro fornecido!");
                        } else {

                            arrayUsuarios.set(result.usuarios);

                            $("#user-result-names >tbody").html('');
                            $("#user-result-plates >tbody").html('');

                            if (result.veiculoEncontrado) {
                                var veiculo = result.veiculoEncontrado;
                                $("#veiculosPlaca").val(veiculo.placa);
                                $("#veiculosModelo").val(veiculo.modelo);
                                $("#veiculosFabricante").val(veiculo.fabricante);
                                $("#veiculosAno").val(veiculo.ano);

                                $(".user-result-plates").show();

                                $.each(result.usuarios, function (index, value) {

                                    var html = "<tr><td>" + value.nome + "</td><td>" + value.data_nasc + "</td><td>" + "<div class='btn btn-primary btn-xs select-button' value='" + value.id + "'><i class='fa fa-check-circle-o'></i> Selecionar</div>" + "</td></tr>";
                                    $("#user-result-plates ").append(html);
                                });
                            }
                            else {
                                $.each(result.usuarios, function (index, value) {

                                    var html = "<tr><td>" + value.nome + "</td><td>" + value.data_nasc + "</td><td>" + "<div class='btn btn-primary btn-xs select-button' value='" + value.id + "'><i class='fa fa-check-circle-o'></i> Selecionar</div>" + "</td></tr>";

                                    $("#user-result-names ").append(html);

                                });
                                $(".user-result-names").show();
                            }

                            initializeSelectClicks();
                        }
                    }
                }
            }).fail(function (e) {
                console.log("error" + e.responseJSON.message);
            }).always(function (e) {
                console.log("complete");
            });
        }
    }
    $("#searchUsuario").on('click', function () {
        searchUsuario();
    });
});

/**
 * Reseta o Filtro de usuário
 */
var resetUserFilter = function () {

    // reseta informações de usuário selecionado
    setUsuariosInfo(null, contaAvulsa);

    // reseta layout
    $(".user-result").hide();
    $(".user-query-region").show();
    $(".user-query-region .parametro").val(null);
    $("#opcoes").val('nome');
}

/**
 * Seta informações de usuário
 * @param {*} data
 */
var setUsuariosInfo = function (data, contaAvulsa) {
    if (data !== undefined && data !== null) {
        $("#usuarios_id").val(data.id);
        $("#usuariosNome").val(data.nome);
        $("#usuariosDataNasc").val(data.data_nasc);

        $("#sexo").val(data.sexo == true ? 1 : 0);
        $("#necessidades_especiais").val(data.necessidades_especiais == true ? 1 : 0);

        $("#usuariosPontuacoes").val(data.pontuacoes);

        $(".user-result").show();
        $(".user-result-names").hide();
        $(".user-result-plates").hide();
        $(".user-query-region").hide();

    } else {

        $("#usuarios_id").val(contaAvulsa ? "conta_avulsa" : null);

        $("#usuariosNome").val(null);
        $("#usuariosDataNasc").val(null);
        $("#usuariosPontuacoes").val(null);
        $("#sexo").val(null);
        $("#necessidades_especiais").val(null);
        $("#current_password").val(null);
    }
}
