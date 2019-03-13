/**
 * @author Gustavo Souza Gonçalves
 * @date 12/07/2017
 * @
 *
 */

$(document).ready(function () {

    $("#cpf").focus();

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

    }

    hideRedesInput();

    /**
     * Mostra informações de redes
     */
    var showRedesInput = function () {
        $(".redes_input").show();
    }

    /**
     * Atualiza o select de unidade da rede
     */
    $(".redes_list").on('change', function () {
        var data = {
            redes_id: $(this).val()
        };

        loadUnidadesRede(data);
    });

    /**
     * Carrega unidades de uma rede
     * @param {object} data
     */
    var loadUnidadesRede = function (data) {

        callLoaderAnimation("Carregando unidades");

        $.ajax({
            type: "post",
            url: "/RedesHasClientes/getAllClientesFromRede",
            data: JSON.stringify(data),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            },
            error: function (response) {
                console.log(response);
                closeLoaderAnimation();
            }
        }).done(function (result) {
            var options = $("#clientes_rede");

            options.empty();
            options.append($("<option />"));

            $.each(result.redes, function () {
                options.append($("<option />").val(this.clientes_id).text(this.cliente.razao_social));
            });
            closeLoaderAnimation();

        });
    }

    // carrega todas as unidades da rede caso já esteja definido redesId

    if ($("#redesId").val() != undefined && $("#redesId").val().length > 0) {
        var data = {
            redes_id: $("#redesId").val()
        };

        loadUnidadesRede(data);
    }

    $("#senha").mask("####");
    $("#confirm-senha").mask("####");

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
        $("#senha").val(null);
        $("#confirm-senha").val(null);

        // verifica se entra no perfil de uma unidade da rede (e se quem está cadastrando é um administrador da RTI)

        var tipoPerfil = $(".usuarioLogadoTipoPerfil").val();

        // Gerente
        var tipoPerfilSelecionado = $("#tipo_perfil").val();
        if (tipoPerfilSelecionado >= 5) {
            $("#telefone").attr("required", null);
            $("#label-telefone").text("Telefone");
        } else {
            $("#telefone").attr("required", true);
            $("#label-telefone").text("Telefone*");
        }

        if (tipoPerfil !== undefined) {
            if (tipoPerfil >= 0 && tipoPerfil <= 2) {
                if ($(data).val() < 1 || $(data).val() > 5) {
                    hideRedesInput();
                } else {
                    showRedesInput();
                }
            }
        }

        if ($(data).val() != 6) {
            $("#senha").mask("AAAAAAAA");
            $("#confirm-senha").mask("AAAAAAAA");
            $(".fields-is-final-customer").hide();

        } else {
            $("#senha").mask("####");
            $("#confirm-senha").mask("####");
            $(".fields-is-final-customer").show();
        }
    }

    changeProfileType($("#tipo_perfil"));

    $("#alternarEstrangeiro").click(function () {
        var toggle = this.checked;
        $("#cpf_box").toggle(!toggle);
        $("#doc_estrangeiro_box").toggle(toggle);

        // if (this.checked) {
        //     startScanDocument();
        // } else {
        //     stopScanDocument();
        // }

    });

    // if ($("#alternarEstrangeiro").prop("checked") == true) {
    //     $("#cpf_box").hide();
    //     $("#doc_estrangeiro_box").show();
    //     $("#doc_estrangeiro_validation").text("É necessário capturar uma cópia do documento para posterior aprovação.");
    //     // startScanDocument();
    // } else {

    //     $("#cpf_box").show();
    //     $("#doc_estrangeiro_box").hide();
    //     $("#doc_estrangeiro_validation").text("É necessário capturar uma cópia do documento para posterior aprovação.");

    //     // $("#doc_estrangeiro_validation").text(null);
    //     // stopScanDocument();
    // }

    $("#alternarTransportadora").click(function () {
        if ($("#alternarTransportadora").is(":checked")) {
            $(".transportadora").show();
        } else {
            $(".transportadora").hide();
        }
    });

    if ($("#alternarTransportadora").val() == 1) {
        $(".transportadora").show();

    } else {
        $(".transportadora").hide();
    }

    $(document).ready(function () {
        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });

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
            url: "/api/usuarios/getUsuarioByCPF",
            type: 'POST',
            data: JSON.stringify({
                id: 0,
                cpf: cpf.value
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            },
            success: function (data) {
                console.log(data);
            },
            error: function (data) {
                console.log(data);
                closeLoaderAnimation();

            }
        }).done(function (result) {
            closeLoaderAnimation();
            if (result !== undefined && result.user !== null) {

                $("#cpf_validation").text("Este CPF já está em uso.");
                $("#cpf_validation").show();
                $("#user_submit").attr('disabled', true);

            } else {

                var isValid = checkCPFIsValid(cleanIdentity(cpf.value));

                $("#cpf_validation").text("");
                $("#cpf_validation").hide();
                if (!isValid) {
                    $("#user_submit").attr('disabled', true);
                    $("#cpf_validation").text("CPF não é válido!");
                    $("#cpf_validation").show();

                    if (occurrencesInvalidCpf >= 1 && (previousCPF == cpf.value)) {
                        // $("#cpf_validation").text("Mesmo CPF digitado inválido duas vezes. Apresente o documento para autorização posterior.");
                        callModalError("Mesmo CPF digitado inválido duas vezes. Apresente o documento para autorização posterior.");

                        startScanDocument();
                    } else {
                        occurrencesInvalidCpf = occurrencesInvalidCpf + 1;
                        previousCPF = cpf.value;
                        // $("#cpf").val("");
                    }
                } else {
                    $("#cpf_validation").text("");
                    $("#cpf_validation").hide();

                    previousCPF = "";
                    occurrencesInvalidCpf = 0;

                    $("#user_submit").attr('disabled', false);
                }
            }
        });
    };

    var checkDocEstrangeiroRepeated = function (param) {

        $.ajax({
            type: "POST",
            // url: "/api/usuarios/getUsuarioByDocEstrangeiroAPI",
            url: "/api/usuarios/get_usuario_by_doc_estrangeiro",
            data: JSON.stringify({
                doc_estrangeiro: param.target.value
            }),
            // dataType: "json",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                xhr.setRequestHeader("IsMobile", 1);
            },
            success: function (response) {
                console.log(response);

            }, error: function(error){
                var msg = JSON.parse(error.responseText);
                callModalError(msg.title, msg.errors);
            }
        });
    }

    /**
     * Limpa campo de CPF ao cadastrar documento estrangeiro
     */
    $("#doc_estrangeiro")
        .on("keyup", function() {
            $("#cpf").val(null);
        })
        .on("blur", checkDocEstrangeiroRepeated);

    /**
     * Função que ativa as verificações de cpf repetido e se é válido
     */
    $("#cpf").on('keyup', function (data) {
        $("#doc_estrangeiro").val(null);

        if (this.value.length == 14) {

            var result = checkCPFRepeated();

            if (result) {
                var cpf = cleanIdentity(this.value);
                checkCPFIsValid(cpf);
            }

        };
    });

    /**
     * Função que ativa as verificações de cpf repetido e se é válido
     */
    $("#cpf").on('blur', function () {
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
    $("#email").on('blur', function () {
        if (this.value.length > 0) {

            // verifica se o e-mail é válido

            var email = this.value;

            // verifica se email possui um @ e se não é o primeiro dígito

            var contains_at = this.value.indexOf("@");

            var contains_dot = getAllIndexes(this.value, ".");

            // se tem arroba e tem um ponto no e-mail

            var email_invalid = "Este e-mail não é válido! Geralmente um e-mail possui um formato do tipo 'usuario@email.com'. Por gentileza, confira.";

            if (contains_at == -1 || contains_dot.length == 0) {
                callModalError(email_invalid);
            } else {
                // verifica se tem algum ponto APÓS o arroba

                var found = false;
                contains_dot.forEach(element => {
                    if (element > contains_at) {
                        found = true;
                        // break;
                    }
                });

                if (!found) {
                    callModalError(email_invalid);
                } else {
                    $.ajax({
                        url: "/Usuarios/getUsuarioByEmail",
                        type: 'post',
                        data: JSON.stringify({
                            id: 0,
                            email: this.value
                        }),
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader("Accept", "application/json");
                            xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                        },
                        success: function (data) {
                            if (data['user'] !== null) {
                                callModalError('Este e-mail já está em uso. Para logar com este e-mail, use o formulário de "Esqueci minha Senha"');
                                $("#user_submit").attr('disabled', true);

                            } else {
                                $("#user_submit").attr('disabled', false);
                            }
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                }
            }
        }
    });

    $("#cpf").mask('###.###.###-##');

    $("#telefone").on('focus', function () {
        $("#telefone").unmask("(99)99999-9999");
        $("#telefone").unmask("(99)9999-9999");
    });

    $("#telefone").on('blur', function () {
        if (this.value.length == 10) {
            $("#telefone").mask("(99)9999-9999");
        } else {
            $("#telefone").mask("(99)99999-9999");
        }
    });

    $("#cep").mask("99.999-999");

    initializeDatePicker("data_nasc");
    /**
     * Configurações de ação para botão confirmar
     */
    $("#user_submit").on('click', function () {
        $("#cpf").attr('disabled', false);
    });
});
