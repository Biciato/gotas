/**
 * Arquivo de 'controller' para src\Template\Gotas\gotas_redes.ctp
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
    var gotasList = [];
    var gotasSelectListBox = $("#gotas-list");
    var gotasSelectedItem = {};
    var redesList = [];
    var redesSelectListBox = $("#redes-list");
    var redesSelectedItem = {};

    var tabela = $("#tabela-dados");

    var pesquisarBtn = $("#btn-pesquisar");
    var imprimirBtn = $("#btn-imprimir");

    // #endregion

    // #region Functions

    function init() {

        getRedesList();
    }

    /**
     * relatorio_entrada_saida.js::gotasSelectListBoxOnChange()
     *
     * Comportamento ao trocar o brinde selecionado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-11
     *
     * @return void
     */
    function gotasSelectListBoxOnChange() {
        var gota = parseInt(gotasSelectListBox.val());

        gota = isNaN(gota) ? 0 : gota;
        form.gotasId = gota;
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

        // Obtem Brindes e Gotas SE estabelecimento selecionado

        if (clientesSelectedItem !== undefined && clientesSelectedItem.id > 0) {
            getBrindesList(clientesId);
            getGotasList(clientesId);
        } else {
            var option1 = document.createElement("option");
            option1.value = 0;
            option1.textContent = "Selecione um Estabelecimento para continuar...";
            option1.title = "Selecione um Estabelecimento para continuar...";
            gotasSelectListBox.empty();
            gotasSelectListBox.append(option1);
            gotasList.push(item);
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

        if (redesSelectedItem.id > 0) {
            getClientesList(redesSelectedItem.id);
        } else {
            var option = document.createElement("option");
            option.value = 0;
            option.textContent = "Selecione uma Rede para continuar...";
            option.title = "Selecione uma Rede para continuar...";

            clientesList = [];
            clientesSelectListBox.empty();
            clientesSelectListBox.append(option);

            gotasList = [];
            var option1 = document.createElement("option");
            option1.value = 0;
            option1.textContent = "Selecione um Estabelecimento para continuar...";
            option1.title = "Selecione um Estabelecimento para continuar...";
            gotasSelectListBox.empty();
            gotasSelectListBox.append(option1);
        }
    }

    // #region Get / Set REST Services

    /**
     * webroot\js\scripts\gotas\relatorio_entrada_saida.js::getClientesList
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
                brindesSelectListBox.change();

            }
        });
    }

    /**
     * Obtem Gotas Cliente
     *
     * Obtem dados de Gotas do estabelecimento selecionado
     *
     * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::getGotasList()
     *
     * @param {int} clientesId Id do cliente
     *
     * @return void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-11
     */
    function getGotasList(clientesId) {
        var data = {
            clientes_id: clientesId
        };

        $.ajax({
            type: "GET",
            url: "/api/gotas/get_gotas_clientes",
            data: data,
            dataType: "JSON",
            success: function (response) {
                gotasSelectListBox.empty();
                gotasList = [];

                var gota = {
                    id: 0,
                    nomeParametro: "<Todos>"
                };

                gotasList.push(gota);

                response.data.gotas.forEach(element => {
                    var gota = {
                        id: element.id,
                        nomeParametro: element.nome_parametro
                    };

                    gotasList.push(gota);
                });

                gotasList.forEach(gota => {
                    var option = document.createElement("option");
                    option.value = gota.id;
                    option.textContent = gota.nomeParametro;
                    gotasSelectListBox.append(option);
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

    // #endregion

    // #region Bindings

    redesSelectListBox.on("change", redesSelectListBoxOnChange);
    clientesSelectListBox.on("change", clientesSelectListBoxOnChange);
    gotasSelectListBox.on("change", gotasSelectListBoxOnChange);
    dataInicio.on("change", dataInicioOnChange);
    dataFim.on("change", dataFimOnChange);
    tipoRelatorio.on("change", tipoRelatorioOnChange);

    imprimirBtn.unbind("click");
    imprimirBtn.on("click", pesquisar);

    // #endregion

    // #endregion

    // "Constroi" a tela
    init();
})
.ajaxStart(callLoaderAnimation)
.ajaxStop(closeLoaderAnimation)
.ajaxError(closeLoaderAnimation);
