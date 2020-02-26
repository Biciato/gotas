var message = "";
$
    (function () {
        "use strict";

        var quantidadeMultiplicador = $("#quantidade-multiplicador");
        var gravarGotasBtn = $("#botao-gravar-gotas");
        var redes = [];
        var reiniciarBtn = $("#reiniciar");
        var redeSelectedItem = null;
        var redeSelectListBox = $("#redes");
        var usuarioParameterOptions = $("#usuario-options-search");
        var usuarioParameterSearch = $("#usuario-parameter-search");
        var usuarioParameterButton = $("#usuario-parameter-button-search");
        var usuarioNome = $("#usuario-nome");
        var usuarioSaldo = $("#usuario-saldo");
        var usuariosList = [];
        var usuariosSelectedItem = null;
        var usuariosTable = $("#usuarios-table");
        var usuariosRegion = $("#usuarios-region");
        var veiculoRegion = $("#veiculo-region");

        /**
         * Constructor
         */
        function init() {
            getRedes();

            reiniciarBtn.unbind("click");
            reiniciarBtn.on("click", init);
            redeSelectListBox.unbind("change");
            redeSelectListBox.on("change", redesOnChange);

            gravarGotasBtn.unbind("click");
            gravarGotasBtn.on("click", setGotasManualUsuario);

            quantidadeMultiplicador.val(null);
            quantidadeMultiplicador.prop("disabled", true);
            quantidadeMultiplicador.on("keyup", updateButtonGravarGotas);
            quantidadeMultiplicador.mask("Z####", {
                translation: {
                    '#': {
                        pattern: /\d/
                    },
                    'Z': {
                        pattern: /[\-]/,
                        optional: true
                    }
                }

            });

            usuarioParameterOptions.unbind("change");
            usuarioParameterOptions.on("change", usuarioParameterOptionsOnChange);
            usuarioParameterOptions.change();

            usuarioParameterButton.unbind("click");
            usuarioParameterButton.bind("click", usuarioParameterButtonOnClick);

            usuariosSelectedItem = null;
            usuarioNome.val(null);
            usuarioParameterSearch.val(null);
            usuarioSaldo.val(null);

            // Habilita/desabilita botão de gravar as gotas do cliente final
            updateButtonGravarGotas();
        }

        // #region Funções da tela

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

            veiculoRegion.hide();

            usuariosSelectedItem = {};
            usuarioNome.val(null);
            usuarioSaldo.val(null);

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
                            telefone: convertTextToPhone(usuario.telefone),
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
                                orderable: true,
                                visible: false,
                            },
                            {
                                data: "nome",
                                name: "Nome",
                                orderable: true,
                            },
                            {
                                data: "telefone",
                                name: "Telefone",
                                orderable: true,
                            },
                            {
                                data: "data_nasc",
                                name: "Data Nasc.",
                                orderable: true,
                            },
                            {
                                data: "acoes",
                                name: "Ações",
                                orderable: false,
                            }
                        ],
                        data: usuarioData
                    });

                    setTimeout(() => {

                        // Após renderizar a tabela, remove e reassocia evento de click dos botões
                        var usuarioButtonSelect = $(".usuario-button-select");
                        usuarioButtonSelect.unbind("click");

                        usuarioButtonSelect.on("click", function () {

                            var id = $(this).data('id');
                            usuariosSelectedItem = usuariosList.find(x => x.id === id);

                            if (usuariosSelectedItem !== undefined) {
                                usuarioNome.val(usuariosSelectedItem.nome);
                                getUsuarioPontuacoes(usuariosSelectedItem.id, redeSelectedItem.id);
                                updateButtonGravarGotas();
                                veiculoRegion.hide();
                                usuariosRegion.hide();
                            }
                        });
                    }, 300);


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
                .prop("maxlength", 100)
                .unmask();

            if (listElement.val() === "nome") {

                return false;
            } else if (listElement.val() === "cpf") {
                textElement.mask("999.999.999-99");
                return false;
            } else if (listElement.val() === "telefone") {
                textElement.prop("maxlength", 11);

                textElement.on('focus', function () {
                    textElement.prop("maxlength", 11);

                }).on('blur', function () {
                    if (this.value.length == 10) {
                        textElement.mask("(99)9999-9999");
                    } else {
                        textElement.mask("(99)99999-9999");
                    }
                }).on("keyup", function (event) {
                    // console.log(event);
                    this.value = clearNumbers(event.target.value);

                }).on("keydown", function (event) {
                    // console.log(event.keyCode);

                    if (event.keyCode == 13) {

                        if (listElement.val() === "telefone") {
                            if (valueElement.length <= 10) {
                                textElement.mask("(99)9999-9999");
                            } else {
                                textElement.mask("(99)99999-9999");
                            }
                        }

                        searchButton.click();
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

        /**
         * Redes on Change
         *
         * Atualiza lista de clientes ao selecionar uma rede
         *
         * webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas.js::redesOnChange
         *
         * @param {Event} evt
         *
         * @returns {void}
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-12
         */
        function redesOnChange(evt) {
            var id = parseInt(evt.target.value);

            if (!isNaN(id)) {
                redeSelectedItem = redes.find(x => x.id == id);
                console.log(redeSelectedItem);
            } else {
                usuariosSelectedItem = null;
                usuarioParameterSearch.val(null);
                usuarioNome.val(null);
                usuarioSaldo.val(null);
                redeSelectedItem = null;
            }

            usuarioParameterSearch.change();
        }

        /**
         * webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas.js::updateButtonGravarGotas
         *
         * Atualiza status habilitado de botão gravar gotas do usuário
         *
         * @returns void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-12
         */
        function updateButtonGravarGotas() {
            gravarGotasBtn.prop("disabled", true);

            var value = quantidadeMultiplicador.val();

            if (redeSelectedItem != null && usuariosSelectedItem != null) {
                quantidadeMultiplicador.prop("disabled", false);
            } else {
                quantidadeMultiplicador.val(null);
                quantidadeMultiplicador.prop("disabled", true);
            }

            if (value !== undefined && value != 0 && redeSelectedItem != null && usuariosSelectedItem != null) {
                gravarGotasBtn.prop("disabled", false);
            }
        }

        // #endregion

        // #region Serviços REST

        /**
         * webroot\js\scripts\pontuacoes_comprovantes\correcao_gotas.js::getRedes
         *
         * Obtem as redes cadastradas e ativas
         *
         * @returns {Select} ListBox
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-09
         */
        function getRedes() {
            $.ajax({
                type: "GET",
                url: "/api/redes/get_redes_list",
                data: {},
                dataType: "JSON",
                success: function (response) {
                    var data = response.data.redes;

                    if (data.length > 0) {
                        redeSelectListBox.empty();

                        var option = document.createElement("option");
                        option.value = null;
                        option.textContent = "";
                        redeSelectListBox.append(option);

                        data.forEach(item => {
                            var rede = {
                                id: item.id,
                                nome: item.nome_rede
                            };

                            redes.push(rede);

                            var option = document.createElement("option");
                            option.value = rede.id;
                            option.textContent = rede.nome;
                            redeSelectListBox.append(option);
                        });

                        if (data.length == 1) {
                            var rede = data[0];
                            redeSelectedItem = rede;
                            redeSelectListBox.val(rede.id);
                            redeSelectListBox.prop("disabled", true);
                            redeSelectListBox.prop("readonly", true);
                        }
                    }
                },
                error: function (response) {
                    var mensagem = response.responseJSON.mensagem;

                    callModalError(mensagem.message, mensagem.errors);
                }
            });
        }

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
        function getUsuarioPontuacoes(usuariosId, redesId) {
            if (redeSelectedItem != null && usuariosSelectedItem != null) {
                var data = {
                    usuarios_id: usuariosId,
                    redes_id: redesId
                };
                $.ajax({
                    type: "POST",
                    url: "/api/pontuacoes/get_pontuacoes_rede",
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        var saldo = response.resumo_gotas != undefined ? response.resumo_gotas.saldo : 0;
                        usuarioSaldo.val(saldo);
                    },
                    error: function (response) {
                        var mensagem = response.responseJSON.mensagem;

                        callModalError(mensagem.message, mensagem.errors);
                    }
                });
            }

        }

        /**
         * Grava as gotas do Usuário
         *
         * Realiza a gravação dos dados inseridos pelo operador
         *
         * webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas.js::setGotasManualUsuario
         *
         * @param {Event} evt
         *
         * @returns {void}
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-13
         */
        function setGotasManualUsuario() {
            var data = {
                redes_id: redeSelectedItem.id,
                usuarios_id: usuariosSelectedItem.id,
                quantidade_gotas: quantidadeMultiplicador.val()
            };

            $.ajax({
                type: "POST",
                url: "/api/pontuacoes_comprovantes/set_gotas_manual_usuario",
                data: data,
                dataType: "JSON",
                success: function (response) {
                    callModalSave();
                    // Uma vez gravado, recarrega a tela
                    init();
                },
                error: function (response) {
                    var msg = response.responseJSON.mensagem;

                    callModalError(msg.message, msg.errors);
                }
            });
        }
        // #endregion

        init();
    })
    .ajaxStart(function () {
        callLoaderAnimation(message);
    })
    .ajaxStop(closeLoaderAnimation);
