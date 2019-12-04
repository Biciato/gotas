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

            var item = {
                id: 0,
                nome: "Selecione um Estabelecimento para continuar..."
            };

            brindesList.push(item);
            brindesSelectListBox.empty();
            brindesSelectListBox.append(option);
            brindesSelectListBox.val(0);

            // Inicializa campos date
            dataInicio.datepicker().datepicker("setDate", dataAtual);
            dataFim.datepicker().datepicker("setDate", dataAtual);

            var option1 = document.createElement("option");
            option1.value = 0;
            option1.textContent = "Selecione um Estabelecimento para continuar...";
            option1.title = "Selecione um Estabelecimento para continuar...";
            gotasSelectListBox.empty();
            gotasSelectListBox.append(option1);
            gotasList.push(item);

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
                brindesList = [];
                var option = document.createElement("option");
                option.value = 0;
                option.textContent = "Selecione um Estabelecimento para continuar...";
                option.title = "Selecione um Estabelecimento para continuar...";

                var item = {
                    id: 0,
                    nome: "Selecione um Estabelecimento para continuar..."
                };

                brindesList.push(item);
                brindesSelectListBox.empty();
                brindesSelectListBox.append(option);
                brindesSelectListBox.val(0);

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
            setTimeout($(".print-region").printThis({
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
                var option = document.createElement("option");
                option.value = 0;
                option.textContent = "Selecione um Estabelecimento para continuar...";
                option.title = "Selecione um Estabelecimento para continuar...";

                brindesList.push(option);
                brindesSelectListBox.empty();
                brindesSelectListBox.append(brindesList);
                brindesSelectListBox.append(option);
                brindesSelectListBox.val(0);

                gotasList = [];
                var option1 = document.createElement("option");
                option1.value = 0;
                option1.textContent = "Selecione um Estabelecimento para continuar...";
                option1.title = "Selecione um Estabelecimento para continuar...";
                gotasSelectListBox.empty();
                gotasSelectListBox.append(option1);
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
                form.brindesId = 0;
                brindesSelectedItem = brindesList.find(x => x.id == 0);
                brindesSelectListBox.val(brindesSelectedItem.id);
            } else {
                brindesSelectListBox.prop("disabled", false);
                gotasSelectListBox.prop("disabled", true);
                form.gotasId = 0;
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
         * @param {int} gotasId id da Gota (Referência)
         * @param {int} brindesId id do Brinde
         * @param {datetime} dataInicio Data Inicio
         * @param {datetime} dataFim DataFim
         * @param {string} tipoRelatorio Analítico / Sintético
         *
         * @returns HtmlTable
         */
        function getPontuacoesRelatorioEntradaSaida(clientesId, gotasId, brindesId, dataInicio, dataFim, tipoRelatorio, tipoMovimentacao) {
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
                gotas_id: gotasId,
                brindes_id: brindesId,
                data_inicio: dataInicioEnvio,
                data_fim: dataFimEnvio,
                tipo_relatorio: tipoRelatorio,
                tipo_movimentacao: tipoMovimentacao
            };

            console.log(data);

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

                                    var datas = Object.keys(element.pontuacoes_entradas[periodo].data);

                                    // Percorre os periodos

                                    datas.forEach(data => {
                                        // Titulo da atual data
                                        var rowPeriodo = document.createElement("tr");

                                        var cellPeriodoLabel = document.createElement("td");
                                        var labelPeriodo = document.createElement("strong");
                                        labelPeriodo.textContent = "Data";
                                        cellPeriodoLabel.append(labelPeriodo);

                                        var cellPeriodoTextoLabel = document.createElement("td");
                                        var labelPeriodoValue = document.createElement("strong");

                                        labelPeriodoValue.textContent = moment(data, "YYYY-MM-DD").format("DD/MM/YYYY");
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

                                        element.pontuacoes_entradas[periodo].data[data].data.forEach(pontuacao => {
                                            dataAtual = moment(pontuacao.periodo, "YYYY-MM-DD").format("DD/MM/YYYY");

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

                                        // Total Dia

                                        var rowTotalDia = document.createElement("tr");
                                        var cellLabelTotal = document.createElement("td");
                                        var labelTotal = document.createElement("strong");

                                        labelTotal.textContent = "Soma Dia: " + moment(data, "YYYY-MM-DD").format("DD/MM/YYYY");
                                        cellLabelTotal.append(labelTotal);

                                        var cellLabelEntradaTotal = document.createElement("td");
                                        var labelEntradaTotal = document.createElement("strong");
                                        labelEntradaTotal.textContent = element.pontuacoes_entradas[periodo].data[data].soma_dia;
                                        cellLabelEntradaTotal.classList.add("text-right");
                                        cellLabelEntradaTotal.colSpan = 2;
                                        cellLabelEntradaTotal.append(labelEntradaTotal);

                                        rowTotalDia.append(cellLabelTotal);
                                        rowTotalDia.append(cellLabelEntradaTotal);

                                        rowsPeriodos.push(rowTotalDia);
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
                                cellInfoCliente.colSpan = 5;
                                cellInfoCliente.classList.add("text-right");

                                cellInfoCliente.append(infoCliente);

                                rowCliente.append(cellLabelCliente);
                                rowCliente.append(cellInfoCliente);

                                rows.push(rowCliente);

                                // Fim dados Estabelecimento

                                // Linhas periodos

                                var rowsPeriodos = [];

                                var indexPeriodos = Object.keys(element.pontuacoes_saidas);
                                var pontuacoesSaidas = element.pontuacoes_saidas;

                                // Percorre as pontuacoes de saida
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
                                    cellPeriodoTextoLabel.colSpan = 5;
                                    cellPeriodoTextoLabel.classList.add("text-right");

                                    rowPeriodo.append(cellPeriodoLabel);
                                    rowPeriodo.append(cellPeriodoTextoLabel);

                                    rowsPeriodos.push(rowPeriodo);

                                    var dataAtual = null;
                                    var dataAnterior = null;

                                    var datas = Object.keys(element.pontuacoes_saidas[periodo].data);

                                    // Percorre os periodos

                                    datas.forEach(data => {
                                        // Titulo da atual data
                                        var rowPeriodo = document.createElement("tr");

                                        var cellPeriodoLabel = document.createElement("td");
                                        var labelPeriodo = document.createElement("strong");
                                        labelPeriodo.textContent = "Data";
                                        cellPeriodoLabel.append(labelPeriodo);

                                        var cellPeriodoTextoLabel = document.createElement("td");
                                        var labelPeriodoValue = document.createElement("strong");

                                        labelPeriodoValue.textContent = moment(data, "YYYY-MM-DD").format("DD/MM/YYYY");
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
                                        textSaidaQte.textContent = "Qte.:";
                                        cellLabelQteSaida.append(textSaidaQte);

                                        headerDadosPeriodoRow.append(cellLabelGota);
                                        headerDadosPeriodoRow.append(cellLabelFuncionarioSaida);
                                        headerDadosPeriodoRow.append(cellLabelUsuarioSaida);
                                        headerDadosPeriodoRow.append(cellLabelGotasSaida);
                                        headerDadosPeriodoRow.append(cellLabelReaisSaida);
                                        headerDadosPeriodoRow.append(cellLabelQteSaida);

                                        rowsPeriodos.push(headerDadosPeriodoRow);

                                        element.pontuacoes_saidas[periodo].data[data].data.forEach(pontuacao => {
                                            dataAtual = moment(pontuacao.periodo, "YYYY-MM-DD").format("DD/MM/YYYY");

                                            // Percorre as pontuações
                                            // Info de saida
                                            var row = document.createElement("tr");

                                            var cellSaidaGota = document.createElement("td");
                                            var labelSaidaGota = document.createElement("span");
                                            labelSaidaGota.textContent = pontuacao.brinde;
                                            cellSaidaGota.append(labelSaidaGota);

                                            var cellSaidaFuncionario = document.createElement("td");
                                            var labelSaidaFuncionario = document.createElement("span");
                                            labelSaidaFuncionario.textContent = pontuacao.funcionario;
                                            cellSaidaFuncionario.append(labelSaidaFuncionario);

                                            var cellSaidaUsuario = document.createElement("td");
                                            var labelSaidaUsuario = document.createElement("span");
                                            labelSaidaUsuario.textContent = pontuacao.usuario;
                                            cellSaidaUsuario.append(labelSaidaUsuario);

                                            var cellSaidaGotas = document.createElement("td");
                                            var labelSaidaGotas = document.createElement("span");
                                            labelSaidaGotas.textContent = pontuacao.qte_gotas;
                                            cellSaidaGotas.classList.add("text-right");
                                            cellSaidaGotas.append(labelSaidaGotas);

                                            var cellSaidaReais = document.createElement("td");
                                            var labelSaidaReais = document.createElement("span");
                                            labelSaidaReais.textContent = "R$ " + parseFloat(pontuacao.qte_reais).toFixed(2);
                                            cellSaidaReais.classList.add("text-right");
                                            cellSaidaReais.append(labelSaidaReais);

                                            var cellSaidaQte = document.createElement("td");
                                            var labelSaidaQte = document.createElement("span");
                                            labelSaidaQte.textContent = pontuacao.qte;
                                            cellSaidaQte.classList.add("text-right");
                                            cellSaidaQte.append(labelSaidaQte);

                                            row.append(cellSaidaGota);
                                            row.append(cellSaidaFuncionario);
                                            row.append(cellSaidaUsuario);
                                            row.append(cellSaidaGotas);
                                            row.append(cellSaidaReais);
                                            row.append(cellSaidaQte);

                                            rowsPeriodos.push(row);

                                        });

                                        // Total Dia

                                        var rowTotalDia = document.createElement("tr");
                                        var cellLabelTotal = document.createElement("td");
                                        var labelTotal = document.createElement("strong");

                                        labelTotal.textContent = "Soma Dia: " + moment(data, "YYYY-MM-DD").format("DD/MM/YYYY");
                                        cellLabelTotal.append(labelTotal);

                                        var cellLabelSaidaDiaGotas = document.createElement("td");
                                        var labelSaidaGotasDia = document.createElement("strong");
                                        labelSaidaGotasDia.textContent = element.pontuacoes_saidas[periodo].data[data].soma_dia_gotas;
                                        cellLabelSaidaDiaGotas.classList.add("text-right");
                                        cellLabelSaidaDiaGotas.colSpan = 1;
                                        cellLabelSaidaDiaGotas.append(labelSaidaGotasDia);

                                        var cellLabelSaidaDiaReais = document.createElement("td");
                                        var labelSaidaReaisDia = document.createElement("strong");
                                        labelSaidaReaisDia.textContent = "R$ " + parseFloat(element.pontuacoes_saidas[periodo].data[data].soma_dia_reais).toFixed(2);
                                        cellLabelSaidaDiaReais.classList.add("text-right");
                                        cellLabelSaidaDiaReais.colSpan = 1;
                                        cellLabelSaidaDiaReais.append(labelSaidaReaisDia);

                                        var cellLabelSaidaDiaQte = document.createElement("td");
                                        var labelSaidaQteDia = document.createElement("strong");
                                        labelSaidaQteDia.textContent = element.pontuacoes_saidas[periodo].data[data].soma_dia_qte;
                                        cellLabelSaidaDiaQte.classList.add("text-right");
                                        cellLabelSaidaDiaQte.colSpan = 1;
                                        cellLabelSaidaDiaQte.append(labelSaidaQteDia);

                                        rowTotalDia.append(cellLabelTotal);
                                        rowTotalDia.append(document.createElement("td"));
                                        rowTotalDia.append(document.createElement("td"));
                                        rowTotalDia.append(cellLabelSaidaDiaGotas);
                                        rowTotalDia.append(cellLabelSaidaDiaReais);
                                        rowTotalDia.append(cellLabelSaidaDiaQte);

                                        rowsPeriodos.push(rowTotalDia);
                                    });


                                    // Emite subtotal de período

                                    // Total periodo

                                    var rowTotalPeriodo = document.createElement("tr");
                                    var cellLabelTotal = document.createElement("td");
                                    var labelTotal = document.createElement("strong");

                                    labelTotal.textContent = "Soma Período: " + mesAtual;
                                    cellLabelTotal.append(labelTotal);

                                    var cellLabelSaidaTotalGotas = document.createElement("td");
                                    var labelSaidaTotalGotas = document.createElement("strong");
                                    labelSaidaTotalGotas.textContent = element.pontuacoes_saidas[periodo].soma_periodo_gotas;
                                    cellLabelSaidaTotalGotas.classList.add("text-right");
                                    cellLabelSaidaTotalGotas.colSpan = 1;
                                    cellLabelSaidaTotalGotas.append(labelSaidaTotalGotas);

                                    var cellLabelSaidaTotalReais = document.createElement("td");
                                    var labelSaidaTotalReais = document.createElement("strong");
                                    labelSaidaTotalReais.textContent = "R$ " + parseFloat(element.pontuacoes_saidas[periodo].soma_periodo_reais).toFixed(2);
                                    cellLabelSaidaTotalReais.classList.add("text-right");
                                    cellLabelSaidaTotalReais.colSpan = 1;
                                    cellLabelSaidaTotalReais.append(labelSaidaTotalReais);

                                    var cellLabelSaidaTotalQte = document.createElement("td");
                                    var labelSaidaTotalQte = document.createElement("strong");
                                    labelSaidaTotalQte.textContent = element.pontuacoes_saidas[periodo].soma_periodo_qte;
                                    cellLabelSaidaTotalQte.classList.add("text-right");
                                    cellLabelSaidaTotalQte.colSpan = 1;
                                    cellLabelSaidaTotalQte.append(labelSaidaTotalQte);

                                    rowTotalPeriodo.append(cellLabelTotal);
                                    rowTotalPeriodo.append(document.createElement("td"));
                                    rowTotalPeriodo.append(document.createElement("td"));
                                    rowTotalPeriodo.append(cellLabelSaidaTotalGotas);
                                    rowTotalPeriodo.append(cellLabelSaidaTotalReais);
                                    rowTotalPeriodo.append(cellLabelSaidaTotalQte);

                                    rowsPeriodos.push(rowTotalPeriodo);
                                });

                                if (element.pontuacoes_saidas.length == 0) {
                                    // Se não teve registro, adiciona uma linha informando que não teve movimentação

                                    var rowEmpty = document.createElement("tr");
                                    var cell = document.createElement("td");
                                    var label = document.createElement("strong");

                                    label.textContent = "Não há registros à serem exibidos!";
                                    cell.append(label);
                                    cell.colSpan = 6;
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

                                var textTotalSaidasGotas = document.createElement("strong");
                                var cellTotalSaidasGotas = document.createElement("td");
                                textTotalSaidasGotas.textContent = data.total_saidas_gotas;
                                cellTotalSaidasGotas.classList.add("text-right");
                                cellTotalSaidasGotas.colSpan = 1;
                                cellTotalSaidasGotas.append(textTotalSaidasGotas);

                                var textTotalSaidasReais = document.createElement("strong");
                                var cellTotalSaidasReais = document.createElement("td");
                                textTotalSaidasReais.textContent = "R$ " + parseFloat(data.total_saidas_reais).toFixed(2);
                                cellTotalSaidasReais.classList.add("text-right");
                                cellTotalSaidasReais.colSpan = 1;
                                cellTotalSaidasReais.append(textTotalSaidasReais);

                                var textTotalSaidasQte = document.createElement("strong");
                                var cellTotalSaidasQte = document.createElement("td");
                                textTotalSaidasQte.textContent = data.total_saidas_qte;
                                cellTotalSaidasQte.classList.add("text-right");
                                cellTotalSaidasQte.colSpan = 1;
                                cellTotalSaidasQte.append(textTotalSaidasQte);

                                rowTotal.append(cellLabelTotal);
                                rowTotal.append(document.createElement("td"));
                                rowTotal.append(document.createElement("td"));
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
                            cellInfoCliente.colSpan = tipoMovimentacaoSelectedItem === "Entrada" ? 2 : 3;
                            cellInfoCliente.append(infoCliente);

                            rowCliente.append(cellLabelCliente);
                            rowCliente.append(cellInfoCliente);

                            // Cabeçalho de periodo

                            var rowHeaderPeriodo = document.createElement("tr");
                            var cellLabelPeriodo = document.createElement("td");
                            var labelPeriodo = document.createElement("strong");
                            labelPeriodo.textContent = "Período";
                            cellLabelPeriodo.append(labelPeriodo);

                            rowHeaderPeriodo.append(cellLabelPeriodo);

                            if (tipoMovimentacaoSelectedItem === "Entrada") {
                                var cellLabelEntradaGotas = document.createElement("td");
                                var labelEntradaGotas = document.createElement("strong");
                                labelEntradaGotas.textContent = "Gotas";
                                cellLabelEntradaGotas.append(labelEntradaGotas);

                                rowHeaderPeriodo.append(cellLabelEntradaGotas);
                            } else {
                                var cellLabelSaidaGotas = document.createElement("td");
                                var labelSaidaGotas = document.createElement("strong");
                                labelSaidaGotas.textContent = "Gotas";
                                cellLabelSaidaGotas.append(labelSaidaGotas);

                                var cellLabelSaidaReais = document.createElement("td");
                                var labelSaidaReais = document.createElement("strong");
                                labelSaidaReais.textContent = "Saida";
                                cellLabelSaidaReais.append(labelSaidaReais);

                                var cellLabelSaidaQte = document.createElement("td");
                                var labelSaidaQte = document.createElement("strong");
                                labelSaidaQte.textContent = "Qte";
                                cellLabelSaidaQte.append(labelSaidaQte);

                                rowHeaderPeriodo.append(cellLabelSaidaGotas);
                                rowHeaderPeriodo.append(cellLabelSaidaReais);
                                rowHeaderPeriodo.append(cellLabelSaidaQte);
                            }


                            if (tipoMovimentacaoSelectedItem == "Entrada") {
                                // Periodos e valores
                                var rowsDadosPeriodos = [];

                                element.pontuacoes_entradas.forEach(entrada => {
                                    var item = {
                                        periodo: moment(entrada.periodo, "YYYY-MM").format("MM/YYYY"),
                                        saidasGotas: entrada.qte_gotas,
                                    };

                                    var rowPeriodo = document.createElement("tr");

                                    var labelItemPeriodo = document.createElement("span");
                                    labelItemPeriodo.textContent = item.periodo;

                                    var cellItemLabelPeriodo = document.createElement("td");
                                    cellItemLabelPeriodo.append(labelItemPeriodo);
                                    cellItemLabelPeriodo.classList.add("text-right");

                                    var textSaidasGotas = document.createElement("span");
                                    textSaidasGotas.textContent = item.saidasGotas;

                                    var cellItemSaidaGotas = document.createElement("td");
                                    cellItemSaidaGotas.append(textSaidasGotas);
                                    cellItemSaidaGotas.classList.add("text-right");

                                    rowPeriodo.append(cellItemLabelPeriodo);
                                    rowPeriodo.append(cellItemSaidaGotas);

                                    rowsDadosPeriodos.push(rowPeriodo);
                                });
                            } else {
                                // Periodos e valores
                                var rowsDadosPeriodos = [];

                                element.pontuacoes_saidas.forEach(saida => {
                                    var item = {
                                        periodo: moment(saida.periodo, "YYYY-MM").format("MM/YYYY"),
                                        saidasGotas: saida.qte_gotas,
                                        saidasReais: saida.qte_reais,
                                        saidasQte: saida.qte,
                                    };

                                    var rowPeriodo = document.createElement("tr");

                                    var labelItemPeriodo = document.createElement("span");
                                    labelItemPeriodo.textContent = item.periodo;

                                    var cellItemLabelPeriodo = document.createElement("td");
                                    cellItemLabelPeriodo.append(labelItemPeriodo);
                                    cellItemLabelPeriodo.classList.add("text-right");

                                    var textSaidasGotas = document.createElement("span");
                                    textSaidasGotas.textContent = item.saidasGotas;

                                    var cellItemSaidaGotas = document.createElement("td");
                                    cellItemSaidaGotas.append(textSaidasGotas);
                                    cellItemSaidaGotas.classList.add("text-right");

                                    var textSaidaReais = document.createElement("span");
                                    textSaidaReais.textContent = "R$ " + parseFloat(item.saidasReais).toFixed(2);

                                    var cellItemSaidaReais = document.createElement("td");
                                    cellItemSaidaReais.append(textSaidaReais);
                                    cellItemSaidaReais.classList.add("text-right");

                                    var textSaidaQte = document.createElement("span");
                                    textSaidaQte.textContent = item.saidasQte;

                                    var cellItemSaidaQte = document.createElement("td");
                                    cellItemSaidaQte.append(textSaidaQte);
                                    cellItemSaidaQte.classList.add("text-right");

                                    rowPeriodo.append(cellItemLabelPeriodo);
                                    rowPeriodo.append(cellItemSaidaGotas);
                                    rowPeriodo.append(cellItemSaidaReais);
                                    rowPeriodo.append(cellItemSaidaQte);

                                    rowsDadosPeriodos.push(rowPeriodo);
                                });


                                // Linha de soma

                                var rowSomaPeriodo = document.createElement("tr");

                                var labelSomaPeriodo = document.createElement("span");
                                labelSomaPeriodo.textContent = "Soma Estabelecimento";

                                var cellLabelSomaPeriodo = document.createElement("td");
                                cellLabelSomaPeriodo.append(labelSomaPeriodo);

                                rowSomaPeriodo.append(cellLabelSomaPeriodo);

                                if (tipoMovimentacaoSelectedItem == "Entrada") {
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

                                    rowSomaPeriodo.append(cellTextSomaPeriodoEntrada);
                                    rowSomaPeriodo.append(cellTextSomaPeriodoSaida);
                                } else {
                                    var textSomaPeriodoSaidaGotas = document.createElement("span");
                                    textSomaPeriodoSaidaGotas.textContent = element.soma_saida_gotas;

                                    var cellTextSomaPeriodoSaidaGotas = document.createElement("td");
                                    cellTextSomaPeriodoSaidaGotas.append(textSomaPeriodoSaidaGotas);
                                    cellTextSomaPeriodoSaidaGotas.classList.add("text-right");

                                    var textSomaPeriodoSaidaReais = document.createElement("span");
                                    textSomaPeriodoSaidaReais.textContent = "R$ " + parseFloat(element.soma_saida_reais).toFixed(2);

                                    var cellTextSomaPeriodoSaidaReais = document.createElement("td");
                                    cellTextSomaPeriodoSaidaReais.append(textSomaPeriodoSaidaReais);
                                    cellTextSomaPeriodoSaidaReais.classList.add("text-right");

                                    var textSomaPeriodoSaidaQte = document.createElement("span");
                                    textSomaPeriodoSaidaQte.textContent = element.soma_saida_qte;

                                    var cellTextSomaPeriodoSaidaQte = document.createElement("td");
                                    cellTextSomaPeriodoSaidaQte.append(textSomaPeriodoSaidaQte);
                                    cellTextSomaPeriodoSaidaQte.classList.add("text-right");

                                    rowSomaPeriodo.append(cellTextSomaPeriodoSaidaGotas);
                                    rowSomaPeriodo.append(cellTextSomaPeriodoSaidaReais);
                                    rowSomaPeriodo.append(cellTextSomaPeriodoSaidaQte);
                                }

                            }

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

                        rowTotal.append(cellLabelTotal);

                        if (tipoMovimentacaoSelectedItem === "Entrada") {
                            var textTotalEntradas = document.createElement("strong");
                            textTotalEntradas.textContent = data.total_entradas;
                            var cellTotalEntradas = document.createElement("td");
                            cellTotalEntradas.classList.add("text-right");
                            cellTotalEntradas.append(textTotalEntradas);

                            rowTotal.append(cellTotalEntradas);
                        } else {
                            var textTotalSaidasGotas = document.createElement("strong");
                            textTotalSaidasGotas.textContent = data.total_saidas_gotas;
                            var cellTotalSaidasGotas = document.createElement("td");
                            cellTotalSaidasGotas.classList.add("text-right");
                            cellTotalSaidasGotas.append(textTotalSaidasGotas);

                            var textTotalSaidasReais = document.createElement("strong");
                            textTotalSaidasReais.textContent = "R$ " + parseFloat(data.total_saidas_reais).toFixed(2);
                            var cellTotalSaidasReais = document.createElement("td");
                            cellTotalSaidasReais.classList.add("text-right");
                            cellTotalSaidasReais.append(textTotalSaidasReais);

                            var textTotalSaidasQte = document.createElement("strong");
                            textTotalSaidasQte.textContent = data.total_saidas_qte;
                            var cellTotalSaidasQte = document.createElement("td");
                            cellTotalSaidasQte.classList.add("text-right");
                            cellTotalSaidasQte.append(textTotalSaidasQte);

                            rowTotal.append(cellTotalSaidasGotas);
                            rowTotal.append(cellTotalSaidasReais);
                            rowTotal.append(cellTotalSaidasQte);
                        }

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

        gotasSelectListBox.on("change", gotasSelectListBoxOnChange);
        brindesSelectListBox.on("change", brindesSelectListBoxOnChange);
        clientesSelectListBox.on("change", clientesSelectListBoxOnChange);
        dataInicio.on("change", dataInicioOnChange);
        dataFim.on("change", dataFimOnChange);
        tipoRelatorio.on("change", tipoRelatorioOnChange);

        $(pesquisarBtn).on("click", function () {
            getPontuacoesRelatorioEntradaSaida(form.clientesId, form.gotasId, form.brindesId, form.dataInicio, form.dataFim, form.tipoRelatorio, tipoMovimentacaoSelectedItem);

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
    .ajaxError(closeLoaderAnimation);
