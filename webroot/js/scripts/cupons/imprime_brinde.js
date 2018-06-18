/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\cupons\imprime_brinde.js
 * @date 21/08/2017
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
            dataType: 'json',
            data: {
                usuarios_id: $("#usuarios_id").val(),
                clientes_id: $("#clientes_id").val(),
                _Token: document.cookie.substr(document.cookie.indexOf("csrfToken=") + "csrfToken=".length)
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', document.cookie.substr(document.cookie.indexOf("csrfToken=") + "csrfToken=".length));
            },
            error: function (e) {
                console.log(e);
                closeLoaderAnimation();
            },
            success: function (e) {
                console.log(e.user);
            },
            complete: function (e) {
                closeLoaderAnimation();
                setUsuariosInfo(e.responseJSON.user);
            }
        })
    }

    $(".validation-message").hide();

    $(".brinde-shower.new-user-search").click(function () {
        $(".user-query-region").show();
        setUsuariosInfo(null);
        $(".user-result").hide();
    });

    $("#parametro-brinde").on('keydown', function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }

    });
    $("#parametro-brinde").on('keyup', function (event) {

        if (event.keyCode == 13) {
            searchUsuarioBrindeShower();
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

    $(".brinde-shower.opcoes").on('change', function () {
        $("#parametro-brinde").val(null);
        $("#parametro-brinde").unmask();
        if (this.value == 'cpf') {
            $("#parametro-brinde").mask('999.999.999-99');
        } else if (this.value == 'placa') {
            $("#parametro-brinde").mask("AAA9999", {
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

    $(".brinde-shower.opcoes").change();

    var searchUsuarioBrindeShower = function () {
        $(".user-result").hide();

        callLoaderAnimation();

        var data = {
            parametro: $("#parametro-brinde").val(),
            opcao: $(".brinde-shower .opcoes").val(),
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
                                    setUsuariosInfo(result.usuarios[0]);
                                } else {
                                    setUsuariosInfo(result.usuarios);

                                }
                            } else {
                                setUsuariosInfo(result.usuarios[0]);

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
    $("#search_usuario_brinde_shower").on('click', function () {
        searchUsuarioBrindeShower();
    });

    /**
     * Reseta o Filtro de usuário
     */
    var resetUserFilter = function () {

        // reseta informações de usuário selecionado
        setUsuariosInfo(null);

        // reseta layout
        $(".brinde-shower .user-result").hide();
        $(".brinde-shower .user-query-region").show();
        $("#parametro-brinde").val(null);
        $(".opcoes").val('nome');
    }

    /**
     * Reseta a interface de brinde
     */
    var resetBrindeLayout = function () {
        $(".display-content").hide();
        $(".brinde-shower.user-query-region").show();
        $(".brinde-shower.opcoes").show();
        $("#impressao-rapida-escolha").show();
    }

    /**
     * Seta informações de usuário
     * @param {*} data
     */
    var setUsuariosInfo = function (data) {
        if (data !== undefined && data !== null) {
            $(".usuarios_id_brinde_shower").val(data.id);

            $(".brinde-shower .usuariosNome").val(data.nome);
            $(".brinde-shower .usuariosDataNasc").val(data.data_nasc);

            $("#sexo_brinde_shower").val(data.sexo == true ? 1 : 0);
            $("#necessidades_especiais_brinde_shower").val(data.necessidades_especiais == true ? 1 : 0);

            $(".brinde-shower .usuariosPontuacoes").val(data.pontuacoes);

            $(".user-result").show();
            $(".user-result-names").hide();
            $(".user-result-plates").hide();
            $(".user-query-region").hide();

        } else {
            $(".usuarios_id_brinde_shower").empty();

            $("#usuariosNome").val(null);
            $("#usuariosDataNasc").val(null);
            $("#usuariosPontuacoes").val(null);
            $("#sexo").val(null);
            $("#necessidades_especiais").val(null);
        }
    }
    var resetUserFilter = function () {

        if ($(".usuarios_id_brinde_shower").val() !== "conta_avulsa") {
            $(".usuarios_id_brinde_shower").val(null);
        }

        $("#brindes_id").val(null);

        $("#usuariosNome").val(null);
        $("#usuariosDataNasc").val(null);
        $("#usuariosPontuacoes").val(null);
        $(".list-gifts").val(null);

        $("#current_password").val(null);
        $("#parametro-brinde").val(null);
        $(".user-result").hide();
        $(".user-result-names").hide();
        $(".user-result-plates").hide();
        $(".user-query-region").show();
    }

    var validateBeforePurchase = function () {

        var message = "";
        if ($("#brindes_id").val().length == 0) {
            message += "É necessário selecionar um brinde para continuar. <br />";
        }

        if ($("#usuarios_id_brinde_shower").val().length == 0) {
            message += "Necessário selecionar um cliente para imprimir o ticket. <br />";
        }

        var usuarioIsAvulso = $("#usuarios_id_brinde_shower").val() == "conta_avulsa";
        var senha = $(".current_password").val();

        if (!usuarioIsAvulso) {
            if (senha.length == 0) {
                message += "Necessário cliente informar uma senha para validar cadastro. <br />";
            }
        }

        if (parseFloat($("#preco_banho").val()) > (parseFloat($("#usuariosPontuacoes").val()) * 1000) && usuarioIsAvulso == false) {
            message += "Usuário não tem pontos suficientes para realizar tal resgate";
        }

        if (message.length > 0) {
            callModalError(message);
            return false;
        } else {

            return true;
        }
    };

    var exibirConfirmacaoImpressaoShower = function () {
        $(".container-emissao-cupom-smart").hide();
        $(".container-confirmacao-cupom-smart").show();
    }

    /**
     * Imprime um canhoto
     */
    var imprimirCanhotoShower = function () {
        $(".container-confirmacao-cupom-smart").hide();
        $(".container-confirmacao-canhoto-smart").show();

        var nome = $(".brinde-shower .usuariosNome").val();
        var data = $(".impressao-cupom-shower #print_data_emissao").text();
        var tempo = $(".impressao-cupom-shower #rti_shower_minutos").text();

        $(".impressao-canhoto-shower .print_area .usuarios-nome").text(nome);

        $(".impressao-canhoto-shower .print_area #print_data_emissao").text(data);

        $(".impressao-canhoto-shower .print_area #rti_shower_minutos").text(tempo);

        setTimeout($(".impressao-canhoto-shower .print_area").printThis({
            importCss: false
        }), 100);
    };


    /**
     * Reimprime cupom de banho
     */
    var reimprimirCupomShower = function () {
        setTimeout($(".impressao-cupom-shower .print_area").printThis({
            importCss: false
        }), 100);

    }

    $(".reimpressao-shower").on('click', reimprimirCupomShower);

    $(".imprimir-canhoto-shower").on('click', imprimirCanhotoShower);
    $(".reimpressao-canhoto-shower").on('click', imprimirCanhotoShower);

    $(".print-gift-shower").on('click', function () {

        callLoaderAnimation();

        var result = validateBeforePurchase();

        /**
         * TODO: Impressão de brinde será método único agora.
         * Todo o processo será feito via backend e a tomada de decisão será
         * conforme o tipo de brinde que está sendo impresso.
         * O Brinde também irá dizer qual é seu tipo de impressão conforme
         * o registro habilitado na UNIDADE da Rede.
         *
         */
        if (result) {
            var data = {
                brindes_id: $("#brindes_id").val(),
                clientes_id: $("#clientes_id").val(),
                usuarios_id: $(".usuarios_id_brinde_shower").val(),
                quantidade: $(".quantidade-brindes").val(),
                current_password: $("#current_password").val(),
                _Token: document.cookie.substr(document.cookie.indexOf("csrfToken=") + "csrfToken=".length)
            };
            $.ajax({
                type: "POST",
                url: "/Cupons/imprimeBrindeAjax",
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

                    if (result.isBrindeSmartShower) {
                        // Se for Banho SMART

                        // exibe tudo que é da div de is-cupom-shower

                        $(".is-cupom-shower").show();
                        $(".is-not-cupom-shower").hide();

                        // TODO: Imprimir o cupom de banho

                        $("#print-validation").text(null);
                        $("#print-validation").hide();
                        $("#print_clientes_nome").text(result.cliente.nome_fantasia);
                        $("#print_usuarios_nome").text(result.usuario.nome);
                        $("#print_data_emissao").text(result.ticket.data.substr(0, 10));

                        var cupom_emitido = result.ticket.cupom_emitido;

                        $("#rti_shower_minutos").text(result.tempo);

                        // TODO: Ajustar como é impresso o ticket

                        var tipoEmissaoCodigoBarras = result.tipoEmissaoCodigoBarras;

                        geraCodigoBarras(cupom_emitido, tipoEmissaoCodigoBarras);

                        setTimeout($(".impressao-cupom-shower .print_area").printThis({
                            importCss: false
                        }), 100);

                        exibirConfirmacaoImpressaoShower();
                    }
                    else {

                        // Se for brinde comum

                        // TODO: Terminar o ajuste para impressão comum.
                        popularDadosCupomResgate(result.ticket.cupom_emitido);
                        $(".resgate-cupom-result").show(500);
                        $(".resgate-cupom-main").hide();

                        generateNewPDF417Barcode($(".impressao-cupom-comum .cupom_emitido").val(), 'canvas_origin', 'canvas_destination', 'canvas_img');

                        setTimeout($(".impressao-cupom-comum .print_area").printThis({
                            importCss: false
                        }), 100);


                        // ocultar div de emissão e exibir div de confirmação de impressão
                        exibirConfirmacaoImpressaoComum();

                    }

                    /**
                     * TODO: confirmar se preciso destes métodos conforme abaixo
                     */
                    // resetUserFilter();
                    // resetBrindeLayout();
                    // setUsuariosInfo(undefined);



                } else {
                    callModalError(result.message);
                }
            });
        }
    });

    /**
     * Função que irá gerar o código de barras a ser emitido
     *
     * @param {*} cupom_emitido
     * @param {*} tipoEmissaoCodigoBarras
     */
    var geraCodigoBarras = function (cupom_emitido, tipoEmissaoCodigoBarras) {

        if (tipoEmissaoCodigoBarras == "Code128")
        {

            $("#print_barcode_ticket").barcode(cupom_emitido, 'code128', {
                barWidth: 2,
                barHeight: 70,
                showHRI: false,
                output: 'bmp'
            });
        } else if (tipoEmissaoCodigoBarras == "PDF417"){
            generateNewPDF417Barcode($(".impressao-cupom-comum .cupom_emitido").val(), 'canvas_origin', 'canvas_destination', 'canvas_img');
        } else {
            callModalError("Tipo de Código de Barras ainda não foi configurado no sistema!");
        }
    };
});
