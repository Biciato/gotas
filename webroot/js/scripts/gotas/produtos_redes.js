/**
 * Arquivo de 'controller' para src\Template\Gotas\produtos_redes.ctp
 *
 * @file webroot\js\scripts\gotas\gotas_redes.js
 * @author Gustavo Souza Gonçalves
 * @date 2019-12-06
 */


$(function () {
    'use strict';
    // #region Properties

    var form = {};
    var clientesSelectListBox = $("#clientes-list");
    var clientesSelectedItem = {};
    var clientesList = [];
    var gotasData = [];
    var gotasSelectedItem = {};
    var redesList = [];
    var redesSelectListBox = $("#redes-list");
    var redesSelectedItem = {};

    var table = $("#data-table");

    var searchBtn = $("#btn-search");
    var clearBtn = $("#btn-clear");

    // #endregion

    // #region Functions

    function init() {

        getRedesList();
    }

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
    function getGotasData(clientesId) {
        var data = {
            clientes_id: clientesId
        };

        $.ajax({
            type: "GET",
            url: "/api/gotas/get_gotas_clientes",
            data: data,
            dataType: "JSON",
            success: function (response) {
                gotasData = [];

                var gota = {
                    id: 0,
                    nomeParametro: "<Todos>"
                };

                gotasData.push(gota);

                response.data.gotas.forEach(element => {
                    var gota = {
                        id: element.id,
                        nomeParametro: element.nome_parametro
                    };

                    gotasData.push(gota);

                    tabelaDados.empty();
                    tabela.pagination({
                        pageSize: 10,
                        showPrevious: true,
                        showNext: true,
                        dataSource: function (done) {
                            done(data);
                        },
                        callback: function (data, pagination) {
                            var rows = [];
                            data.forEach(element => {
                                var importar = element.importar ? "Sim" : "Não";
                                var botaoEditar = geraEditarButton(element.id);
                                var botaoTrocaStatus = !element.importar ?
                                    geraHabilitarButton(element.id) :
                                    geraDesabilitarButton(element.id);

                                var row =
                                    "<tr><td>" +
                                    element.nomeParametro +
                                    "</td><td>" +
                                    element.multiplicadorGota +
                                    "</td><td>" +
                                    importar +
                                    "</td><td>" +
                                    botaoEditar +
                                    botaoTrocaStatus +
                                    "</td></tr>";
                                rows.push(row);
                            });

                            tabelaDados.append(rows);

                            geraEditarButtonFunctions("form-btn-edit", data);
                            geraHabilitarButtonFunctions("form-btn-enable", data);
                            geraDesabilitarButtonFunctions("form-btn-disable", data);
                        }
                    });
                });

                gotasData.forEach(gota => {
                    var option = document.createElement("option");
                    option.value = gota.id;
                    option.textContent = gota.nomeParametro;
                });
            },
            error: function (response) {
                var mensagem = response.responseJSON.mensagem;

                callModalError(mensagem.message, mensagem.errors);
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

    function searchGotasByFilter(clientesId) {
        var dataSend = {

        };

        gotasData = [];

        $.ajax({
            type: "GET",
            url: "/api/gotas/get_gotas_clientes",
            data: dataSend,
            dataType: "JSON",
            success: function (response) {
                if (response.data.gotas.length > 0) {
                    table.empty();


                    response.data.gotas.forEach(gota => {
                        // gotasData.push(new gota() {
                        //     id: gota.id,
                        //     nomeParametro: gota.nome_parametro,
                        //     multiplicadorGota: gota.multiplicador_gota,

                        // });
                        var item = {
                            id: gota.id,
                            nomeParametro: gota.nome_parametro,
                            multiplicadorGota: gota.multiplicador_gota
                        }
                        gotasData.push(item);
                    })

                }

            }
        });

    }

    // #endregion

    // #region Bindings

    redesSelectListBox.on("change", redesSelectListBoxOnChange);
    clientesSelectListBox.on("change", clientesSelectListBoxOnChange);

    searchBtn.unbind("click");
    searchBtn.on("click", searchGotasByFilter);

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
