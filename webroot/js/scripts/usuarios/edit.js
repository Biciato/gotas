/**
 * @author Gustavo Souza Gonçalves
 * @date 12/07/2017
 * @
 *
 */

$(document).ready(function () {
        $("#cpf").mask("###.###.###-##");

        var imageStored = false;

        var startScanDocument = function () {
            $(".group-video-capture").show();

            var video = document.querySelector("#video");
            var photo = document.querySelector("#photoTaken");

            var canvas = $("#canvas")[0];
            var canvasContext = canvas.getContext("2d");

            navigator.getUserMedia =
                navigator.getUserMedia ||
                navigator.webkitGetUserMedia ||
                navigator.mozGetUserMedia ||
                navigator.msGetUserMedia ||
                navigator.oGetUserMedia;

            if (navigator.getUserMedia) {
                navigator.getUserMedia({
                        video: true,
                        audio: false
                    },
                    handleVideo,
                    videoError
                );
            }

            function handleVideo(stream) {
                window.localStream = stream;
                video.src = window.URL.createObjectURL(stream);
            }

            function videoError(e) {
                // do something
            }

            $("#takeSnapshot").click(function () {
                canvasContext.drawImage(video, 0, 0, 400, 300);
            });

            $("#storeImage").click(function () {
                var message = "";
                var messageValidation = "";

                if ($("#alternarEstrangeiro")[0].checked) {
                    if ($("#doc_estrangeiro").val().length == 0) {
                        messageValidation =
                            "Documento de Identificação Estrangeira";
                    }
                } else {
                    if ($("#cpf").val().length < 14) {
                        messageValidation = "CPF";
                    }
                }

                if (messageValidation.length > 0) {
                    message =
                        "Campo precisa estar preenchido para continuar: " +
                        messageValidation;
                    callModalError(message);
                } else {
                    var resizedCanvas = document.createElement("canvas");
                    var resizedContext = resizedCanvas.getContext("2d");

                    resizedCanvas.height = "768";
                    resizedCanvas.width = "1024";
                    resizedContext.drawImage(canvas, 0, 0, 1024, 768);

                    var nameImage =
                        $("#cpf").val().length == 0 ?
                        $("#doc_estrangeiro").val() :
                        $("#cpf").val();

                    if (!$("#doc_invalido").val().length > 0) {
                        nameImage = cleanIdentity(nameImage);
                    }

                    callLoaderAnimation();
                    $.ajax({
                        url: "/Usuarios/uploadDocumentTemporary",
                        type: "post",
                        data: JSON.stringify({
                            image: resizedCanvas.toDataURL("image/jpeg"),
                            imageName: nameImage
                        }),
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader("Accept", "application/json");
                            xhr.setRequestHeader(
                                "Content-Type",
                                "application/json; charset=UTF-8"
                            );
                        },
                        success: function (e) {
                            $("#cpf_validation").val(
                                "Imagem de Documento enviado com sucesso."
                            );
                            $(".group-video-capture").hide();
                            $("#doc_invalido").val(true);
                        },
                        error: function (e) {
                            window.alert(
                                "Houve um erro, por favor tente novamente."
                            );
                            console.log(e);

                            closeLoaderAnimation();
                        },
                        complete: function (e) {
                            $("#cpf_validation").text(
                                "Imagem armazenada no servidor."
                            );
                            $("#doc_estrangeiro_validation").text(
                                "Imagem armazenada no servidor."
                            );
                            stopScanDocument();
                            $("#user_submit").attr("disabled", false);

                            closeLoaderAnimation();

                            // atribui como true a imagem enviada
                            imageStored = true;

                            // a imagem foi armazenada, então o CPF, mesmo incorreto, está vinculado à imagem.
                            $("#cpf").attr("disabled", true);
                        }
                    });
                }
            });
        };

        var stopScanDocument = function () {
            if (window.localStream !== undefined) {
                window.localStream.getVideoTracks()[0].stop();
            }

            $(".group-video-capture").hide();
        };

        $("#alternarEstrangeiro").click(function () {
            $("#cpf_box").toggle();
            $("#doc_estrangeiro_box").toggle();
        });

        if ($("#alternarEstrangeiro").checked == true) {
            $("#cpf_box").hide();
            $("#doc_estrangeiro_box").show();
            // $("#doc_estrangeiro_validation").text(
            //     "É necessário capturar uma cópia do documento para posterior aprovação."
            // );
            // startScanDocument();
        } else {
            $("#cpf_box").show();
            $("#doc_estrangeiro_box").hide();
            // $("#doc_estrangeiro_validation").text(
            //     "É necessário capturar uma cópia do documento para posterior aprovação."
            // );
            // stopScanDocument();
        }

        var occurrencesInvalidCpf = 0;
        var previousCPF = "";

        /**
         * Remove qualquer caracter especial
         * @param {object} documentUser
         */
        var cleanIdentity = function (parameter) {
            var returnValue = parameter.replace(/\./g, "");
            returnValue = returnValue.replace(/\-/g, "");
            return returnValue;
        };

        /**
         * Verifica se CPF é válido
         * @param {*} strCPF
         */
        var checkCPFIsValid = function (strCPF) {
            var sum;
            var rest;
            sum = 0;
            if (strCPF == "00000000000") {
                return false;
            }

            for (i = 1; i <= 9; i++) {
                sum = sum + parseInt(strCPF.substring(i - 1, i)) * (11 - i);
            }

            rest = (sum * 10) % 11;

            if (rest == 10 || rest == 11) {
                rest = 0;
            }

            if (rest != parseInt(strCPF.substring(9, 10))) {
                return false;
            }

            sum = 0;
            for (i = 1; i <= 10; i++) {
                sum = sum + parseInt(strCPF.substring(i - 1, i)) * (12 - i);
            }

            rest = (sum * 10) % 11;

            if (rest == 10 || rest == 11) {
                rest = 0;
            }
            if (rest != parseInt(strCPF.substring(10, 11))) {
                return false;
            }

            return true;
        };

        /**
         * Verifica se há CPF repetido no servidor
         */
        var checkCPFRepeated = function () {
            callLoaderAnimation("Verificando CPF...");

            $.ajax({
                url: "/Usuarios/getUsuarioByCPF",
                type: "POST",
                data: {
                    id: $("#usuarios_id").val(),
                    cpf: cpf.value
                },
                error: function (data) {
                    console.log(data);
                    closeLoaderAnimation();
                }
            }).done(function (result) {
                closeLoaderAnimation();
                if (result !== undefined && result.user !== null) {
                    $("#cpf_validation").text("Este CPF já está em uso.");
                    $("#user_submit").attr("disabled", true);
                } else {
                    var isValid = checkCPFIsValid(cleanIdentity(cpf.value));

                    $("#cpf_validation").text("");
                    if (!isValid) {
                        $("#user_submit").attr("disabled", true);
                        $("#cpf_validation").text("CPF não é válido!");
                        $("#cpf_validation").show();

                        if (
                            occurrencesInvalidCpf >= 1 &&
                            previousCPF == cpf.value
                        ) {
                            $("#cpf_validation").text(
                                "Mesmo CPF digitado inválido duas vezes. Apresente o documento para autorização posterior."
                            );

                            startScanDocument();
                        } else {
                            occurrencesInvalidCpf = occurrencesInvalidCpf + 1;
                            previousCPF = cpf.value;
                            $("#cpf").val("");
                        }
                    } else {
                        $("#cpf_validation").text("");
                        $("#cpf_validation").hide();

                        previousCPF = "";
                        $("#user_submit").attr("disabled", false);
                    }
                }
            });
        };

        /**
         * Limpa campo de CPF ao cadastrar documento estrangeiro
         */
        $("#doc_estrangeiro").on("keyup", function () {
            $("#cpf").val(null);
        });

        /**
         * Função que ativa as verificações de cpf repetido e se é válido
         */
        $("#cpf").on("keyup", function (data) {
            $("#doc_estrangeiro").val(null);

            if (this.value.length == 14) {
                var result = checkCPFRepeated();

                if (result) {
                    var cpf = cleanIdentity(this.value);
                    checkCPFIsValid(cpf);
                }
            }
        });

        /**
         * Função que ativa as verificações de cpf repetido e se é válido
         */
        $("#cpf").on("blur", function () {
            if (!imageStored) {
                if (this.value.length == 14) {
                    var result = checkCPFRepeated();

                    if (result) {
                        var cpf = cleanIdentity(this.value);
                        checkCPFIsValid(cpf);
                    }
                }
            }
        });

        /**
         * Verifica se há e-mail em uso
         */
        $("#email").on("blur", function () {
            if (this.value.length > 0) {
                // verifica se o e-mail é válido

                var email = this.value;
                var tipoPerfil = $("#tipo_perfil").val();

                // Validação de e-mail se for adm developer ou usuário
                if (tipoPerfil == 0 || tipoPerfil == 6) {

                    // se tem arroba e tem um ponto no e-mail
                    // verifica se email possui um @ e se não é o primeiro dígito

                    var contains_at = email.indexOf("@");
                    var contains_dot = getAllIndexes(email, ".");

                    var email_invalid =
                        "Este e-mail não é válido! Geralmente um e-mail possui um formato do tipo 'usuario@email.com'. Por gentileza, confira.";

                    // verifica se tem algum ponto APÓS o arroba
                    if (contains_at == -1 || contains_dot.length == 0) {
                        callModalError(email_invalid);
                    } else {

                        var found = false;

                        contains_dot.forEach(element => {
                            if (element > contains_at) {
                                found = true;
                            }
                        });

                        if (!found) {
                            callModalError(email_invalid);
                            return;
                        }

                    }
                }

                $.ajax({
                    url: "/Usuarios/getUsuarioByEmail",
                    type: "post",
                    data: JSON.stringify({
                        id: $("#usuarios_id").val(),
                        email: this.value,
                        tipo_perfil: tipoPerfil
                    }),
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Accept", "application/json");
                        xhr.setRequestHeader(
                            "Content-Type",
                            "application/json; charset=UTF-8"
                        );
                    },
                    success: function (data) {
                        if (data["user"] !== null) {
                            callModalError(
                                'Este login já está em uso. Para logar com este login, use o formulário de "Esqueci minha Senha"'
                            );
                            $("#email_validation").show();
                            $("#user_submit").attr("disabled", true);
                            // $("#email").focus();
                        } else {
                            $("#email_validation").text("");
                            $("#email_validation").hide();
                            $("#user_submit").attr("disabled", false);
                        }
                    },
                    error: function (error) {
                        callModalError(error.responseJSON.mensagem.message, error.responseJSON.mensagem.errors);
                    }
                });
            }
        });

        $("#telefone").on("focus", function () {
            $("#telefone").unmask("(99)99999-9999");
            $("#telefone").unmask("(99)9999-9999");
        });

        var updateTelefoneFormat = function (data) {
            if (data.length == 10) {
                $("#telefone").mask("(99)9999-9999");
            } else {
                $("#telefone").mask("(99)99999-9999");
            }
        };

        $("#telefone").on("blur", function () {
            updateTelefoneFormat(this.value);
        });

        updateTelefoneFormat($("#telefone").val());

        $("#cep").mask("99.999-999");

        initializeDatePicker("data_nasc", null, null, null, new Date());

        /**
         * Esconde e reseta as informações de Redes
         */
        var hideRedesInput = function () {
            $(".redes_input").hide();
            $(".redes_list").val(null);
            $(".clientes_rede").val(null);
            $("#redes_id").prop("required", false);
            $("#clientes_rede").prop("required", false);

        };

        hideRedesInput();

        /**
         * Mostra informações de redes
         */
        var showRedesInput = function () {
            $(".redes_input").show();

            $("#clientes_rede").prop("required", required);

            var unidadeLabel = "Unidade da Rede";

            if (required) {
                unidadeLabel = unidadeLabel + "*";
            }
            $("label[for=clientes_rede").text(unidadeLabel);
        };

        /**
         * Atualiza o select de unidade da rede
         */
        $(".redes_list").on("change", function () {
            loadUnidadesRede($(this).val());
        });

        /**
         * Carrega unidades de uma rede
         * @param {object} data
         */
        var loadUnidadesRede = function (redesId) {
            var data = {
                redes_id: redesId
            };

            console.log(redesId);
            // callLoaderAnimation("Carregando unidades");

            var clientesRede = $("#clientes_rede");

            clientesRede.empty();

            var option = document.createElement("option");
            option.value = 0;
            option.textContent = "<Selecione um estabelecimento para continuar...>";
            clientesRede.empty();

            if (redesId === 0 || redesId === undefined || isNaN(parseInt(redesId)))
                return false;

            $.ajax({
                type: "GET",
                url: "/api/clientes/get_clientes_list",
                data: data,
                dataType: "JSON",
                success: function (response) {
                    if (response.data.clientes.length > 0) {
                        clientesList = [];
                        clientesRede.empty();

                        var cliente = {
                            id: 0,
                            nomeFantasia: "<Selecione um estabelecimento para continuar...>"
                        };

                        clientesList.push(cliente);
                        clientesRede.prop("disabled", false);

                        response.data.clientes.forEach(cliente => {
                            var item = {
                                id: cliente.id,
                                nomeFantasia: cliente.nome_fantasia_municipio_estado
                            };

                            clientesList.push(item);
                        });

                        clientesList.forEach(cliente => {
                            var option = document.createElement("option");
                            option.value = cliente.id;
                            option.textContent = cliente.nomeFantasia;

                            clientesRede.append(option);
                        });

                        // Se só tem 2 registros, significa que
                        if (clientesList.length == 2) {
                            clientesSelectedItem = clientesList[1];

                            // Option vazio e mais um Estabelecimento? Desabilita pois só tem uma seleção possível
                            clientesRede.prop("disabled", true);
                        }

                        var clientesId = parseInt($("#clientes_id").val());
                        clientesRede.val(clientesId);

                        // var interval;

                        // interval = setInterval(function () {
                        //     if (
                        //         $("#clientes_rede").val() != "" &&
                        //         $("#clientes_rede").val() != null
                        //     ) {
                        //         clearInterval(interval);
                        //     } else {

                        //     }
                        // }, 100);
                    }

                },
                error: function (response) {
                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.error);
                }
            });
        }


        // carrega todas as unidades da rede caso já esteja definido redes_id

        if ($("#redes_id").val() !== undefined && $("#redes_id").val().length > 0) {
            loadUnidadesRede($("#redes_id").val());
        }

        $("#tipo_perfil").on("change", function () {
            changeProfileType(this);
        });

        /**
         * Atualiza dados de Perfil selecionado
         *
         * @param {object} data
         */
        var changeProfileType = function (data) {
            // verifica se entra no perfil de uma unidade da rede (e se quem está cadastrando é um administrador da RTI)

            // verifica se entra no perfil de uma unidade da rede (e se quem está cadastrando é um administrador da RTI)

            var tipoPerfil = $(".usuarioLogadoTipoPerfil").val();

            var labelUnidadeRede = "Unidade da Rede*";

            $(".clientes_rede").prop("required", true);
            $("label[for=clientes_rede]").text(labelUnidadeRede);
            // Gerente
            var tipoPerfilSelecionado = $("#tipo_perfil").val();
            if (tipoPerfilSelecionado >= 5) {
                $("#telefone").attr("required", null);
                $("#label-telefone").text("Telefone");
            } else if (tipoPerfilSelecionado == 1) {
                $(".clientes_rede").prop("required", false);
                $("label[for=clientes_rede]").text(labelUnidadeRede.substr(0, labelUnidadeRede.length - 1));
            } else {
                $("#telefone").attr("required", true);
                $("#label-telefone").text("Telefone*");
            }

            if (tipoPerfil !== undefined) {
                if (tipoPerfil >= 0 && tipoPerfil <= 2) {
                    if ($(data).val() < 1 || $(data).val() > 5) {
                        hideRedesInput();
                    } else {
                        if (tipoPerfilSelecionado > 2 && tipoPerfilSelecionado <= 5) {
                            showRedesInput(true);
                        } else {
                            showRedesInput(false);
                        }
                    }
                }
            }


            if ($(data).val() != 5) {
                $("#senha").mask("AAAAAAAA");
                $("#confirm-senha").mask("AAAAAAAA");
                $(".fields-is-final-customer").hide();
            } else {
                $("#senha").mask("####");
                $("#confirm-senha").mask("####");
                $(".fields-is-final-customer").show();
            }
        };

        changeProfileType($("#tipo-perfil"));
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation);
