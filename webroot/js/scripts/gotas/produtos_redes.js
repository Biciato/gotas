/**
 * Arquivo de 'controller' para src\Template\Gotas\produtos_redes.ctp
 *
 * @file webroot\js\scripts\gotas\gotas_redes.js
 * @author Gustavo Souza Gonçalves
 * @date 2019-12-06
 */


$
    (function () {
        "use strict";
        // #region Fields

        var form = {};
        var clientesSelectListBox = $("#clientes-list");
        var clientesSelectedItem = {};
        var clientesList = [];
        var gotasData = [];
        var gotasSelectedItem = {};
        var redesList = [];
        var redesSelectListBox = $("#redes-list");
        var redesSelectedItem = {};

        var tableGotas = $("#data-table");

        var searchBtn = $("#btn-search");
        var clearBtn = $("#btn-clear");

        // #endregion

        // #region Functions

        function init() {
            // Desabilita inicialmente os botões

            // tableGotasData.empty();
            gotasData = [];
            searchBtnChangeState(false);
            clearBtnChangeState(false);

            getRedesList();
        }

        function searchBtnChangeState(state) {
            searchBtn.prop("disabled", state);
        }

        function clearBtnChangeState(state) {
            clearBtn.prop("disabled", state);
        }

        /**
         * Altera estado
         *
         * Altera estado de um item
         *
         * @param {Gota} dataSelecionado Registro Selecionado
         * @param {bool} habilitado Status
         *
         * @returns void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-06
         */
        var changeStateItem = function (dataSelecionado, habilitado) {
            // Obtem índice
            var index = data
                .map(function (obj) {
                    return obj.id;
                })
                .indexOf(dataSelecionado.id);

            dataSelecionado.importar = habilitado;
            data[index] = dataSelecionado;

            gerarTabelaGotas(data);
        };

        /**
         * Gera Template
         *
         * Gera template de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraEditarButton
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {HTMLButtonElement} Elemento Button
         */
        var geraEditarButton = function (value) {
            var template =
                "<button class='btn btn-primary btn-xs form-btn-edit' id='form-btn-edit' value=" +
                value +
                "> <i class=' fa fa-edit'></i></button>";

            return template;
        };

        /**
         * Gera Funções
         *
         * Gera funções de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraEditarButtonFunctions
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {Function} Elemento Button
         */
        var geraEditarButtonFunctions = function (classString, data) {
            var click = function (e) {
                var id = $(this).val();

                var dataSelecionadoTemp = $.grep(data, function (cb) {
                    return cb.id == id;
                });

                if (dataSelecionadoTemp.length > 0) {
                    dataSelecionado = dataSelecionadoTemp[0];
                }

                editar(dataSelecionado);
            };

            $("." + classString).on("click", click);
        };

        /**
         * Gera Template
         *
         * Gera template de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraHabilitarButton
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {HTMLButtonElement} Elemento Button
         */
        var geraHabilitarButton = function (value) {
            var template =
                "<button class='btn btn-primary btn-xs form-btn-enable' id='form-btn-enable' value=" +
                value +
                "> <i class=' fa fa-power-off'></i></button>";

            return template;
        };

        /**
         * Gera Funções
         *
         * Gera funções de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraHabilitarButtonFunctions
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {Function} Elemento Button
         */
        var geraHabilitarButtonFunctions = function (classString, data) {
            $("." + classString).on("click", function () {
                var id = $(this).val();
                dataSelecionado = data.find(obj => obj.id == id);
                changeStateItem(dataSelecionado, true);
            });
        };

        /**
         * Gera Template
         *
         * Gera template de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraDesabilitarButton
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {HTMLButtonElement} Elemento Button
         */
        var geraDesabilitarButton = function (value) {
            var template =
                "<button class='btn btn-danger btn-xs form-btn-disable' id='form-btn-disable' value=" +
                value +
                "> <i class=' fa fa-power-off'></i></button>";

            return template;
        };

        /**
         * Gera Funções
         *
         * Gera funções de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraDesabilitarButtonFunctions
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {Function} Elemento Button
         */
        var geraDesabilitarButtonFunctions = function (classString, data) {
            $("." + classString).on("click", function () {
                var id = $(this).val();
                dataSelecionado = data.find(obj => obj.id == id);
                changeStateItem(dataSelecionado, false);
            });
        };


        /**
         * relatorio_entrada_saida.js::clientesSelectListBoxOnChange()
         *
         * Comportamento ao trocar o cliente selecionado
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-11
         *
         * @return void
         */
        function clientesSelectListBoxOnChange() {
            var clienteSelected = clientesSelectListBox.val();

            // Se não tiver seleção, será feito via backend.
            clienteSelected = parseInt(clienteSelected);
            var clientesId = isNaN(clienteSelected) ? 0 : clienteSelected;
            form.clientesId = clientesId;

            if (form.clientesId > 0) {
                clientesSelectedItem = clientesList.find(x => x.id === form.clientesId);
            } else {
                clientesSelectedItem = {};
            }
        }

        /**
         * Redes On Change
         *
         * Comportamento ao atualizar rede selecionada
         *
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::redesSelectListBoxOnChange
         *
         * @returns void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-26
         */
        function redesSelectListBoxOnChange() {
            var rede = parseInt(this.value);

            if (isNaN(rede)) {
                rede = 0;
            }

            redesSelectedItem = redesList.find(x => x.id == rede);

            if (redesSelectedItem !== undefined && redesSelectedItem.id > 0) {
                getClientesList(redesSelectedItem.id);
            } else {
                var option = document.createElement("option");
                option.value = 0;
                option.textContent = "Selecione uma Rede para continuar...";
                option.title = "Selecione uma Rede para continuar...";

                clientesList = [];
                clientesSelectListBox.empty();
                clientesSelectListBox.append(option);

                gotasData = [];
                gotasSelectedItem = {};
            }
        }

        // #region Get / Set REST Services

        /**
         * Obtem Clientes
         *
         * Obtem lista de clientes disponível para seleção
         *
         * @param {int} redesId Id da rede
         *
         * @return SelectListBox
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-06
         */
        function getClientesList(redesId) {
            $.ajax({
                type: "GET",
                url: "/api/clientes/get_clientes_list",
                data: {
                    redes_id: redesId
                },
                dataType: "JSON",
                success: function (res) {
                    if (res.data.clientes.length > 0) {
                        clientesList = [];
                        clientesSelectListBox.empty();

                        var cliente = {
                            id: 0,
                            nomeFantasia: "Todos"
                        };

                        clientesList.push(cliente);
                        clientesSelectListBox.prop("disabled", false);

                        res.data.clientes.forEach(cliente => {
                            var cliente = {
                                id: cliente.id,
                                nomeFantasia: cliente.nome_fantasia
                            };

                            clientesList.push(cliente);
                        });

                        clientesList.forEach(cliente => {
                            var option = document.createElement("option");
                            option.value = cliente.id;
                            option.textContent = cliente.nomeFantasia;

                            clientesSelectListBox.append(option);
                        });

                        // Se só tem 2 registros, significa que
                        if (clientesList.length == 2) {
                            clientesSelectedItem = clientesList[1];

                            // Option vazio e mais um Estabelecimento? Desabilita pois só tem uma seleção possível
                            clientesSelectListBox.prop("disabled", true);
                        }

                        if (clientesSelectedItem !== undefined && clientesSelectedItem.id > 0) {
                            clientesSelectListBox.val(clientesSelectedItem.id);
                        }
                    }
                },
                error: function (response) {
                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.error);
                },
                complete: function (response) {
                    clientesSelectListBox.change();
                }
            });
        }

        /**
         * webroot\js\scripts\gotas\relatorio_entrada_saida.js::getRedesList
         *
         * Obtem lista de redes disponível para seleção
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-06
         *
         * return SelectListBox
         */
        function getRedesList() {
            $.ajax({
                type: "GET",
                url: "/api/redes/get_redes_list",
                data: {},
                dataType: "JSON",
                success: function (response) {
                    var data = response.data.redes;

                    if (data.length > 0) {
                        redesSelectListBox.empty();
                        redesList = [];
                        redesSelectedItem = {};
                        redesSelectListBox.prop("disabled", false);
                        redesSelectListBox.prop("readonly", false);

                        var rede = {
                            id: undefined,
                            nome: "<Selecionar>"
                        };

                        redesList.push(rede);

                        data.forEach(item => {
                            var rede = {
                                id: item.id,
                                nome: item.nome_rede
                            };

                            redesList.push(rede);
                        });

                        redesList.forEach(rede => {
                            var option = document.createElement("option");
                            option.value = rede.id;
                            option.textContent = rede.nome;

                            redesSelectListBox.append(option);
                        });

                        if (redesList.length == 2) {
                            // Se é 2 o tamanho, significa que só veio 1 rede do lado do servidor,
                            // que ou é a unica rede disponível ou a única rede selecionável dependendo do perfil do usuário
                            redesSelectedItem = redesList.find(x => x.id == data[0].id);
                            redesSelectListBox.val(redesSelectedItem.id);
                            redesSelectListBox.prop("disabled", true);
                            redesSelectListBox.prop("readonly", true);
                        }
                    }
                },
                error: function (response) {
                    var mensagem = response.responseJSON.mensagem;

                    callModalError(mensagem.message, mensagem.errors);
                },
                complete: function () {
                    redesSelectListBox.change();
                }
            });
        }

        function populateGotasTable(gotasData) {

            tableGotas.pagination({
                pageSize: 10,
                showPrevious: true,
                showNext: true,
                showTotal: true,
                dataSource: gotasData,
                callback: function (data, pagination) {
                    tableGotas.children("tbody").empty();
                    var rows = [];
                    data.forEach(element => {
                        var habilitado = element.habilitado ? "Habilitado" : "Desabilitado";
                        var botaoEditar = geraEditarButton(element.id);
                        var botaoTrocaStatus = !element.habilitado ?
                            geraHabilitarButton(element.id) :
                            geraDesabilitarButton(element.id);

                        var row = "<tr><td>" +
                            element.id +
                            "</td><td>" +
                            element.nomeParametro +
                            "</td><td>" +
                            parseFloat(element.multiplicadorGota).toFixed(3) +
                            "</td><td>" +
                            habilitado +
                            "</td><td>" +
                            botaoEditar +
                            botaoTrocaStatus +
                            "</td></tr>";
                        rows.push(row);
                    });
                    tableGotas.children("tbody").html(rows);

                    geraEditarButtonFunctions("form-btn-edit", data);
                    geraHabilitarButtonFunctions("form-btn-enable", data);
                    geraDesabilitarButtonFunctions("form-btn-disable", data);
                }
            });

        }

        /**
         * Obtem Gotas Cliente
         *
         * Obtem dados de Gotas do estabelecimento selecionado
         *
         * @param {int} clientesId Id do cliente
         *
         * @return void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-11
         */
        function getGotasByCliente(clientesId) {
            var dataSend = {
                clientes_id: clientesId,
            };

            // gotasData = [];
            $.ajax({
                type: "GET",
                url: "/api/gotas/get_gotas_clientes",
                data: dataSend,
                dataType: "JSON",
                success: function (response) {
                    if (response.data.gotas.length > 0) {
                        response.data.gotas.forEach(gota => {
                            gotasData.push(new Gota(
                                gota.id,
                                gota.nome_parametro,
                                gota.multiplicador_gota,
                                gota.habilitado,
                                gota.tipo_cadastro,
                            ));
                        });

                        populateGotasTable(gotasData);
                    }
                },
                error: function (response) {
                    callModalError(response.responseJSON.mensagem.message, response.responseJSON.mensagem.errors);
                }
            });
        }

        // #endregion

        // #region Bindings

        redesSelectListBox.on("change", redesSelectListBoxOnChange);
        clientesSelectListBox.on("change", clientesSelectListBoxOnChange);

        searchBtn.unbind("click");
        searchBtn.on("click", function () {
            if (clientesSelectedItem !== undefined) {
                getGotasByCliente(clientesSelectedItem.id);
            }
        });

        // Inicializa como desabilitado, se selecionar uma rede ou estabelecimento permite filtrar
        searchBtn.prop("disabled", true);

        // #endregion

        // #endregion

        // "Constroi" a tela
        init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
