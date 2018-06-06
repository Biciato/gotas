/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\usuarios\filtro_usuarios_ajax.js
 * @date 11/08/2017
 * 
 */

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
            setUsuariosInfo(result.user);
        });
    }

    $(".validation-message").hide();

    $("#new-user-search").click(function () {
        $(".user-query-region").show();
        setUsuariosInfo(null);
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

                value = $("#opcoes").val() == 'placa' ? value.usuario : value;

                if (value.id === id) {
                    result = value;
                    return false;
                }
            });
            setUsuariosInfo(result);
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
                    if (result.user === null) {
                        if ($("#opcoes").val() == 'placa') {

                            callModalError("Veículo não encontrado! Se usuário já está cadastrado, adicione um novo veículo para este usuário.");
                            $("#userValidationMessage").html("Veículo não encontrado! Se usuário já está cadastrado, adicione um novo veículo para este usuário.");
                        } else {
                            callModalError("Cliente não encontrado!");
                        }
                        $(".validation-message").show();
                    } else {
                        $(".validation-message").hide();
                        if (result.count == 1) {
                            if (typeof (result.user === 'object')) {

                                if ($("#opcoes").val() == 'placa') {
                                    setUsuariosInfo(result.user.usuarios_has_veiculos[0].usuario);
                                }
                                //if (($("#opcoes").val() == 'nome') || ($("#opcoes").val() == 'cpf')) {
                                else {
                                    if (result.user.length !== undefined) {
                                        setUsuariosInfo(result.user[0]);
                                    } else {
                                        setUsuariosInfo(result.user);

                                    }
                                }
                            } else {
                                setUsuariosInfo(result.user[0]);

                            }

                        } else if (result.user.length == 0) {
                            callModalError("Não foi(foram) encontrado usuário(s) com o parâmetro fornecido!");
                        } else {

                            arrayUsuarios.set($("#opcoes").val() == 'nome' || $("#opcoes").val() == 'doc_estrangeiro' ? result.user : result.user.usuarios_has_veiculos);

                            $("#user-result-names >tbody").html('');
                            $("#user-result-plates >tbody").html('');

                            if ($("#opcoes").val() == 'nome' || $("#opcoes").val() == 'doc_estrangeiro') {
                                $.each(result.user, function (index, value) {

                                    var html = "<tr><td>" + value.nome + "</td><td>" + value.data_nasc + "</td><td>" + "<div class='btn btn-primary btn-xs select-button' value='" + value.id + "'><i class='fa fa-check-circle-o'></i> Selecionar</div>" + "</td></tr>";

                                    $("#user-result-names ").append(html);
                                });
                            } else {
                                $("#veiculosPlaca").val(result.user.placa);
                                $("#veiculosModelo").val(result.user.modelo);
                                $("#veiculosFabricante").val(result.user.fabricante);
                                $("#veiculosAno").val(result.user.ano);
                                $.each(result.user.usuarios_has_veiculos, function (index, value) {

                                    var value = value.usuario;
                                    var html = "<tr><td>" + value.nome + "</td><td>" + value.data_nasc + "</td><td>" + "<div class='btn btn-primary btn-xs select-button' value='" + value.id + "'><i class='fa fa-check-circle-o'></i> Selecionar</div>" + "</td></tr>";
                                    $("#user-result-plates ").append(html);

                                });
                            }

                            if ($("#opcoes").val() == 'nome' || $("#opcoes").val() == 'doc_estrangeiro') {
                                $(".user-result-names").show();
                            } else {
                                $(".user-result-plates").show();
                            }
                            initializeSelectClicks();
                        }
                    }
                }
            }).fail(function (e) {
                console.log("error");

                if (e.responseJSON != undefined) {
                    callModalError(e.responseJSON.message);
                }
                callModalError("Houve um erro ao buscar o(s) usuário(s). Informe o suporte.");

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
    setUsuariosInfo(null);

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
var setUsuariosInfo = function (data) {
    if (data !== undefined && data !== null) {
        $("#usuarios_id").val(data.id);
        $("#usuariosNome").val(data.nome);
        $("#usuariosDataNasc").val(data.data_nasc);

        $("#sexo").val(data.sexo == true ? 1 : 0);
        $("#necessidades_especiais").val(data.necessidades_especiais == true ? 1 : 0);

        // if (data.pontuacoes.indexOf('.') > 0) {
        //     $("#usuariosPontuacoes").val(parseFloat(data.pontuacoes) * 1000);
        // } else {
        //     $("#usuariosPontuacoes").val(parseFloat(data.pontuacoes));
        // }

        $("#usuariosPontuacoes").val(data.pontuacoes);

        $(".user-result").show();
        $(".user-result-names").hide();
        $(".user-result-plates").hide();
        $(".user-query-region").hide();

    } else {
        $("#usuarios_id").val(null);
        $("#usuariosNome").val(null);
        $("#usuariosDataNasc").val(null);
        $("#usuariosPontuacoes").val(null);
        $("#sexo").val(null);
        $("#necessidades_especiais").val(null);
    }
}
