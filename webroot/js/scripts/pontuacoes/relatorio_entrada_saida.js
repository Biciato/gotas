$(function() {
    // #region Properties

    var form = {};
    var data = {};
    var clientesSelectListBox = $("#clientes-list");
    var clientesList = [];
    var brindesSelectListBox = $("#brindes-list");
    var brindesList = [];

    var tabela = $("#tabela-dados");
    var conteudoTabela = $("#tabela-dados tbody");
    var tipoRelatorio = $("#tipo-relatorio");
    var pesquisarBtn = $("#btn-pesquisar");
    var imprimirBtn = $("#btn-imprimir");

    var dataAtual = moment().format("DD/MM/YYYY");

    var dataInicio = $("#data-inicio").datepicker({
        minView: 2,
        maxView: 2,
        clearBtn: true,
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        forceParse: false,
        language: "pt-BR",
        format: "dd/mm/yyyy",
        initialDate: new Date()
    });
    var dataFim = $("#data-fim").datepicker({
        minView: 2,
        maxView: 2,
        clearBtn: true,
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        forceParse: false,
        language: "pt-BR",
        format: "dd/mm/yyyy",
        initialDate: new Date()
    });

    // #endregion

    // #region Functions

    function init() {
        brindesList = [];
        var option = document.createElement("option");
        option.value = undefined;
        option.textContent = "Selecione um Posto para continuar...";
        option.title = "Selecione um Posto para continuar...";
        brindesList.push(option);

        brindesSelectListBox.empty();
        brindesSelectListBox.append(brindesList);

        // Inicializa campos date

        dataInicio.datepicker().datepicker("setDate", dataAtual);
        dataFim.datepicker().datepicker("setDate", dataAtual);

        // Dispara todos os eventos que precisam de inicializar
        dataInicioOnChange();
        dataFimOnChange();
        tipoRelatorioOnChange();
        getClientesList();
        clientesSelectListBoxOnChange();
        brindesSelectListBoxOnChange();
    }


    /**
     * relatorio_entrada_saida.js::brindesSelectListBoxOnChange()
     *
     * Comportamento ao trocar o brinde selecionado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-11
     *
     * @return void
     */
    function brindesSelectListBoxOnChange() {
        var brinde = parseInt(brindesSelectListBox.val());

        brinde = isNaN(brinde) ? undefined : brinde;
        form.brindesId = brinde;
        console.log(form);
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
        var clienteSelected = this.value;

        if (isNaN(clienteSelected)) {
            clienteSelected = parseInt($("#cliente-selected").val());
        }

        if (!isNaN(clienteSelected)) {
            form.clientesId = clienteSelected;
            // Obtem Brindes
            getBrindesList(clienteSelected);
        }
    }

    function dataInicioOnChange() {
        var date = this.value;

        if (date !== undefined) {
            date = moment(this.value, "DD/MM/YYYY").format("YYYY-MM-DD");
            form.dataInicio = date;
        }
    }

    function dataFimOnChange() {
        var date = this.value;

        if (date !== undefined) {
            date = moment(this.value, "DD/MM/YYYY").format("YYYY-MM-DD");
            form.dataFim = date;
        }
    }

    function tipoRelatorioOnChange() {
        form.tipoRelatorio = tipoRelatorio.val();
    }

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

    /**
     * Obtem os dados de relatório do servidor
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-12
     *
     * @param {int} clientesId Id do Cliente
     * @param {int} brindesId id do Brinde
     * @param {datetime} dataInicio Data Inicio
     * @param {datetime} dataFim DataFim
     * @param {string} tipoRelatorio Analítico / Sintético
     *
     * @returns HtmlTable
     */
    function getEntradaSaida(
        clientesId,
        brindesId,
        dataInicio,
        dataFim,
        tipoRelatorio
    ) {
        // Validação
        var dataInicioEnvio = moment(dataInicio);
        var dataFimEnvio = moment(dataFim);

        if(!dataInicioEnvio.isValid()) {
            dataInicioEnvio = undefined;
        } else {
            dataInicioEnvio = dataInicio;
        }

        if (!dataFimEnvio.isValid()) {
            dataFimEnvio = undefined;
        } else {
            dataFimEnvio =  dataFim;
        }

        var data = {
            clientes_id: clientesId,
            brindes_id: brindesId,
            data_inicio: dataInicioEnvio,
            data_fim: dataFimEnvio,
            tipo_relatorio: tipoRelatorio
        };

        callLoaderAnimation();
        $.ajax({
            type: "GET",
            url: "/api/pontuacoes/get_pontuacoes_relatorio_entrada_saida",
            data: data,
            dataType: "JSON",
            success: function(response) {
                console.log(response);

                var data = response.data.pontuacoes_report;

                if (data.length > 0) {
                    conteudoTabela.empty();

                    $(tabela).hide();
                    $(tabela).fadeIn(500);
                }

                var rows = [];

                if(form.tipoRelatorio == "Analítico") {

                } else {
                    data.forEach(element => {
                        // Dados do posto
                        var rowCliente = document.createElement("tr");

                        var cellLabelCliente = document.createElement("td");
                        var labelCliente = document.createElement("span");
                        labelCliente.textContent = "Posto: ";
                        cellLabelCliente.append(labelCliente);

                        var cellInfoCliente = document.createElement("td");
                        var infoCliente = document.createElement("span");
                        infoCliente.textContent = element.cliente.nome_fantasia;

                        cellInfoCliente.append(infoCliente);

                        rowCliente.append(cellLabelCliente);
                        rowCliente.append(cellInfoCliente);

                        // Cabeçalho de periodo

                        var rowHeaderPeriodo = document.createElement("tr");

                        var cellLabelPeriodo = document.createElement("td");
                        var labelPeriodo = document.createElement("span");
                        labelPeriodo.textContent = "Período";

                        cellLabelPeriodo.append(labelPeriodo);

                        var cellLabelEntrada = document.createElement("td");
                        var labelEntrada = document.createElement("span");
                        labelEntrada.textContent = "Entrada";

                        cellLabelEntrada.append(labelEntrada);

                        var cellLabelSaida = document.createElement("td");
                        var labelSaida = document.createElement("span");
                        labelSaida.textContent = "Saida";

                        cellLabelSaida.append(labelSaida);
                        rowHeaderPeriodo.append(cellLabelPeriodo);
                        rowHeaderPeriodo.append(cellLabelEntrada);
                        rowHeaderPeriodo.append(cellLabelSaida);

                        // Periodos e valores

                        var pontuacoesEntradas = element.pontuacoes_entradas;
                        var pontuacoesSaidas = element.pontuacoes_saidas;
                        var length = pontuacoesEntradas;

                        var rowsDadosPeriodos = [];

                        for (let index = 0; index < pontuacoesEntradas.length; index++) {
                            var item = {
                                periodo: pontuacoesEntradas[index].periodo,
                                gotasEntradas: pontuacoesEntradas[index].qte_gotas,
                                gotasSaidas: pontuacoesSaidas[index].qte_gotas
                            };

                            var rowPeriodo = document.createElement("tr");

                            var labelItemPeriodo = document.createElement("span");
                            labelItemPeriodo.textContent = item.periodo;

                            var cellItemLabelPeriodo = document.createElement("td");
                            cellItemLabelPeriodo.append(labelItemPeriodo);
                            cellItemLabelPeriodo.classList.add("text-right");

                            var textEntrada = document.createElement("span");
                            textEntrada.textContent = item.gotasEntradas;

                            var cellItemEntrada = document.createElement("td");
                            cellItemEntrada.append(textEntrada);
                            cellItemEntrada.classList.add("text-right");

                            var textSaida = document.createElement("span");
                            textSaida.textContent = item.gotasSaidas;

                            var cellItemSaida = document.createElement("td");
                            cellItemSaida.append(textSaida);
                            cellItemSaida.classList.add("text-right");

                            rowPeriodo.append(cellItemLabelPeriodo);
                            rowPeriodo.append(cellItemEntrada);
                            rowPeriodo.append(cellItemSaida);

                            rowsDadosPeriodos.push(rowPeriodo);
                        }

                        // Linha de soma

                        var rowSomaPeriodo = document.createElement("tr");

                        var labelSomaPeriodo = document.createElement("span");
                        labelSomaPeriodo.textContent = "Soma Estabelecimento";

                        var cellLabelSomaPeriodo = document.createElement("td");
                        cellLabelSomaPeriodo.append(labelSomaPeriodo);

                        var textSomaPeriodoEntrada = document.createElement("span");
                        textSomaPeriodoEntrada.textContent = element.soma_entradas;

                        var cellTextSomaPeriodoEntrada = document.createElement("td");
                        cellTextSomaPeriodoEntrada.append(textSomaPeriodoEntrada);
                        cellTextSomaPeriodoEntrada.classList.add("text-right");

                        var textSomaPeriodoSaida = document.createElement("span");
                        textSomaPeriodoSaida.textContent = element.soma_saidas;

                        var cellTextSomaPeriodoSaida = document.createElement("td");
                        cellTextSomaPeriodoSaida.append(textSomaPeriodoSaida);
                        cellTextSomaPeriodoSaida.classList.add("text-right");

                        rowSomaPeriodo.append(cellLabelSomaPeriodo);
                        rowSomaPeriodo.append(cellTextSomaPeriodoEntrada);
                        rowSomaPeriodo.append(cellTextSomaPeriodoSaida);


                        rows.push(rowCliente);
                        rows.push(rowHeaderPeriodo);

                        rowsDadosPeriodos.forEach(item => {
                            rows.push(item);
                        });

                        rows.push(rowSomaPeriodo);
                    });

                }
                conteudoTabela.append(rows);
            },
            error: function(response) {
                closeLoaderAnimation();
            },
            complete: function(response) {
                closeLoaderAnimation();
            }
        });
    }

    // #endregion

    // #region Bindings

    brindesSelectListBox.on("change", brindesSelectListBoxOnChange);
    clientesSelectListBox.on("change", clientesSelectListBoxOnChange);

    dataInicio.on("change", dataInicioOnChange);
    dataFim.on("change", dataFimOnChange);

    tipoRelatorio.on("change", tipoRelatorioOnChange);

    $(pesquisarBtn).on("click", function() {
        console.log(form);
        getEntradaSaida(
            form.clientesId,
            form.brindesId,
            form.dataInicio,
            form.dataFim,
            form.tipoRelatorio
        );
    });
    // pesquisarBtn.on("click", function() { alert("oi") });
    console.log("oi");

    // #endregion

    // #endregion

    // "Constroi" a tela
    init();

});
