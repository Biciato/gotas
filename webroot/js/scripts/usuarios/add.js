/**
 * @author Gustavo Souza Gonçalves
 * @date 12/07/2017
 * @
 *
 */

$(document).ready(function () {

        $("#cpf").focus();
        var formatSenha = "######?################";


        var imageStored = false;

        var startScanDocument = function () {

            $(".group-video-capture").show();

            var video = document.querySelector("#video");
            var photo = document.querySelector("#photoTaken");

            var canvas = $("#canvas")[0];
            var canvasContext = canvas.getContext('2d');

            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

            if (navigator.getUserMedia) {
                navigator.getUserMedia({
                    video: true,
                    audio: false
                }, handleVideo, videoError);
            }

            function handleVideo(stream) {
                window.localStream = stream;
                video.src = window.URL.createObjectURL(stream);

            }

            function videoError(e) { // do something
            }

            $("#takeSnapshot").click(function () {

                canvasContext.drawImage(video, 0, 0, 400, 300);

            });

            $("#storeImage").click(function () {

                var message = '';
                var messageValidation = '';

                if ($("#alternarEstrangeiro")[0].checked) {
                    if ($("#doc_estrangeiro").val().length == 0) {
                        messageValidation = 'Documento de Identificação Estrangeira';
                    }
                } else {
                    if ($("#cpf").val().length < 14) {
                        messageValidation = 'CPF';
                    }
                }

                if (messageValidation.length > 0) {
                    message = "Campo precisa estar preenchido para continuar: " + messageValidation;
                    callModalError(message);
                } else {

                    var resizedCanvas = document.createElement("canvas");
                    var resizedContext = resizedCanvas.getContext('2d');

                    resizedCanvas.height = '768';
                    resizedCanvas.width = '1024';
                    resizedContext.drawImage(canvas, 0, 0, 1024, 768);

                    var nameImage = $("#cpf").val().length == 0 ? $("#doc_estrangeiro").val() : $("#cpf").val();

                    if (!$("#doc_invalido").val().length > 0) {
                        nameImage = cleanIdentity(nameImage);
                    }

                    callLoaderAnimation();
                    $.ajax({
                        url: '/Usuarios/uploadDocumentTemporary',
                        type: 'post',
                        data: JSON.stringify({
                            image: resizedCanvas.toDataURL("image/jpeg"),
                            imageName: nameImage
                        }),
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader("Accept", "application/json");
                            xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                        },
                        success: function (e) {
                            $("#cpf_validation").val("Imagem de Documento enviado com sucesso.");
                            $(".group-video-capture").hide();
                            $("#doc_invalido").val(true);

                        },
                        error: function (e) {
                            window.alert("Houve um erro, por favor tente novamente.");
                            console.log(e);

                            closeLoaderAnimation();

                        },
                        complete: function (e) {
                            $("#cpf_validation").text("Imagem armazenada no servidor.");
                            $("#doc_estrangeiro_validation").text("Imagem armazenada no servidor.");
                            stopScanDocument();
                            $("#user_submit").attr('disabled', false);

                            closeLoaderAnimation();

                            // atribui como true a imagem enviada
                            imageStored = true;

                            // a imagem foi armazenada, então o CPF, mesmo incorreto, está vinculado à imagem.
                            $("#cpf").attr('disabled', true);
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
        }

        /**
         * Esconde e reseta as informações de Redes
         */
        var hideRedesInput = function () {
            $(".redes_input").hide();
            $(".redes_list").val(null);
            $(".clientes_rede").val(null);
            $("#redes_id").prop("required", false);
            $("#clientes_rede").prop("required", false);

        }

        hideRedesInput();

        /**
         * Mostra informações de redes
         */
        var showRedesInput = function (required) {
            $(".redes_input").show();
            $("#redes_id").prop("required", true);
            $("#clientes_rede").prop("required", required);

            var unidadeLabel = "Unidade da Rede";

            if (required) {
                unidadeLabel = unidadeLabel + "*";
            }
            $("label[for=clientes_rede").text(unidadeLabel);
        }

        /**
         * Atualiza o select de unidade da rede
         */
        $(".redes_list").on('change', function () {
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
                        }

                        if (clientesSelectedItem !== undefined && clientesSelectedItem.id > 0) {
                            clientesRede.val(clientesSelectedItem.id);
                        }
                    }

                },
                error: function (response) {
                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.error);
                }
            });
        }

        // carrega todas as unidades da rede caso já esteja definido redesId

        if ($("#redesId").val() != undefined && $("#redesId").val().length > 0) {
            loadUnidadesRede($("#redesId").val());
        }

        $("#senha").mask(formatSenha);
        $("#confirm_senha").mask(formatSenha);

        $(".fields-is-final-customer").show();

        $("#tipo_perfil").on('change', function () {
            changeProfileType(this);
        });

        /**
         * Atualiza dados de Perfil selecionado
         *
         * @param {object} data
         */
        var changeProfileType = function (data) {

            // verifica se entra no perfil de uma unidade da rede (e se quem está cadastrando é um administrador da RTI)

            var tipoPerfil = $(".usuarioLogadoTipoPerfil").val();

            if (tipoPerfil == 5) {
                $("#senha").val(123456);
                $("#confirm_senha").val(123456);
            } else {
                $("#senha").val(null);
                $("#confirm_senha").val(null);
            }

            // Gerente
            var tipoPerfilSelecionado = $("#tipo_perfil").val();

            if (tipoPerfilSelecionado == 5) {
                $("#telefone").prop("required", null);
                $("#label-telefone").text("Telefone");
            } else {
                $("#telefone").prop("required", true);
                $("#label-telefone").text("Telefone*");
            }

            if (tipoPerfil !== undefined) {
                if (tipoPerfil >= 0 && tipoPerfil <= 2) {
                    // if ($(data).val() < 1 || $(data).val() > 5) {
                    if (tipoPerfilSelecionado < 1 || tipoPerfilSelecionado > 5) {
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

            if (tipoPerfilSelecionado > 2) {
                $("#clientes_rede").attr("required", true);
            } else {
                $("#clientes_rede").removeAttr("required");
            }

            console.log("validação de perfil...");
            if ($(data).val() != 6) {
                $("#senha").mask("AAAAAAAA");
                $("#confirm_senha").mask("AAAAAAAA");
                $(".fields-is-final-customer").hide();

            } else {
                $("#senha").mask(formatSenha);
                $("#confirm_senha").mask(formatSenha);
                $(".fields-is-final-customer").show();
            }
        }

        changeProfileType($("#tipo_perfil").val());

        $("#alternarEstrangeiro").click(function () {
            if (this.checked) {
                $("#cpf_box").hide();
                $("#doc_estrangeiro_box").show();
                $("#cpf").prop("required", false);
                $("#doc_estrangeiro").prop("required", true);
            } else {
                $("#cpf_box").show();
                $("#doc_estrangeiro_box").hide();
                $("#cpf").prop("required", false);
                $("#doc_estrangeiro").prop("required", true);
            }

        });

        $("#alternarTransportadora").click(function () {
            if ($("#alternarTransportadora").is(":checked")) {
                $(".transportadora").show();
                $(".transportadora .cnpj").attr("required", true);
                $(".transportadora .razao_social").attr("required", true);
            } else {
                $(".transportadora").hide();
                $(".transportadora .cnpj").attr("required", false);
                $(".transportadora .razao_social").attr("required", false);
            }
        });
        // Já começa desabilitado
        $(".transportadora .cnpj").attr("required", false);
        $(".transportadora .razao_social").attr("required", false);

        if ($("#alternarTransportadora").val() == 1) {
            $(".transportadora").show();

        } else {
            $(".transportadora").hide();
        }

        // $(document).ready(function () {
        //     $(window).keydown(function (event) {
        //         if (event.keyCode == 13) {
        //             event.preventDefault();
        //             return false;
        //         }
        //     });
        // });

        /**
         * Limpa o formulário de cadastro
         * TODO: melhorar
         */
        $(".clearForm").on('click', function () {

            $("#cpf").val("");
            $("#email").val("");
            $("#nome").val("");
            $("#sexo").val(null);
            $("#data_nasc").val("");
            $("#senha").val("");
            $("#confirm_senha").val("");
            $("#telefone").val("");
            $("#endereco").val("");
            $("#endereco_numero").val("");
            $("#endereco_complemento").val("");
            $("#bairro").val("");
            $("#municipio").val("");
            $("#estado").val("");
            $("#pais").val("");
            $("#cep").val("");
        });

        var populateUserData = function (data) {

            $("#cpf").val(data.cpf);
            $("#cpf").mask('###.###.###-##');
            $("#email").val(data.email);
            $("#nome").val(data.nome);
            $("#sexo").val(data.sexo);
            $("#data_nasc").val(data.data_nasc);
            $("#telefone").val(data.telefone);
            $("#endereco").val(data.endereco);
            $("#endereco_numero").val(data.endereco_numero);
            $("#endereco_complemento").val(data.endereco_complemento);
            $("#bairro").val(data.bairro);
            $("#municipio").val(data.municipio);
            $("#estado").val(data.estado);
            $("#pais").val(data.pais);
            $("#cep").val(data.cep);
        }

        var occurrencesInvalidCpf = 0;
        var previousCPF = "";

        /**
         * Remove qualquer caracter especial
         * @param {object} documentUser
         */
        var cleanIdentity = function (parameter) {
            var returnValue = parameter.replace(/\./g, '');
            returnValue = returnValue.replace(/\-/g, '');
            return returnValue;
        }

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

            if ((rest == 10) || (rest == 11)) {
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

            if ((rest == 10) || (rest == 11)) {
                rest = 0;
            }
            if (rest != parseInt(strCPF.substring(10, 11))) {

                return false;
            }

            return true;
        }

        /**
         * Verifica se há CPF repetido no servidor
         */
        var checkCPFRepeated = function () {

            callLoaderAnimation("Verificando CPF...");

            $.ajax({
                url: "/api/usuarios/get_usuario_by_cpf",
                // url: "/usuarios/getUsuarioByCPF",
                type: 'POST',
                data: JSON.stringify({
                    id: 0,
                    cpf: cpf.value
                }),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json");
                    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                    xhr.setRequestHeader("IsMobile", true);
                },
                success: function (data) {
                    // console.log(data);
                },
                error: function (data) {
                    // console.log(data);
                    closeLoaderAnimation();
                    callModalError(data.responseJSON.mensagem.message);

                }
            }).done(function (result) {
                closeLoaderAnimation();
                if (result !== undefined && result.user !== null) {

                    callModalError("Este CPF já está em uso!");
                    $("#user_submit").attr('disabled', true);

                } else {
                    $("#user_submit").attr("disabled", false);
                }
            });
        };

        var checkDocEstrangeiroRepeated = function (param) {

            callLoaderAnimation();

            $.ajax({
                type: "POST",
                url: "/api/usuarios/get_usuario_by_doc_estrangeiro",
                data: JSON.stringify({
                    doc_estrangeiro: param.target.value
                }),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json");
                    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                    xhr.setRequestHeader("IsMobile", 1);
                },
                success: function (response) {
                    console.log(response);
                    $("#user_submit").attr('disabled', false);
                    closeLoaderAnimation();

                },
                error: function (error) {
                    closeLoaderAnimation();
                    var msg = JSON.parse(error.responseText);
                    $("#user_submit").attr('disabled', true);
                    callModalError(msg.title, msg.errors);
                }
            });
        }

        /**
         * Limpa campo de CPF ao cadastrar documento estrangeiro
         */
        $("#doc_estrangeiro")
            .on("keyup", function () {
                $("#cpf").val(null);
            })
            .on("blur", checkDocEstrangeiroRepeated);

        /**
         * Função que ativa as verificações de cpf repetido e se é válido
         */
        $("#cpf").on('keyup', function (data) {
            $("#doc_estrangeiro").val(null);

            if (this.value.length == 14) {
                checkCPFRepeated();
            };
        });

        /**
         * Verifica se há e-mail em uso
         */
        $("#email").on('blur', function () {

            // Só verifica se o usuário informar valor
            if (this.value.length > 0) {
                $.ajax({
                    url: "/Usuarios/getUsuarioByEmail",
                    type: 'post',
                    data: JSON.stringify({
                        id: 0,
                        email: this.value,
                        tipo_perfil: $("#tipo_perfil").val()
                    }),
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Accept", "application/json");
                        xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                        xhr.setRequestHeader("IsMobile", 1);

                        callLoaderAnimation();
                    },
                    success: function (data) {
                        if (data['user'] !== null) {
                            callModalError('Este login já está em uso. Para usar este login, use o formulário de "Esqueci minha Senha"');
                            $("#user_submit").attr('disabled', true);

                        } else {
                            $("#user_submit").attr('disabled', false);
                        }

                    },
                    error: function (error) {
                        callModalError(error.responseJSON.mensagem.message, error.responseJSON.mensagem.errors);
                    }
                }).done(function () {
                    closeLoaderAnimation();
                });
            }

        });

        $("#cpf").mask('###.###.###-##');

        $("#telefone").on('focus', function () {
            $("#telefone").unmask("(99)99999-9999");
            $("#telefone").unmask("(99)9999-9999");
        }).on('blur', function () {
            if (this.value.length == 10) {
                $("#telefone").mask("(99)9999-9999");
            } else {
                $("#telefone").mask("(99)99999-9999");
            }
        }).on("keyup", function (event) {
            // console.log(event);
            this.value = clearNumbers(event.target.value);

        }).on("keydown", function (event) {
            console.log(event.keyCode);
            if (event.keyCode == 13) {
                if (this.value.length == 10) {
                    $("#telefone").mask("(99)9999-9999");
                } else {
                    $("#telefone").mask("(99)99999-9999");
                }
            }
        });

        $("#cep").mask("99.999-999");

        initializeDatePicker("data_nasc", null, null, null, 'now');
        /**
         * Configurações de ação para botão confirmar
         */
        $("#user_submit").on('click', function () {
            $("#cpf").attr('disabled', false);
        });
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation);
