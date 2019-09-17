/**
 * Arquivo de funções para src\Template\Pontuacoes\relatorio_gotas.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-09-17
 */

$(function() {
    'use strict';
    // #region Properties

    var form = {};
    var clientesSelectListBox = $("#clientes-list");
    var clientesList = [];
    var funcionariosSelectListBox = $("#brindes-list");
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
        option.textContent = "Selecione um Estabelecimento para continuar...";
        option.title = "Selecione um Estabelecimento para continuar...";

        brindesList.push(option);
        funcionariosSelectListBox.empty();
        funcionariosSelectListBox.append(brindesList);

        // Inicializa campos date
        dataInicio.datepicker().datepicker("setDate", dataAtual);
        dataFim.datepicker().datepicker("setDate", dataAtual);

        // Dispara todos os eventos que precisam de inicializar
        // dataInicioOnChange();
        // dataFimOnChange();
        tipoRelatorioOnChange();
        getClientesList();


        // Desabilita botão de imprimir até que usuário faça alguma consulta
        imprimirBtn.addClass("disabled");
        imprimirBtn.addClass("readonly");
        imprimirBtn.unbind("click");
    }

    /**
     * relatorio_gotas.js::brindesSelectListBoxOnChange()
     *
     * Comportamento ao trocar o brinde selecionado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-11
     *
     * @return void
     */
    function brindesSelectListBoxOnChange() {
        var brinde = parseInt(funcionariosSelectListBox.val());

        brinde = isNaN(brinde) ? undefined : brinde;
        form.brindesId = brinde;
    }

    /**
     * relatorio_gotas.js::clientesSelectListBoxOnChange()
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

        form.clientesId = isNaN(clienteSelected) ? 0 : clienteSelected;

        // Obtem Brindes
        getFuncionariosList(form.clientesId);
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::dataInicioOnChange
     *
     * Comportamento ao atualizar campo de data
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-12
     */
    function dataInicioOnChange() {
        var date = this.value;

        if (date !== undefined) {
            if (date.length == 8 && date.indexOf("/") == -1) {
                date = moment(date, "DDMMYYYY").format("DD/MM/YYYY");
                this.value = date;
            }

            date = moment(this.value, "DD/MM/YYYY").format("YYYY-MM-DD");
            form.dataInicio = date;
        }
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::dataFimOnChange
     *
     * Comportamento ao atualizar campo de data
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-12
     */
    function dataFimOnChange() {
        var date = this.value;

        if (date !== undefined) {
            if (date.length == 8 && date.indexOf("/") == -1) {
                date = moment(date, "DDMMYYYY").format("DD/MM/YYYY");
                this.value = date;
            }

            date = moment(this.value, "DD/MM/YYYY").format("YYYY-MM-DD");
            form.dataFim = date;
        }
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::imprimirRelatorio
     *
     * Imprime relatório
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-12
     */
    function imprimirRelatorio() {
        setTimeout(tabela.printThis({
            importCss: false
        }), 100);
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::tipoRelatorioOnChange
     *
     * Comportamento ao trocar o tipo de relatório
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-12
     */
    function tipoRelatorioOnChange() {
        form.tipoRelatorio = tipoRelatorio.val();
    }

    // #region Get / Set REST Services

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::getFuncionariosList
     *
     * Obtem lista de Funcionários do posto(s) selecionado(s)
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-17
     *
     * @returns SelectListBox Lista de Seleção
     */
    function getFuncionariosList(clientesId) {
        callLoaderAnimation();
        $.ajax({
            type: "GET",
            url: "/api/usuarios/get_funcionarios_list",
            data: {
                clientes_id: clientesId
            },
            dataType: "JSON",
            success: function(response) {

                if (response.data !== undefined) {
                    funcionariosSelectListBox.empty();

                    var data = response.data.brindes;
                    var collection = [];
                    var options = [];
                    var option = document.createElement("option");
                    option.title = "Selecionar Funcionário para filtro específico";
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

                    funcionariosSelectListBox.append(options);
                    brindesList = collection;
                }
            },
            error: function(response) {
                var data = response.responseJSON;
                callModalError(data.mensagem.message, data.mensagem.error);
            },
            complete: function(response) {
                closeLoaderAnimation();
            }
        });
    }

    /**
     * webroot\js\scripts\gotas\relatorio_gotas.js::getClientesList
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
                    option.textContent = "Todos";

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

                    // Option vazio e mais um Estabelecimento? Desabilita pois só tem uma seleção possível
                    if (clientesList.length == 2) {
                        $(clientesSelectListBox).attr("disabled", true);
                    }
                }

                closeLoaderAnimation();
            },
            error: function(response) {
                var data = response.responseJSON;
                callModalError(data.mensagem.message, data.mensagem.error);
            },
            complete: function(response) {
                closeLoaderAnimation();
                clientesSelectListBoxOnChange();
                brindesSelectListBoxOnChange();
            }
        });
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::getDataPontuacoesEntradaSaida
     *
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
    function getDataPontuacoesEntradaSaida(clientesId, brindesId, dataInicio, dataFim, tipoRelatorio) {
        // Validação
        var dataInicioEnvio = moment(dataInicio);
        var dataFimEnvio = moment(dataFim);

        if (!dataInicioEnvio.isValid()) {
            dataInicioEnvio = undefined;
        } else {
            dataInicioEnvio = dataInicio;
        }

        if (!dataFimEnvio.isValid()) {
            dataFimEnvio = undefined;
        } else {
            dataFimEnvio = dataFim;
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
            url: "/api/pontuacoes/get_pontuacoes_relatorio_gotas",
            data: data,
            dataType: "JSON",
            success: function(response) {
                imprimirBtn.removeClass("disabled");
                imprimirBtn.removeClass("readonly");
                imprimirBtn.on("click", imprimirRelatorio);

                var data = response.data.pontuacoes_report;

                if (data.pontuacoes.length > 0) {
                    conteudoTabela.empty();

                    $(tabela).hide();
                    $(tabela).fadeIn(500);
                }

                var rows = [];

                if (form.tipoRelatorio == "Analítico") {
                    data.pontuacoes.forEach(element => {
                        // Dados do Estabelecimento
                        var rowCliente = document.createElement("tr");

                        var labelCliente = document.createElement("strong");
                        labelCliente.textContent = "Estabelecimento: ";
                        var cellLabelCliente = document.createElement("td");
                        cellLabelCliente.classList.add("font-weight-bold");
                        cellLabelCliente.append(labelCliente);

                        var cellInfoCliente = document.createElement("td");
                        var infoCliente = document.createElement("strong");
                        infoCliente.textContent = element.cliente.nome_fantasia + " / " + element.cliente.razao_social;
                        cellInfoCliente.colSpan = 6;
                        cellInfoCliente.classList.add("text-right");

                        cellInfoCliente.append(infoCliente);

                        rowCliente.append(cellLabelCliente);
                        rowCliente.append(cellInfoCliente);

                        rows.push(rowCliente);

                        // Fim dados Estabelecimento

                        // Linhas periodos

                        var rowsPeriodos = [];
                        var pontuacoesLength = element.pontuacoes_entradas.length;
                        var pontuacoesEntradas = element.pontuacoes_entradas;
                        var pontuacoesSaidas = element.pontuacoes_saidas;

                        for (var pontuacoesIndex = 0; pontuacoesIndex < pontuacoesLength; pontuacoesIndex++) {
                            var pontuacoesEntradaPeriodoList = pontuacoesEntradas[pontuacoesIndex];
                            var pontuacoesSaidaPeriodoList = pontuacoesSaidas[pontuacoesIndex];

                            var pontuacoesDataLength = pontuacoesEntradaPeriodoList.data.length;
                            var pontuacoesEntradaDataList = pontuacoesEntradaPeriodoList.data;
                            var pontuacoesSaidaDataList = pontuacoesSaidaPeriodoList.data;

                            var mesAtual = '';
                            var ultimaData = '';

                            for (var indexData = 0; indexData < pontuacoesDataLength; indexData++) {
                                var entrada = pontuacoesEntradaDataList[indexData];
                                var saida = pontuacoesSaidaDataList[indexData];
                                var periodoAtual = moment(entrada.periodo, "YYYY-MM-DD").format("DD/MM/YYYY");

                                // O header deve ser construído se a data muda
                                if (ultimaData !== periodoAtual) {
                                    ultimaData = periodoAtual;
                                    // Linha que indica o cabeçalho dos períodos
                                    var rowPeriodo = document.createElement("tr");

                                    var cellPeriodoLabel = document.createElement("td");
                                    var labelPeriodo = document.createElement("strong");
                                    labelPeriodo.textContent = "Data";
                                    cellPeriodoLabel.append(labelPeriodo);

                                    var cellPeriodoTextoLabel = document.createElement("td");
                                    var labelPeriodoValue = document.createElement("strong");

                                    mesAtual = moment(entrada.periodo, "YYYY-MM-DD").format("MM/YYYY");
                                    labelPeriodoValue.textContent = periodoAtual;
                                    cellPeriodoTextoLabel.append(labelPeriodoValue);
                                    cellPeriodoTextoLabel.colSpan = 6;
                                    cellPeriodoTextoLabel.classList.add("text-right");

                                    rowPeriodo.append(cellPeriodoLabel);
                                    rowPeriodo.append(cellPeriodoTextoLabel);

                                    rowsPeriodos.push(rowPeriodo);

                                    // linha que indica o cabeçalho das colunas quem compõem o conjunto dos períodos
                                    var headerDadosPeriodoRow = document.createElement("tr");

                                    var cellLabelGota = document.createElement("td");
                                    var textlabelGota = document.createElement("strong");
                                    textlabelGota.textContent = "Gota:";
                                    cellLabelGota.append(textlabelGota);

                                    var cellLabelUsuarioEntrada = document.createElement("td");
                                    var textUsuarioEntrada = document.createElement("strong");
                                    textUsuarioEntrada.textContent = "Usuário:";
                                    cellLabelUsuarioEntrada.append(textUsuarioEntrada);

                                    var cellLabelGotasEntrada = document.createElement("td");
                                    var textEntradaGotas = document.createElement("strong");
                                    textEntradaGotas.textContent = "Gotas";
                                    cellLabelGotasEntrada.append(textEntradaGotas);

                                    var cellLabelUsuarioSaida = document.createElement("td");
                                    var textUsuarioSaida = document.createElement("strong");
                                    textUsuarioSaida.textContent = "Usuário:";
                                    cellLabelUsuarioSaida.append(textUsuarioSaida);

                                    var cellLabelBrindesSaida = document.createElement("td");
                                    var textBrindesSaida = document.createElement("strong");
                                    textBrindesSaida.textContent = "Brindes";
                                    cellLabelBrindesSaida.append(textBrindesSaida);

                                    var cellLabelGotasSaida = document.createElement("td");
                                    var textSaidaGotas = document.createElement("strong");
                                    textSaidaGotas.textContent = "Gotas";
                                    cellLabelGotasSaida.append(textSaidaGotas);

                                    headerDadosPeriodoRow.append(document.createElement("td"));
                                    headerDadosPeriodoRow.append(cellLabelGota);
                                    headerDadosPeriodoRow.append(cellLabelUsuarioEntrada);
                                    headerDadosPeriodoRow.append(cellLabelGotasEntrada);
                                    headerDadosPeriodoRow.append(cellLabelUsuarioSaida);
                                    headerDadosPeriodoRow.append(cellLabelBrindesSaida);
                                    headerDadosPeriodoRow.append(cellLabelGotasSaida);

                                    rowsPeriodos.push(headerDadosPeriodoRow);
                                }

                                // Info de entrada
                                var row = document.createElement("tr");
                                var cellEmpty = document.createElement("td");

                                row.append(cellEmpty);

                                var cellEntradaGota = document.createElement("td");
                                var labelEntradaGota = document.createElement("span");
                                labelEntradaGota.textContent = entrada.gota !== undefined ? entrada.gota.nome_parametro : "";
                                cellEntradaGota.append(labelEntradaGota);

                                var cellEntradaUsuario = document.createElement("td");
                                var labelEntradaUsuario = document.createElement("span");
                                labelEntradaUsuario.textContent = entrada.usuario !== undefined ? entrada.usuario.nome : "";
                                cellEntradaUsuario.append(labelEntradaUsuario);

                                var cellEntradaQteGota = document.createElement("td");
                                var labelEntradaQteGota = document.createElement("span");
                                labelEntradaQteGota.textContent = entrada.qte_gotas;
                                cellEntradaQteGota.classList.add("text-right");
                                cellEntradaQteGota.append(labelEntradaQteGota);

                                // Info de Saida
                                var cellSaidaUsuario = document.createElement("td");
                                var labelSaidaUsuario = document.createElement("span");
                                labelSaidaUsuario = saida.usuario !== undefined ? saida.usuario.nome : "";
                                cellSaidaUsuario.append(labelSaidaUsuario);

                                var cellSaidaBrinde = document.createElement("td");
                                var labelSaidaBrinde = document.createElement("span");
                                labelSaidaBrinde.textContent = saida.brinde !== undefined ? saida.brinde.nome_brinde_detalhado : "";
                                cellSaidaBrinde.append(labelSaidaBrinde);

                                var cellSaidaQteGota = document.createElement("td");
                                var labelSaidaQteGota = document.createElement("span");
                                labelSaidaQteGota.textContent = saida.qte_gotas;
                                cellSaidaQteGota.classList.add("text-right");
                                cellSaidaQteGota.append(labelSaidaQteGota);

                                row.append(cellEntradaGota);
                                row.append(cellEntradaUsuario);
                                row.append(cellEntradaQteGota);
                                row.append(cellSaidaUsuario);
                                row.append(cellSaidaBrinde);
                                row.append(cellSaidaQteGota);

                                rowsPeriodos.push(row);
                            }

                            // Total periodo

                            var rowTotalPeriodo = document.createElement("tr");
                            var cellLabelTotal = document.createElement("td");
                            var labelTotal = document.createElement("strong");

                            labelTotal.textContent = "Total Período: " + mesAtual;
                            cellLabelTotal.append(labelTotal);

                            var cellLabelEntradaTotal = document.createElement("td");
                            var labelEntradaTotal = document.createElement("strong");
                            labelEntradaTotal.textContent = pontuacoesEntradaPeriodoList.soma_entradas;
                            cellLabelEntradaTotal.classList.add("text-right");
                            cellLabelEntradaTotal.colSpan = 3;
                            cellLabelEntradaTotal.append(labelEntradaTotal);

                            var cellLabelSaidaTotal = document.createElement("td");
                            var labelSaidaTotal = document.createElement("strong");
                            labelSaidaTotal.textContent = pontuacoesSaidaPeriodoList.soma_saidas;
                            cellLabelSaidaTotal.classList.add("text-right");
                            cellLabelSaidaTotal.colSpan = 3;
                            cellLabelSaidaTotal.append(labelSaidaTotal);

                            rowTotalPeriodo.append(cellLabelTotal);
                            rowTotalPeriodo.append(cellLabelEntradaTotal);
                            rowTotalPeriodo.append(cellLabelSaidaTotal);

                            rowsPeriodos.push(rowTotalPeriodo);
                        }

                        if(pontuacoesLength == 0) {
                            // Se não teve registro, adiciona uma linha informando que não teve movimentação

                            var rowEmpty = document.createElement("tr");
                            var cell = document.createElement("td");
                            var label = document.createElement("strong");

                            label.textContent = "Não há registros à serem exibidos!";
                            cell.append(label);
                            cell.colSpan = 7;
                            cell.classList.add("text-center");
                            rowEmpty.append(cell);
                            rowsPeriodos.push(rowEmpty);
                        }

                        // Linhas Periodo

                        // Linha Total Geral

                        var rowTotal = document.createElement("tr");
                        var cellLabelTotal = document.createElement("td");
                        var labelTotal = document.createElement("strong");

                        labelTotal.classList.add("text-bold");
                        labelTotal.textContent = "Total Geral";
                        cellLabelTotal.append(labelTotal);

                        var textTotalEntradas = document.createElement("strong");
                        var cellTotalEntradas = document.createElement("td");
                        textTotalEntradas.textContent = data.total_entradas;
                        cellTotalEntradas.classList.add("text-right");
                        cellTotalEntradas.colSpan = 3;
                        cellTotalEntradas.append(textTotalEntradas);

                        var textTotalSaidas = document.createElement("strong");
                        var cellTotalSaidas = document.createElement("td");
                        textTotalSaidas.textContent = data.total_saidas;
                        cellTotalSaidas.classList.add("text-right");
                        cellTotalSaidas.colSpan = 3;
                        cellTotalSaidas.append(textTotalSaidas);

                        rowTotal.append(cellLabelTotal);
                        rowTotal.append(cellTotalEntradas);
                        rowTotal.append(cellTotalSaidas);

                        rowsPeriodos.push(rowTotal);

                        rowsPeriodos.forEach(element => {
                            rows.push(element);
                        });
                    });
                } else {
                    data.pontuacoes.forEach(element => {
                        // Dados do Estabelecimento
                        var rowCliente = document.createElement("tr");

                        var cellLabelCliente = document.createElement("td");
                        var labelCliente = document.createElement("strong");
                        labelCliente.textContent = "Estabelecimento: ";
                        cellLabelCliente.append(labelCliente);

                        var cellInfoCliente = document.createElement("td");
                        var infoCliente = document.createElement("strong");
                        infoCliente.textContent = element.cliente.nome_fantasia + " / " + element.cliente.razao_social;
                        cellInfoCliente.colSpan = 2;
                        cellInfoCliente.append(infoCliente);

                        rowCliente.append(cellLabelCliente);
                        rowCliente.append(cellInfoCliente);

                        // Cabeçalho de periodo

                        var rowHeaderPeriodo = document.createElement("tr");
                        var cellLabelPeriodo = document.createElement("td");
                        var labelPeriodo = document.createElement("strong");
                        labelPeriodo.textContent = "Período";
                        cellLabelPeriodo.append(labelPeriodo);


                        var cellLabelEntrada = document.createElement("td");
                        var labelEntrada = document.createElement("strong");
                        labelEntrada.textContent = "Entrada";
                        cellLabelEntrada.append(labelEntrada);

                        var cellLabelSaida = document.createElement("td");
                        var labelSaida = document.createElement("strong");
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

                        for (let index = 0; index < length; index++) {
                            var item = {
                                periodo: moment(pontuacoesEntradas[index].periodo, "YYYY-MM").format("MM/YYYY"),
                                gotasEntradas:
                                    pontuacoesEntradas[index].qte_gotas,
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

                    // Linha de soma total

                    var rowTotal = document.createElement("tr");
                    var cellLabelTotal = document.createElement("td");
                    var labelTotal = document.createElement("strong");

                    labelTotal.classList.add("text-bold");
                    labelTotal.textContent = "Total";
                    cellLabelTotal.append(labelTotal);

                    var textTotalEntradas = document.createElement("strong");
                    textTotalEntradas.textContent = data.total_entradas;
                    var cellTotalEntradas = document.createElement("td");
                    cellTotalEntradas.classList.add("text-right");
                    cellTotalEntradas.append(textTotalEntradas);

                    var textTotalSaidas = document.createElement("strong");
                    textTotalSaidas.textContent = data.total_saidas;
                    var cellTotalSaidas = document.createElement("td");
                    cellTotalSaidas.classList.add("text-right");
                    cellTotalSaidas.append(textTotalSaidas);

                    rowTotal.append(cellLabelTotal);
                    rowTotal.append(cellTotalEntradas);
                    rowTotal.append(cellTotalSaidas);

                    rows.push(rowTotal);
                }
                conteudoTabela.append(rows);
            },
            error: function(response) {
                closeLoaderAnimation();
                var data = response.responseJSON;
                callModalError(data.mensagem.message, data.mensagem.errors);
            },
            complete: function(response) {
                closeLoaderAnimation();
            }
        });
    }

    // #endregion

    // #region Bindings

    funcionariosSelectListBox.on("change", brindesSelectListBoxOnChange);
    clientesSelectListBox.on("change", clientesSelectListBoxOnChange);
    dataInicio.on("change", dataInicioOnChange);
    dataFim.on("change", dataFimOnChange);
    tipoRelatorio.on("change", tipoRelatorioOnChange);

    $(pesquisarBtn).on("click", function() {
        getDataPontuacoesEntradaSaida(form.clientesId, form.brindesId, form.dataInicio, form.dataFim, form.tipoRelatorio);
    });

    imprimirBtn.on("click", imprimirRelatorio);

    // #endregion

    // #endregion

    // "Constroi" a tela
    init();
});
