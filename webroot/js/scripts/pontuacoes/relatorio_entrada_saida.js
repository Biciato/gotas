$(function() {
    // #region Properties

    var clientesSelectListBox = $("#clientes-list");
    var clientesList = [];
    var brindesSelectListBox = $("#brindes-list");
    var brindesList = [];

    var dataInicio = null;
    var dataFim = null;

    // #endregion

    // #region Functions

    function init() {
        brindesList = [];
        var option = document.createElement("option");
        option.value = undefined;
        // option.textContent = "Selecionar";
        option.textContent = "Selecione um Posto para continuar...";
        option.title = "Selecione um Posto para continuar...";
        brindesList.push(option);

        brindesSelectListBox.empty();
        brindesSelectListBox.append(brindesList);

        // Inicializa campos date

        var dataAtual = moment();
        // var dataInicio = $("#data-inicio").dateTimePicker();
        dataInicio = initializeDatePicker("data-inicio", "data-inicio-e ", dataAtual );
        dataFim = initializeDatePicker("data-fim", "data-fim-envio", dataAtual, null, dataAtual);

        getClientesList();

        clientesSelectListBoxOnChange();
    }

    init();

    function clientesSelectListBoxOnChange() {
        var clienteSelected = this.value;

        if (isNaN(clienteSelected)) {
            clienteSelected = $("#cliente-selected").val();
        }

        if (clienteSelected !== "undefined") {
            // Obtem Brindes
            getBrindesList(clienteSelected);
        }
    }

    clientesSelectListBox.on("change", clientesSelectListBoxOnChange);

    // #region Get / Set REST Services

    function getBrindesList(clientesId) {
        callLoaderAnimation();
        $.ajax({
            type: "POST",
            url: "/api/brindes/get_brindes_unidade",
            data: {
                clientes_id: clientesId
            },
            dataType: "JSON",
            success: function(response) {
                console.log(response);

                if (response.brindes !== undefined) {
                    brindesSelectListBox.empty();

                    var data = response.brindes.data;
                    var collection = [];
                    var options = [];
                    var option = document.createElement("option");
                    option.title = "Selecionar Brinde para filtro específico";
                    option.textContent = "Todos";
                    options.push(option);

                    data.forEach(dataItem => {
                        var option = document.createElement("option");
                        var item = {
                            id: dataItem.id,
                            nome: dataItem.nome_brinde_detalhado
                        };

                        option.value = item.id;
                        option.textContent = item.nome;
                        collection.push(item);
                        options.push(option);
                    });

                    brindesSelectListBox.append(options);
                    brindesList = collection;
                }
            },
            error: function(response) {
                callModalError(
                    response.mensagem.message,
                    response.mensagem.error
                );
            },
            complete: function(response) {
                closeLoaderAnimation();
            }
        });
    }

    /**
     * webroot\js\scripts\gotas\relatorio_entrada_saida.js::getClientesList
     *
     * Obtem lista de clientes disponível para seleção
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-06
     *
     * return SelectListBox
     */
    function getClientesList() {
        callLoaderAnimation();
        $.ajax({
            type: "GET",
            url: "/api/clientes/get_clientes_list",
            data: {},
            dataType: "JSON",
            success: function(res) {
                if (res.clientes.length > 0) {
                    clientesList = [];
                    clientesSelectListBox.empty();

                    var option = document.createElement("option");
                    option.value = undefined;
                    option.textContent = "Selecionar";

                    clientesList.push(option);

                    res.clientes.forEach(cliente => {
                        var cliente = {
                            id: cliente.id,
                            value: cliente.nome_fantasia
                        };

                        var option = document.createElement("option");
                        option.value = cliente.id;
                        option.textContent = cliente.value;

                        clientesList.push(option);
                    });

                    clientesSelectListBox.append(clientesList);
                    var clienteSelected = $("#cliente-selected").val();

                    if (clienteSelected !== undefined && clienteSelected > 0) {
                        clientesSelectListBox.val(clienteSelected);
                    }

                    // Option vazio e mais um posto? Desabilita pois só tem uma seleção possível
                    if (clientesList.length == 2) {
                        $(clientesSelectListBox).attr("disabled", true);
                    }
                }
                console.log(res);

                closeLoaderAnimation();
            },
            error: function(res) {
                console.log(res);
                closeLoaderAnimation();
            }
        });
    }

    function getEntradaSaida(clientesId, brindesId, dataInicio, dataFim) {
        var data = {
            clientes_id: clientesId,
            dataInicio: dataInicio,
            dataFim: dataFim
        };

        if (brindesId !== undefined) {
            data.brindes_id = brindesId;
        }

        callLoaderAnimation();
        $.ajax({
            type: "GET",
            url: "/api/pontuacoes/get_gotas_entrada_saida",
            data: data,
            dataType: "JSON",
            success: function (response) {

            }, complete: function (response) {
                closeLoaderAnimation();
            }
        });

    }

    // #endregion

    // #endregion
});
