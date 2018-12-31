/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\cupons\imprime_brinde_shower.js
 * @date 21/08/2017
 *
 */

$(document).ready(function () {

    // ------------------------------------------------------------------
    // Métodos de inicialização

    // se há valor, significa que foi filtrado novamente (post de filtro)
    if ($(".brinde-comum-container #usuarios_id").val() !== undefined && $(".brinde-comum-container #usuarios_id").val().length > 0) {

        callLoaderAnimation();

        $.ajax({
            url: '/Usuarios/findUsuarioById',
            type: 'POST',
            data: JSON.stringify({
                usuarios_id: $("#usuarios_id").val(),
                clientes_id: $("#clientes_id").val(),
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
        })
    }

    $(".validation-message").hide();

    $(".brinde-comum.new-user-search").click(function () {
        $(".user-query-region").show();
        setUsuariosInfo(null);
        $(".user-result").hide();
    });

    $(".brinde-comum-container .parametro-brinde-comum").on('keydown', function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    $(".brinde-comum-container .parametro-brinde-comum").on('keyup', function (event) {
        if (event.keyCode == 13) {
            searchUsuarioBrindeComum();
        }
    });

    var initializeSelectClicks = function () {
        $(".select-button").on('click', function (data) {
            var a = arrayUsuarios.get();
            var id = parseInt($(this).attr('value'));
            var result = null;

            $.each(a, function (index, value) {
                value = $(".opcoes").val() == 'placa' ? value.usuario : value;

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

    $(".brinde-comum.opcoes").on('change', function () {
        $(".brinde-comum-container .parametro-brinde-comum").val(null);
        $(".brinde-comum-container .parametro-brinde-comum").unmask();
        if (this.value == 'cpf') {
            $(".brinde-comum-container .parametro-brinde-comum").mask('999.999.999-99');
        } else if (this.value == 'placa') {
            $(".brinde-comum-container .parametro-brinde-comum").mask("AAA9999", {
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

    $(".brinde-comum.opcoes").change();

    var searchUsuarioBrindeComum = function () {
        $(".brinde-comum-container.user-result").hide();

        callLoaderAnimation();

        var data = {
            parametro: $(".brinde-comum-container .parametro-brinde-comum").val(),
            opcao: $(".brinde-comum-container .opcoes").val(),
            clientes_id: $(".brinde-comum-container #clientes_id").val(),
            restrict_query: $(".brinde-comum-container #restrict_query").length > 0 ? $(".brinde-comum-container #restrict_query").val() : null,
            _Token: document.cookie.substr(document.cookie.indexOf("csrfToken=") + "csrfToken=".length)
        };

        if (data.parametro.length < 3) {
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
                        if ($(".container-emissao-cupom-comum #opcoes").val() == 'placa') {
                            $("#userValidationMessage").html("Veículo não encontrado! Se usuário já está cadastrado, adicione um novo veículo para este usuário.");
                        } else {
                            $("#userValidationMessage").html("Cliente não encontrado!");
                        }
                        $(".validation-message").show();
                    } else {
                        $(".validation-message").hide();
                        if (result.count == 1) {
                            if (typeof (result.user === 'object')) {

                                if ($(".container-emissao-cupom-comum #opcoes").val() == 'placa') {
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
                            arrayUsuarios.set($(".container-emissao-cupom-comum #opcoes").val() == 'nome' || $(".container-emissao-cupom-comum #opcoes").val() == 'doc_estrangeiro' ? result.user : result.user.usuarios_has_veiculos);

                            $(".container-emissao-cupom-comum #user-result-names >tbody").html('');
                            $(".container-emissao-cupom-comum #user-result-plates >tbody").html('');

                            if ($(".container-emissao-cupom-comum #opcoes").val() == 'nome' || $("#opcoes").val() == 'doc_estrangeiro') {
                                $.each(result.user, function (index, value) {

                                    var html = "<tr><td>" + value.nome + "</td><td>" + value.data_nasc + "</td><td>" + "<div class='btn btn-primary btn-xs select-button' value='" + value.id + "'><i class='fa fa-check-circle-o'></i> Selecionar</div>" + "</td></tr>";

                                    $(".container-emissao-cupom-comum #user-result-names ").append(html);
                                });
                            } else {
                                $(".container-emissao-cupom-comum #veiculosPlaca").val(result.user.placa);
                                $(".container-emissao-cupom-comum #veiculosModelo").val(result.user.modelo);
                                $(".container-emissao-cupom-comum #veiculosFabricante").val(result.user.fabricante);
                                $(".container-emissao-cupom-comum #veiculosAno").val(result.user.ano);
                                $.each(result.user.usuarios_has_veiculos, function (index, value) {

                                    var value = value.usuario;
                                    var html = "<tr><td>" + value.nome + "</td><td>" + value.data_nasc + "</td><td>" + "<div class='btn btn-primary btn-xs select-button' value='" + value.id + "'><i class='fa fa-check-circle-o'></i> Selecionar</div>" + "</td></tr>";
                                    $("#user-result-plates ").append(html);

                                });
                            }

                            if ($(".container-emissao-cupom-comum #opcoes").val() == 'nome' || $("#opcoes").val() == 'doc_estrangeiro') {
                                $(".container-emissao-cupom-comum .user-result-names").show();
                            } else {
                                $(".container-emissao-cupom-comum .user-result-plates").show();
                            }

                            initializeSelectClicks();
                        }
                    }
                }
            }).fail(function (e) {
                console.log("error");
            }).always(function (e) {
                console.log("complete");
            });
        }
    }
    $(".brinde-comum-container #search_usuario_brinde_comum").on('click', function () {
        searchUsuarioBrindeComum();
    });

    /**
     * Reseta o Filtro de usuário
     */
    var resetUserFilter = function () {

        // reseta informações de usuário selecionado
        setUsuariosInfo(null);

        // reseta layout
        $(".brinde-comum-container .brinde-comum .user-result").hide();
        $(".brinde-comum-container .brinde-comum .user-query-region").show();
        $(".brinde-comum-container #parametro-brinde").val(null);
        $(".brinde-comum-container .opcoes").val('nome');
    }

    /**
     * Seta informações de usuário
     * @param {*} data
     */
    var setUsuariosInfo = function (data) {
        if (data !== undefined && data !== null) {
            $(".brinde-comum-container .usuarios-id-brinde-comum").val(data.id);

            $(".brinde-comum .usuariosNome").val(data.nome);
            $(".brinde-comum .usuariosDataNasc").val(data.data_nasc);

            $(".brinde-comum .usuariosPontuacoes").val(data.pontuacoes);

            $(".brinde-comum-container .user-result").show();
            $(".brinde-comum-container .user-result-names").hide();
            $(".brinde-comum-container .user-result-plates").hide();
            $(".brinde-comum-container .user-query-region").hide();

        } else {
            $(".brinde-comum-container .usuarios-id-brinde-comum").val(null);

            $(".brinde-comum .usuariosNome").val(null);
            $(".brinde-comum .usuariosDataNasc").val(null);

            $(".brinde-comum .usuariosPontuacoes").val(null);

        }
    }

    /**
     * Reseta o Filtro de usuário
     */
    var resetUserFilter = function () {

        $("brinde-comum-container .usuarios-id-brinde-comum").val(null);

        $(".brinde-comum .opcoes").val('nome');
        $(".brinde-comum .usuariosNome").val(null);
        $(".brinde-comum .usuariosDataNasc").val(null);
        $(".brinde-comum .usuariosPontuacoes").val(null);
        $(".list-gifts-comum").val(null);

        $(".brinde-comum-container #current_password").val(null)
        $(".brinde-comum-container .parametro-brinde-comum").val(null);
        $(".brinde-comum-container .list-gifts-comum").val(null);
        $(".brinde-comum-container .quantidade-brindes").val(null);


        $(".brinde-comum-container .user-result").hide();
        $(".brinde-comum-container .user-result-names").hide();
        $(".brinde-comum-container .user-result-plates").hide();
        $(".brinde-comum-container .user-query-region").hide();
    }

    /**
     * Reseta a interface de brinde
     */
    var resetBrindeLayout = function () {

        $(".display-content").hide();
        $(".brinde-comum.user-query-region").show();
        $(".brinde-comum.opcoes").show();
        $(".brinde-comum.user-result").hide();
        $("#impressao-rapida-escolha").show();
    }

    /**
     * Valida informações de form antes de fazer a compra
     */
    var validateBeforePurchase = function () {

        var message = "";

        if ($(".brinde-comum-container #usuarios_id_brinde_comum").val().length == 0) {
            message += "Selecione um cliente para imprimir o ticket. <br />";
        }

        if ($(".brinde-comum-container .list-gifts-comum").val().length == 0) {
            message += "Selecione um brinde para continuar. <br />";
        }

        if ($(".brinde-comum-container .quantidade-brindes").val() == 0) {
            message += "Quantidade deve ser maior que 0 (zero) para continuar. <br />";
        }

        var usuarioIsAvulso = $(".brinde-comum-container #usuarios_id_brinde_comum").val();

        var senha = $(".brinde-comum-container .current_password").val();

        if (!usuarioIsAvulso) {
            if (senha.length == 0) {
                message += "Necessário cliente informar uma senha para validar cadastro. <br />";
            }
        }
        if (parseFloat($("#preco_banho").val()) > (parseFloat($("#usuariosPontuacoes").val()) * 1000)) {
            message += "Usuário não tem pontos suficientes para realizar tal resgate";
        }

        if (message.length > 0) {
            callModalError(message);
            return false;
        } else {
            return true;
        }
    };

    $(".brinde-comum-container .print-gift-comum").on('click', function () {
        geraBrindeComum();
    });

    var exibirConfirmacaoImpressaoComum = function () {
        $(".container-emissao-cupom-comum").hide();
        $(".container-confirmacao-cupom-comum").show();
    }

    var reimprimirBrindeComum = function(){
        setTimeout($(".impressao-cupom-comum .print_area").printThis({
            importCss: false
        }), 100);
    };

    $(".container-confirmacao-cupom-comum .reimpressao-comum").on('click', reimprimirBrindeComum);

    /**
     * Gera o brinde no sistema, e depois o obtêm para impressão
     */
    var geraBrindeComum = function () {
        callLoaderAnimation();

        var result = validateBeforePurchase();

        if (result) {
            var data = {
                brindes_id: $(".brinde-comum-container .list-gifts-comum").val(),
                clientes_id: $(".brinde-comum-container #clientes_id").val(),
                usuarios_id: $(".brinde-comum-container #usuarios_id_brinde_comum").val(),
                quantidade: 1,
                current_password: $(".brinde-comum-container #current_password").val(),
                _Token: document.cookie.substr(document.cookie.indexOf("csrfToken=") + "csrfToken=".length)
            };
            $.ajax({
                type: "POST",
                url: "/Cupons/geraBrindeComumAjax",
                data: JSON.stringify(data),
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

                    var cupom_emitido = result.ticket.cupom_emitido;

                    // Dados foram gravados com sucesso.
                    // Chama impressão de dados

                    imprimeBrindeComum(cupom_emitido);

                    resetUserFilter();
                } else {
                    // Erro: exibe mensagem
                    callModalError(result.message);
                }
            });
        }
    };

    /**
     * Imprime o brinde conforme cupom informado
     * @param {string} cupom_emitido
     */
    var imprimeBrindeComum = function (cupom_emitido) {
        var data = {
            cupom_emitido: cupom_emitido
        };

        $.ajax({
            type: "POST",
            url: "/Cupons/getCupomPorCodigo",
            data: JSON.stringify(data),
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
            console.log(result);

            closeLoaderAnimation();
            if (!result.status) {
                callModalError(result.message);
            } else {

                popularDadosCupomResgate(result.data);
                $(".resgate-cupom-result").show(500);
                $(".resgate-cupom-main").hide();

                generateNewPDF417Barcode($(".impressao-cupom-comum .cupom_emitido").val(), 'canvas_origin', 'canvas_destination', 'canvas_img');

                setTimeout($(".impressao-cupom-comum .print_area").printThis({
                    importCss: false
                }), 100);

                // resetUserFilter();
                // resetBrindeLayout();
                // setUsuariosInfo(undefined);

                // ocultar div de emissão e exibir div de confirmação de impressão
                exibirConfirmacaoImpressaoComum();


            }
        });
    }

    /**
     * Inicializa seleção de cliques
     */
    var initializeSelectClicks = function () {
        $(".select-button").on('click', function (data) {

            var a = arrayBrindes.get();

            var id = parseInt($(this).attr('value'));

            var result = $.grep(a, function (value, index) {

                if (value.id === id)
                    return value;

            });

            setBrindesInfo(result[0]);

        });
    }

    var arrayBrindes = {
        array: [],
        get: function () {
            return this.array;
        },
        set: function (array) {
            this.array = array;
        }
    };

    var searchBrindeComum = function () {

        callLoaderAnimation();
        $.ajax({
            url: '/Brindes/findBrindes',
            type: 'POST',
            dataType: 'json',
            data: JSON.stringify({
                parametro_brinde: $("#parametro_brinde").val(),
                clientes_id: $("#clientes_id").val(),
                equipamento_rti_shower: 0
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
                console.log(e);
            }
        }).done(function (result) {

            closeLoaderAnimation();
            if (result.brindes === null || result.brindes.length == 0) {
                callModalError("Não foram encontrados brindes comuns! Tem brindes comuns cadastrados em sua loja?");
                // $(".brinde-comum-container #giftsValidationMessage").val("Brinde não encontrado! Tem brindes cadastrados em sua loja?");
            } else {
                arrayBrindes.set(result.brindes);
                $(".brinde-comum-container .list-gifts-comum").append($('<option>'));
                $.each(result.brindes, function (index, value) {

                    $(".brinde-comum-container .list-gifts-comum").append($('<option>', {
                        value: value.id,
                        text: value.brinde.nome + " - Preço: " + value.brinde_habilitado_preco_atual.preco
                    }));

                });
            }

        }).fail(function (e) {
            console.log("error");
        }).always(function (e) {
            console.log("complete");
        });
    };

    searchBrindeComum();
});
