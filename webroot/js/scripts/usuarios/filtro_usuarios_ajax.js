/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\usuarios\filtro_usuarios_ajax.js
 * @date 11/08/2017
 *
 */

var contaAvulsa = $("#usuarios_id").val() == "conta_avulsa";

$(document).ready(function () {

        var usuarioParameterOptions = $("#usuario-options-search");
        var usuarioParameterSearch = $("#usuario-parameter-search");
        var usuarioParameterButton = $("#usuario-parameter-button-search");
        var usuariosNome = $("#usuarios-nome");
        var usuariosSaldo = $("#usuarios-saldo");
        var usuariosList = [];
        var usuariosSelectedItem = {};
        var usuariosTable = $("#usuarios-table");
        var usuariosTableBody = $("#usuarios-table tbody");
        var usuariosRegion = $("#usuarios-region");
        var veiculoRegion = $("#veiculo-region");
        var redesId = parseInt($("#redes-id").val());

        // Botões

        var newSearchUserBtn = $("#new-user-search");


        function init() {

            newSearchUserBtn.unbind("click");
            newSearchUserBtn.on("click", newSearchUserBtnOnClick);

            usuarioParameterOptions.unbind("change");
            usuarioParameterOptions.on("change", usuarioParameterOptionsOnChange);
            usuarioParameterOptions.change();

            usuarioParameterButton.unbind("click");
            usuarioParameterButton.bind("click", usuarioParameterButtonOnClick);


            usuariosNome.val(null);
            usuarioParameterSearch.val(null);
            usuariosSaldo.val(null);
        }


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

        function newSearchUserBtnOnClick() {
            $(".user-query-region").show();
            setUsuariosInfo(null, contaAvulsa);
            $(".user-result").hide();
        };

        /**
         * Comportamento de click do button que faz a pesquisa do usuário
         */
        function usuarioParameterButtonOnClick() {
            var url = usuarioParameterOptions.val() === "placa" ? "/api/veiculos/get_usuarios_by_veiculo" : "/api/usuarios/get_usuarios_finais";

            var dataToSend = {};

            if (usuarioParameterOptions.val() === "nome") {
                dataToSend.nome = usuarioParameterSearch.val().trim();
            } else if (usuarioParameterOptions.val() === "cpf") {
                dataToSend.cpf = clearNumbers(usuarioParameterSearch.val().trim());
            } else if (usuarioParameterOptions.val() === "telefone") {
                dataToSend.telefone = clearNumbers(usuarioParameterSearch.val().trim());
            } else if (usuarioParameterOptions.val() === "placa") {
                dataToSend.placa = usuarioParameterSearch.val().trim();
            }

            var createUser = parseInt($("#cria-usuario-cpf-pesquisa").val());

            // Se no DOM está definido para criar o registro e a opção é CPF, então envia como criação.
            if ((createUser !== undefined && createUser !== null) && usuarioParameterOptions.val() == "cpf") {
                dataToSend.create_user = createUser == 1;
            } else {
                dataToSend.create_user = false;
            }

            veiculoRegion.hide();

            usuariosSelectedItem = {};
            usuariosNome.val(null);
            usuariosSaldo.val(null);

            // Limpa os dados do usuário selecionado em nova pesquisa
            setUsuariosInfo(null, null);

            // Caso especial para telas são de localizar usuário para pontuar, desabilita-se em nova pesquisa
            $(".user-btn-proceed").addClass("disabled");
            $(".user-btn-proceed").attr("disabled", true);
            $(".user-selected").hide();

            $.ajax({
                type: "GET",
                url: url,
                data: dataToSend,
                dataType: "JSON",
                success: function (res) {

                    usuariosRegion.show();
                    console.log(res);
                    var veiculo = {};

                    usuariosList = [];

                    if (usuarioParameterOptions.val() === "placa") {
                        veiculo = res.data.veiculo;
                        veiculoRegion.show();

                        updateVeiculosDetails(veiculo);
                        veiculo.usuarios_has_veiculos.forEach(item => {

                            usuariosList.push(item.usuario);
                        });
                    } else
                        usuariosList = res.data.usuarios;

                    var usuarioData = [];
                    usuariosList.forEach(usuario => {

                        var selectButton = "<div data-id='" + usuario.id + "' class='btn btn-primary usuario-button-select' title='Selecionar'><i class='fas fa-check-circle'></i></div>";

                        usuarioData.push({
                            id: usuario.id,
                            nome: usuario.nome,
                            telefone: usuario.telefone === undefined || usuario.telefone === null ? "" : convertTextToPhone(usuario.telefone),
                            data_nasc: usuario.data_nasc === undefined || usuario.data_nasc === null ? "" : moment(usuario.data_nasc, "YYYY-MM-DD").format("DD/MM/YYYY"),
                            acoes: selectButton
                        });
                    });

                    if ($.fn.DataTable.isDataTable("#" + usuariosTable.attr('id'))) {
                        usuariosTable.DataTable().clear();
                        usuariosTable.DataTable().destroy();
                    }

                    usuariosTable.DataTable({
                        language: {
                            "url": "/webroot/js/DataTables/i18n/dataTables.pt-BR.lang",
                            // "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json",
                        },
                        columns: [{
                                data: "id",
                                name: "Id",
                                title: "Id",
                                orderable: true,
                                visible: false,
                            },
                            {
                                data: "nome",
                                name: "Nome",
                                title: "Nome",
                                orderable: true,
                            },
                            {
                                data: "telefone",
                                name: "Telefone",
                                title: "Telefone",
                                orderable: true,
                            },
                            {
                                data: "data_nasc",
                                name: "Data Nasc.",
                                title: "Data Nasc.",
                                orderable: true,
                            },
                            {
                                data: "acoes",
                                name: "Ações",
                                title: "Ações",
                                orderable: false,
                            }
                        ],
                        data: usuarioData
                    });

                    // Após renderizar a tabela, remove e reassocia evento de click dos botões

                    usuariosTableBody.unbind("click");

                    usuariosTableBody.on("click", ".btn", function () {

                        var id = $(this).data('id');
                        usuariosSelectedItem = usuarioData.find(x => x.id === id);

                        if (usuariosSelectedItem !== undefined) {
                            usuariosNome.val(usuariosSelectedItem.nome);
                            getUsuarioPontuacoes(usuariosSelectedItem.id, redesId);
                            veiculoRegion.hide();
                            usuariosRegion.hide();
                            console.log(usuariosSelectedItem);

                            setUsuariosInfo(usuariosSelectedItem, contaAvulsa);

                            // Caso especial para telas são de localizar usuário para pontuar, deve-se habilitar um botão
                            $(".user-btn-proceed").removeClass("disabled");
                            $(".user-btn-proceed").attr("disabled", false);
                            $(".user-selected").show();

                        }
                    });
                },
                error: function (res) {
                    var mensagem = res.responseJSON.mensagem;
                    callModalError(mensagem.message, mensagem.errors);
                }
            });
        }

        /**
         * Evento disparado ao digitar cpf do Usuário
         *
         * Ao digitar o cpf do usuário, busca o registro na base de dados se o CPF existir
         *
         * @param {Event} event Event
         *
         * @returns void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.4
         */
        function usuarioParameterOptionsOnChange(e) {
            var textElement = usuarioParameterSearch;
            var valueElement = clearNumbers(textElement.val());
            var listElement = usuarioParameterOptions;
            var searchButton = usuarioParameterButton;
            textElement.val(null);
            textElement.unbind("focus")
                .unbind("blur")
                .unbind("keyup")
                .unbind("keydown")
                .prop("maxlength", 100)
                .unmask();

            // Adiciona enter ao element de pesquisa
            textElement.on("keydown", function (evt) {
                var list = ["nome", "placa"];

                if (list.includes(listElement.val())) {
                    valueElement = textElement.val();
                } else {
                    valueElement = clearNumbers(textElement.val());
                }

                if (evt.keyCode === 13 && valueElement.length > 3) {
                    searchButton.click();
                };
            });

            if (listElement.val() === "nome") {
                textElement.attr("max-length", 999);
                return false;
            } else if (listElement.val() === "cpf") {
                console.log(textElement);
                textElement.mask("999.999.999-99");
                return false;
            } else if (listElement.val() === "telefone") {
                textElement.prop("maxlength", 11);

                textElement.on('focus', function () {
                    textElement.prop("maxlength", 11);

                }).on('focus', function () {
                    textElement.unmask();

                    textElement.prop("maxlength", 11);

                }).on('blur', function () {
                    if (this.value.length == 10) {
                        textElement.mask("(99)9999-9999");
                    } else {
                        textElement.mask("(99)99999-9999");
                    }
                }).on("keyup", function (event) {
                    textElement.prop("maxlength", 11);
                    valueElement = clearNumbers(textElement.val());

                    if (event.keyCode == 13) {

                        if (listElement.val() === "telefone") {
                            if (valueElement.length <= 10) {
                                textElement.mask("(99)9999-9999");
                            } else {
                                textElement.mask("(99)99999-9999");
                            }
                        }
                    } else {
                        textElement.unmask();
                    }
                });

                return false;
            } else {
                textElement.mask("AAA9B99", {
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
                return false;
            }
        }

        /**
         * Exibe veículo
         *
         * Atualiza dados de veículos à serem exibidos
         *
         * @param {item} data Item de Veículo
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.4
         */
        function updateVeiculosDetails(data) {

            var veiculoPlaca = $("#veiculo-placa");
            var veiculoModelo = $("#veiculo-modelo");
            var veiculoFabricante = $("#veiculo-fabricante");
            var veiculoAno = $("#veiculo-ano");
            if (data === undefined) {
                veiculoPlaca.val(null);
                veiculoModelo.val(null);
                veiculoFabricante.val(null);
                veiculoAno.val(null);
            } else {
                veiculoPlaca.val(data.placa);
                veiculoModelo.val(data.modelo);
                veiculoFabricante.val(data.fabricante);
                veiculoAno.val(data.ano);
            }
        }

        //#region REST Methods

        /**
         * Obtem pontuações de usuário
         *
         * Obtem pontuações de usuário após selecionar o usuário e a rede
         *
         * webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas.js::getUsuarioPontuacoes
         *
         * @param {*} usuariosId Id de Usuario
         * @param {*} redesId Id da Rede
         *
         * @returns {float} Pontuações
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-13
         */
        async function getUsuarioPontuacoes(usuariosId, redesId) {
            try {
                // casos difíceis de acontecer, mas se por ventura acontecer, gera exception
                if (usuariosId === undefined || usuariosId === null) {
                    throw "Necessário selecionar um usuário para obter saldo de pontos!";
                }

                if (redesId === undefined || redesId === null) {
                    throw "Necessário selecionar uma rede para obter saldo de pontos!";
                }

                let getData = function (usuariosId, redesId) {
                    var data = {
                        usuarios_id: usuariosId,
                        redes_id: redesId
                    };
                    return Promise.resolve($.ajax({
                        type: "POST",
                        url: "/api/pontuacoes/get_pontuacoes_rede",
                        data: data,
                        dataType: "JSON"
                    }));
                }

                var dataReturn = await getData(usuariosId, redesId);

                if (dataReturn !== undefined && dataReturn !== null) {
                    var saldo = dataReturn.resumo_gotas != undefined ? dataReturn.resumo_gotas.saldo : 0;
                    usuariosSaldo.val(saldo);
                }

            } catch (error) {
                console.log(error);
                var msg = {};
                if (error.responseJSON !== undefined) {
                    msg = error.responseJSON.mensagem;
                    callModalError(msg.message, msg.errors);
                } else if (error.responseText !== undefined) {
                    msg = error.responseText;
                    callModalError(msg);
                } else {
                    msg = error;
                    callModalError(msg);
                }
            }
        }

        //#endregion

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
                $(".user-query-region .parametro").mask("AAA9B99", {
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
            }
        });

        $(".opcoes").change();

        // Chama o método construtor
        init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation);

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
        $("#usuarios-id").val(data.id);
        $("#usuarios-nome").val(data.nome);
        $("#usuarios-data-nasc").val(data.data_nasc);
        $("#usuarios-pontuacoes").val(data.pontuacoes);
    } else {
        $("#usuarios-id").val(contaAvulsa ? "conta_avulsa" : null);
        $("#usuarios-nome").val(null);
        $("#usuarios-data-nasc").val(null);
        $("#usuarios-pontuacoes").val(null);
        $("#current_password").val(null);
    }
}
