$(function () {
        'use strict';
        // #region Properties

        var form = {};
        var clientesSelectListBox = $("#clientes-list");
        var clientesSelectedItem = {};
        var clientesList = [];
        var brindesSelectListBox = $("#brindes-list");
        var brindesList = [];
        var brindesSelectedItem = {};
        var gotasList = [];
        var gotasSelectListBox = $("#gotas-list");
        var gotasSelectedItem = {};
        var redesList = [];
        var redesSelectListBox = $("#redes-list");
        var redesSelectedItem = {};

        var tabela = $("#tabela-dados");
        var conteudoTabela = $("#tabela-dados tbody");
        var tipoMovimentacao = $("#tipo-movimentacao");
        var tipoMovimentacaoSelectedItem = {};
        var tipoRelatorio = $("#tipo-relatorio");
        var pesquisarBtn = $("#btn-pesquisar");
        var imprimirBtn = $("#btn-imprimir");

        //#region Dados de Brinde Selecionado

        var tabelaResumoBrinde = $("#tabela-resumo-brinde");

        var brindeSelecionadoNome = $("#nome-brinde");
        var brindeSelecionadoQte = $("#quantidade-emitida");
        var brindeSelecionadoGotas = $("#total-gotas-brinde");
        var brindeSelecionadoReais = $("#total-reais-brinde");

        //#endregion

        //#region Dados de Resumo

        var totalGotasOntem = $("#total-gotas-ontem");
        var totalGotasResgatadas = $("#total-gotas-resgatadas");
        var gotasAdquiridasPeriodo = $("#gotas-adquiridas-periodo");
        var gotasExpiradasPeriodo = $("#gotas-expiradas-periodo");
        var caixaHojeGotas = $("#caixa-hoje-gotas");
        var caixaHojeReais = $("#caixa-hoje-reais");

        //#endregion

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
            option.value = 0;
            option.textContent = "Selecione um Estabelecimento para continuar...";
            option.title = "Selecione um Estabelecimento para continuar...";

            brindesList.push(option);
            brindesSelectListBox.empty();
            brindesSelectListBox.append(brindesList);

            // Inicializa campos date
            dataInicio.datepicker().datepicker("setDate", dataAtual);
            dataFim.datepicker().datepicker("setDate", dataAtual);

            var option1 = document.createElement("option");
            option1.value = 0;
            option1.textContent = "Selecione um Estabelecimento para continuar...";
            option1.title = "Selecione um Estabelecimento para continuar...";
            gotasSelectListBox.append(option1);

            // Dispara todos os eventos que precisam de inicializar
            // dataInicioOnChange();
            // dataFimOnChange();
            tipoRelatorioOnChange();
            getRedesList();

            redesSelectListBox.unbind("change");
            redesSelectListBox.on("change", redesSelectListBoxOnChange);

            tipoMovimentacao.unbind("change");
            tipoMovimentacao.on("change", tipoMovimentacaoOnChange);
            tipoMovimentacao.change();

            // Desabilita botão de imprimir até que usuário faça alguma consulta
            imprimirBtn.addClass("disabled");
            imprimirBtn.addClass("readonly");
            imprimirBtn.unbind("click");
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

            brinde = isNaN(brinde) ? 0 : brinde;
            form.brindesId = brinde;
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

            // Obtem Brindes
            getBrindesList(clientesId);
            getGotasList(clientesId);
        }

        /**
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::dataInicioOnChange
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
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::dataFimOnChange
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
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::imprimirRelatorio
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

                brindesList = [];
                brindesSelectListBox.empty();
                brindesSelectListBox.append(option);

                gotasList = [];
                gotasSelectListBox.empty();
                gotasSelectListBox.append(option);
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
        function tipoMovimentacaoOnChange() {
            tipoMovimentacaoSelectedItem = this.value;

            if (tipoMovimentacaoSelectedItem == "Entrada") {
                brindesSelectListBox.prop("disabled", true);
                gotasSelectListBox.prop("disabled", false);
                brindesSelectedItem = brindesList.find(x => x.id == 0);
                brindesSelectListBox.val(brindesSelectedItem.id);
            } else {
                brindesSelectListBox.prop("disabled", false);
                gotasSelectListBox.prop("disabled", true);
                gotasSelectedItem = gotasList.find(x => x.id == 0);
                gotasSelectListBox.val(gotasSelectedItem.id);
            }
        }

        /**
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::tipoRelatorioOnChange
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
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::getBrindesList
         *
         * Obtem lista de Brindes do posto(s) selecionado(s)
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-12
         *
         * @returns SelectListBox Lista de Seleção
         */
        function getBrindesList(clientesId) {
            console.log(clientesId);

            $.ajax({
                type: "GET",
                url: "/api/brindes/get_brindes_list",
                data: {
                    clientes_id: clientesId,
                    habilitado: 2
                },
                dataType: "JSON",
                success: function (response) {
                    brindesSelectListBox.empty();

                    if (response.data !== undefined && response.data.brindes.length > 0) {
                        brindesList = [];
                        var data = response.data.brindes;
                        var brinde = {
                            id: 0,
                            nome: "<Todos>"
                        };
                        brindesList.push(brinde);
                        brindesSelectListBox.title = "Selecionar Brinde para filtro específico";

                        data.forEach(dataItem => {
                            var item = {
                                id: dataItem.id,
                                nome: dataItem.nome_brinde_detalhado
                            };

                            brindesList.push(item);
                        });

                        brindesList.forEach(item => {
                            var option = document.createElement("option");
                            option.value = item.id;
                            option.textContent = item.nome;
                            brindesSelectListBox.append(option);
                        });
                    }
                },
                error: function (response) {
                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.error);
                },
                complete: function (response) {

                }
            });
        }

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
         * Obtem dados de pontuações
         *
         * Obtem os dados de relatório do servidor
         *
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::getPontuacoesRelatorioEntradaSaida
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
        function getPontuacoesRelatorioEntradaSaida(clientesId, brindesId, dataInicio, dataFim, tipoRelatorio, tipoMovimentacao) {
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
                tipo_relatorio: tipoRelatorio,
                tipo_movimentacao: tipoMovimentacao
            };


            $.ajax({
                type: "GET",
                url: "/api/pontuacoes/get_pontuacoes_relatorio_entrada_saida",
                data: data,
                dataType: "JSON",
                success: function (response) {
                    imprimirBtn.removeClass("disabled");
                    imprimirBtn.removeClass("readonly");
                    imprimirBtn.unbind("click");
                    imprimirBtn.on("click", imprimirRelatorio);

                    var data = response.data.pontuacoes_report;

                    if (data.pontuacoes.length > 0) {
                        conteudoTabela.empty();

                        $(tabela).hide();
                        $(tabela).fadeIn(500);
                    }

                    var rows = [];

                    if (form.tipoRelatorio == "Analítico") {

                        if (tipoMovimentacaoSelectedItem == "Entrada") {

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
                                cellInfoCliente.colSpan = 2;
                                cellInfoCliente.classList.add("text-right");

                                cellInfoCliente.append(infoCliente);

                                rowCliente.append(cellLabelCliente);
                                rowCliente.append(cellInfoCliente);

                                rows.push(rowCliente);

                                // Fim dados Estabelecimento

                                // Linhas periodos

                                var rowsPeriodos = [];

                                var indexPeriodos = Object.keys(element.pontuacoes_entradas);
                                var pontuacoesEntradas = element.pontuacoes_entradas;

                                // Percorre as pontuacoes de entrada
                                indexPeriodos.forEach(periodo => {
                                    // Titulo de períodos
                                    var rowPeriodo = document.createElement("tr");

                                    var cellPeriodoLabel = document.createElement("td");
                                    var labelPeriodo = document.createElement("strong");
                                    labelPeriodo.textContent = "Periodo";
                                    cellPeriodoLabel.append(labelPeriodo);

                                    var cellPeriodoTextoLabel = document.createElement("td");
                                    var labelPeriodoValue = document.createElement("strong");

                                    var mesAtual = moment(periodo, "YYYY-MM-DD").format("MM/YYYY");
                                    labelPeriodoValue.textContent = mesAtual;
                                    cellPeriodoTextoLabel.append(labelPeriodoValue);
                                    cellPeriodoTextoLabel.colSpan = 2;
                                    cellPeriodoTextoLabel.classList.add("text-right");

                                    rowPeriodo.append(cellPeriodoLabel);
                                    rowPeriodo.append(cellPeriodoTextoLabel);

                                    rowsPeriodos.push(rowPeriodo);

                                    var dataAtual = null;
                                    var dataAnterior = null;

                                    // Percorre os periodos
                                    element.pontuacoes_entradas[periodo].data.forEach(pontuacao => {
                                        dataAtual = moment(pontuacao.periodo, "YYYY-MM-DD").format("DD/MM/YYYY");

                                        if (dataAtual !== dataAnterior) {
                                            dataAnterior = dataAtual;
                                            // Constrói cabeçalhos

                                            // Titulo da atual data
                                            var rowPeriodo = document.createElement("tr");

                                            var cellPeriodoLabel = document.createElement("td");
                                            var labelPeriodo = document.createElement("strong");
                                            labelPeriodo.textContent = "Data";
                                            cellPeriodoLabel.append(labelPeriodo);

                                            var cellPeriodoTextoLabel = document.createElement("td");
                                            var labelPeriodoValue = document.createElement("strong");

                                            labelPeriodoValue.textContent = dataAtual;
                                            cellPeriodoTextoLabel.append(labelPeriodoValue);
                                            cellPeriodoTextoLabel.colSpan = 2;
                                            cellPeriodoTextoLabel.classList.add("text-right");

                                            rowPeriodo.append(cellPeriodoLabel);
                                            rowPeriodo.append(cellPeriodoTextoLabel);

                                            rowsPeriodos.push(rowPeriodo);

                                            // linha que indica o cabeçalho das colunas quem compõem o conjunto dos períodos
                                            var headerDadosPeriodoRow = document.createElement("tr");

                                            var cellLabelGota = document.createElement("td");
                                            var textlabelGota = document.createElement("strong");
                                            textlabelGota.textContent = "Referência:";
                                            cellLabelGota.append(textlabelGota);

                                            var cellLabelUsuarioEntrada = document.createElement("td");
                                            var textUsuarioEntrada = document.createElement("strong");
                                            textUsuarioEntrada.textContent = "Usuário:";
                                            cellLabelUsuarioEntrada.append(textUsuarioEntrada);

                                            var cellLabelGotasEntrada = document.createElement("td");
                                            var textEntradaGotas = document.createElement("strong");
                                            textEntradaGotas.textContent = "Qte. Gotas:";
                                            cellLabelGotasEntrada.append(textEntradaGotas);

                                            headerDadosPeriodoRow.append(cellLabelGota);
                                            headerDadosPeriodoRow.append(cellLabelUsuarioEntrada);
                                            headerDadosPeriodoRow.append(cellLabelGotasEntrada);

                                            rowsPeriodos.push(headerDadosPeriodoRow);
                                        }


                                        // Percorre as pontuações
                                        // Info de entrada
                                        var row = document.createElement("tr");

                                        var cellEntradaGota = document.createElement("td");
                                        var labelEntradaGota = document.createElement("span");
                                        labelEntradaGota.textContent = pontuacao.gota !== undefined ? pontuacao.gota.nome_parametro : "";
                                        cellEntradaGota.append(labelEntradaGota);

                                        var cellEntradaUsuario = document.createElement("td");
                                        var labelEntradaUsuario = document.createElement("span");
                                        labelEntradaUsuario.textContent = pontuacao.usuario !== undefined ? pontuacao.usuario.nome : "";
                                        cellEntradaUsuario.append(labelEntradaUsuario);

                                        var cellEntradaQteGota = document.createElement("td");
                                        var labelEntradaQteGota = document.createElement("span");
                                        labelEntradaQteGota.textContent = pontuacao.qte_gotas;
                                        cellEntradaQteGota.classList.add("text-right");
                                        cellEntradaQteGota.append(labelEntradaQteGota);

                                        row.append(cellEntradaGota);
                                        row.append(cellEntradaUsuario);
                                        row.append(cellEntradaQteGota);

                                        rowsPeriodos.push(row);

                                    });

                                    // Emite subtotal de período

                                    // Total periodo

                                    var rowTotalPeriodo = document.createElement("tr");
                                    var cellLabelTotal = document.createElement("td");
                                    var labelTotal = document.createElement("strong");

                                    labelTotal.textContent = "Soma Período: " + mesAtual;
                                    cellLabelTotal.append(labelTotal);

                                    var cellLabelEntradaTotal = document.createElement("td");
                                    var labelEntradaTotal = document.createElement("strong");
                                    labelEntradaTotal.textContent = element.pontuacoes_entradas[periodo].soma_periodo;
                                    cellLabelEntradaTotal.classList.add("text-right");
                                    cellLabelEntradaTotal.colSpan = 2;
                                    cellLabelEntradaTotal.append(labelEntradaTotal);

                                    rowTotalPeriodo.append(cellLabelTotal);
                                    rowTotalPeriodo.append(cellLabelEntradaTotal);

                                    rowsPeriodos.push(rowTotalPeriodo);
                                });

                                if (element.pontuacoes_entradas.length == 0) {
                                    // Se não teve registro, adiciona uma linha informando que não teve movimentação

                                    var rowEmpty = document.createElement("tr");
                                    var cell = document.createElement("td");
                                    var label = document.createElement("strong");

                                    label.textContent = "Não há registros à serem exibidos!";
                                    cell.append(label);
                                    cell.colSpan = 3;
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

                                rowTotal.append(cellLabelTotal);
                                rowTotal.append(cellTotalEntradas);

                                rowsPeriodos.push(rowTotal);

                                rowsPeriodos.forEach(element => {
                                    rows.push(element);
                                });
                            });
                        } else {

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
                                var pontuacoesSaidas = element.pontuacoes_saidas;

                                for (var pontuacoesIndex = 0; pontuacoesIndex < pontuacoesLength; pontuacoesIndex++) {
                                    var pontuacoesSaidaPeriodoList = pontuacoesSaidas[pontuacoesIndex];

                                    var pontuacoesDataLength = pontuacoesSaidaPeriodoList.data.length;
                                    var pontuacoesSaidaDataList = pontuacoesSaidaPeriodoList.data;

                                    var mesAtual = '';
                                    var ultimaData = '';

                                    for (var indexData = 0; indexData < pontuacoesDataLength; indexData++) {
                                        var saida = pontuacoesSaidaDataList[indexData];
                                        var periodoAtual = moment(saida.periodo, "YYYY-MM-DD").format("DD/MM/YYYY");

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

                                            mesAtual = moment(saida.periodo, "YYYY-MM-DD").format("MM/YYYY");
                                            labelPeriodoValue.textContent = periodoAtual;
                                            cellPeriodoTextoLabel.append(labelPeriodoValue);
                                            cellPeriodoTextoLabel.colSpan = 5;
                                            cellPeriodoTextoLabel.classList.add("text-right");

                                            rowPeriodo.append(cellPeriodoLabel);
                                            rowPeriodo.append(cellPeriodoTextoLabel);

                                            rowsPeriodos.push(rowPeriodo);

                                            // linha que indica o cabeçalho das colunas quem compõem o conjunto dos períodos
                                            var headerDadosPeriodoRow = document.createElement("tr");

                                            var cellLabelGota = document.createElement("td");
                                            var textlabelGota = document.createElement("strong");
                                            textlabelGota.textContent = "Referência:";
                                            cellLabelGota.append(textlabelGota);

                                            var cellLabelFuncionarioSaida = document.createElement("td");
                                            var textFuncionarioSaida = document.createElement("strong");
                                            textFuncionarioSaida.textContent = "Funcionário:";
                                            cellLabelFuncionarioSaida.append(textFuncionarioSaida);

                                            var cellLabelUsuarioSaida = document.createElement("td");
                                            var textUsuarioSaida = document.createElement("strong");
                                            textUsuarioSaida.textContent = "Usuário:";
                                            cellLabelUsuarioSaida.append(textUsuarioSaida);

                                            var cellLabelGotasSaida = document.createElement("td");
                                            var textSaidaGotas = document.createElement("strong");
                                            textSaidaGotas.textContent = "Gotas:";
                                            cellLabelGotasSaida.append(textSaidaGotas);

                                            var cellLabelReaisSaida = document.createElement("td");
                                            var textSaidaReais = document.createElement("strong");
                                            textSaidaReais.textContent = "Reais:";
                                            cellLabelReaisSaida.append(textSaidaReais);

                                            var cellLabelQteSaida = document.createElement("td");
                                            var textSaidaQte = document.createElement("strong");
                                            textSaidaQte.textContent = "Qtde:";
                                            cellLabelQteSaida.append(textSaidaQte);

                                            // headerDadosPeriodoRow.append(document.createElement("td"));
                                            headerDadosPeriodoRow.append(cellLabelGota);
                                            headerDadosPeriodoRow.append(cellLabelFuncionarioSaida);
                                            headerDadosPeriodoRow.append(cellLabelUsuarioSaida);
                                            headerDadosPeriodoRow.append(cellLabelGotasSaida);
                                            headerDadosPeriodoRow.append(cellLabelReaisSaida);
                                            headerDadosPeriodoRow.append(cellLabelQteSaida);

                                            rowsPeriodos.push(headerDadosPeriodoRow);
                                        }

                                        // Info de Saida

                                        console.log(saida);
                                        // Nome do Brinde
                                        var cellSaidaNomeBrinde = document.createElement("td");
                                        var labelSaidaNomeBrinde = document.createElement("span");
                                        labelSaidaNomeBrinde.textContent = saida !== undefined && saida.brinde !== undefined ? saida.brinde : "";
                                        cellSaidaNomeBrinde.append(labelSaidaNomeBrinde);

                                        // Funcionário
                                        var cellSaidaFuncionario = document.createElement("td");
                                        var labelSaidaFuncionario = document.createElement("span");
                                        labelSaidaFuncionario = saida !== undefined && saida.funcionario !== undefined ? saida.funcionario : "";
                                        cellSaidaFuncionario.append(labelSaidaFuncionario);

                                        // Cliente Final
                                        var cellSaidaUsuario = document.createElement("td");
                                        var labelSaidaUsuario = document.createElement("span");
                                        labelSaidaUsuario = saida !== undefined && saida.usuario !== undefined ? saida.usuario : "";
                                        cellSaidaUsuario.append(labelSaidaUsuario);

                                        // Gotas
                                        var cellSaidaGotas = document.createElement("td");
                                        var labelSaidaGotas = document.createElement("span");
                                        labelSaidaGotas.textContent = saida !== undefined ? saida.qte_gotas : 0;
                                        cellSaidaGotas.classList.add("text-right");
                                        cellSaidaGotas.append(labelSaidaGotas);

                                        // Reais
                                        var cellSaidaReais = document.createElement("td");
                                        var labelSaidaReais = document.createElement("span");
                                        labelSaidaReais.textContent = saida !== undefined ? saida.qte_reais : 0;
                                        cellSaidaReais.classList.add("text-right");
                                        cellSaidaReais.append(labelSaidaReais);

                                        // Qte
                                        var cellSaidaQte = document.createElement("td");
                                        var labelSaidaQte = document.createElement("span");
                                        labelSaidaQte.textContent = saida !== undefined ? saida.qte : 0;
                                        cellSaidaQte.classList.add("text-right");
                                        cellSaidaQte.append(labelSaidaQte);


                                        var row = document.createElement("tr");
                                        row.append(cellSaidaNomeBrinde);
                                        row.append(cellSaidaFuncionario);
                                        row.append(cellSaidaUsuario);
                                        row.append(cellSaidaGotas);
                                        row.append(cellSaidaReais);
                                        row.append(cellSaidaQte);

                                        rowsPeriodos.push(row);
                                    }

                                    // Total periodo

                                    var rowTotalPeriodo = document.createElement("tr");
                                    var cellLabelTotal = document.createElement("td");
                                    var labelTotal = document.createElement("strong");

                                    labelTotal.textContent = "Total Período: " + mesAtual;
                                    cellLabelTotal.append(labelTotal);

                                    var cellLabelGotasPeriodo = document.createElement("td");
                                    var labelGotasPeriodo = document.createElement("strong");
                                    labelGotasPeriodo.textContent = pontuacoesSaidaPeriodoList.soma_saidas;
                                    cellLabelGotasPeriodo.classList.add("text-right");
                                    cellLabelGotasPeriodo.colSpan = 3;
                                    cellLabelGotasPeriodo.append(labelGotasPeriodo);

                                    var cellLabelGotasPeriodo = document.createElement("td");
                                    var labelGotasPeriodo = document.createElement("strong");
                                    labelGotasPeriodo.textContent = pontuacoesSaidaPeriodoList.soma_saidas;
                                    cellLabelGotasPeriodo.classList.add("text-right");
                                    cellLabelGotasPeriodo.colSpan = 3;
                                    cellLabelGotasPeriodo.append(labelGotasPeriodo);

                                    rowTotalPeriodo.append(cellLabelTotal);
                                    rowTotalPeriodo.append(cellLabelGotasPeriodo);

                                    rowsPeriodos.push(rowTotalPeriodo);
                                }

                                if (pontuacoesLength == 0) {
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

                                // Total Gotas
                                var cellTotalSaidasGotas = document.createElement("td");
                                var textTotalSaidasGotas = document.createElement("strong");
                                textTotalSaidasGotas.textContent = data.total_saidas;
                                cellTotalSaidasGotas.classList.add("text-right");
                                cellTotalSaidasGotas.colSpan = 2;
                                cellTotalSaidasGotas.append(textTotalSaidasGotas);

                                // Total Reais
                                var cellTotalSaidasReais = document.createElement("td");
                                var textTotalSaidasReais = document.createElement("strong");
                                textTotalSaidasReais.textContent = data.total_reais;
                                cellTotalSaidasReais.classList.add("text-right");
                                cellTotalSaidasReais.colSpan = 1;
                                cellTotalSaidasReais.append(textTotalSaidasReais);

                                // Total Qte
                                var cellTotalSaidasQte = document.createElement("td");
                                var textTotalSaidasQte = document.createElement("strong");
                                textTotalSaidasQte.textContent = data.total_saidas;
                                cellTotalSaidasQte.classList.add("text-right");
                                cellTotalSaidasQte.colSpan = 1;
                                cellTotalSaidasQte.append(textTotalSaidasQte);

                                rowTotal.append(cellLabelTotal);
                                rowTotal.append(cellTotalEntradas);
                                rowTotal.append(cellTotalSaidasGotas);
                                rowTotal.append(cellTotalSaidasReais);
                                rowTotal.append(cellTotalSaidasQte);

                                rowsPeriodos.push(rowTotal);

                                rowsPeriodos.forEach(element => {
                                    rows.push(element);
                                });
                            });
                        }
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
                error: function (response) {

                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.errors);
                },
                complete: function (response) {
                    getResumoPontuacoesRelatorioEntradaSaida(redesSelectedItem.id, form.clientesId, dataInicio, dataFim);
                }
            });
        }

        function getResumoBrinde(brindesId, dataInicio, dataFim) {
            // Validação

            tabelaResumoBrinde.hide();

            console.log(brindesId);
            if (tipoMovimentacaoSelectedItem === "Saída" && (brindesId !== undefined && brindesId > 0)) {
                tabelaResumoBrinde.show();

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

                var dataSend = {
                    brindes_id: brindesId,
                    data_inicio: dataInicioEnvio,
                    data_fim: dataFimEnvio
                };

                $.ajax({
                    type: "GET",
                    url: "/api/cupons/get_resumo_brinde",
                    data: dataSend,
                    dataType: "JSON",
                    success: function (response) {

                        brindeSelecionadoQte.val(response.data.brinde.qte);
                        brindeSelecionadoGotas.val(response.data.brinde.soma_gotas);
                        brindeSelecionadoReais.val("R$ " + parseFloat(response.data.brinde.soma_reais).toFixed(2));
                        brindeSelecionadoNome.text(response.data.brinde.nome_brinde);
                    },
                    error: function (response) {

                    }
                });
            }


        }

        /**
         * Obtem Resumo de dados
         *
         * Obtem Resumo de dados
         *
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::getResumoPontuacoesRelatorioEntradaSaida
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-11-28
         *
         *
         * @param {int} redesId Redes Id
         * @param {int} clientesId Clientes Id
         * @param {datetime} dataInicio Data Inicio
         * @param {datetime} dataFim Data Fim
         */
        function getResumoPontuacoesRelatorioEntradaSaida(redesId, clientesId, dataInicio, dataFim) {
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

            var dataSend = {
                clientes_id: clientesId,
                data_inicio: dataInicioEnvio,
                data_fim: dataFimEnvio
            };

            $.ajax({
                type: "GET",
                url: "/api/pontuacoes/get_resumo_pontuacoes_estabelecimento",
                data: dataSend,
                dataType: "JSON",
                success: function (res) {
                    console.log(res);

                    totalGotasOntem.val(res.data.soma_ate_ontem);
                    totalGotasResgatadas.val(res.data.total_gotas_resgatadas);
                    gotasAdquiridasPeriodo.val(res.data.gotas_adquiridas_periodo);
                    gotasExpiradasPeriodo.val(res.data.total_gotas_expiradas_periodo);
                    caixaHojeGotas.val(res.data.caixa_hoje.soma_gotas);
                    caixaHojeReais.val("R$ " + parseFloat(res.data.caixa_hoje.soma_reais).toFixed(2));


                    // var data = res
                },
                error: function (res) {
                    console.log(res);
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

        brindesSelectListBox.on("change", brindesSelectListBoxOnChange);
        clientesSelectListBox.on("change", clientesSelectListBoxOnChange);
        dataInicio.on("change", dataInicioOnChange);
        dataFim.on("change", dataFimOnChange);
        tipoRelatorio.on("change", tipoRelatorioOnChange);

        $(pesquisarBtn).on("click", function () {
            getPontuacoesRelatorioEntradaSaida(form.clientesId, form.brindesId, form.dataInicio, form.dataFim, form.tipoRelatorio, tipoMovimentacaoSelectedItem);

            getResumoBrinde(form.brindesId, form.dataInicio, form.dataFim);
        });

        imprimirBtn.unbind("click");
        imprimirBtn.on("click", imprimirRelatorio);

        // #endregion

        // #endregion

        // "Constroi" a tela
        init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
// .ajaxError(closeLoaderAnimation)
;
