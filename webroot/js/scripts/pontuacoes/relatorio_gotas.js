/**
 * Arquivo de funções para src\Template\Pontuacoes\relatorio_gotas.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-09-17
 */

$(function () {
        'use strict';
        // #region Properties

        var form = {};
        var clientesSelectListBox = $("#clientes-list");
        var clientes = [];
        var clientesSelectedItem = {};
        var dataAtual = moment().format("DD/MM/YYYY");
        var funcionariosSelectListBox = $("#funcionarios-list");
        var funcionariosSelectedItem = {};
        var funcionarios = [];
        var gotasSelectListBox = $("#gotas-list");
        var gotas = [];
        var gotasSelectedItem = {};

        var conteudoTabela = $("#tabela-dados tbody");
        var imprimirBtn = $("#imprimir-btn");
        var pesquisarBtn = $("#pesquisar-btn");
        var reiniciarBtn = $("#reiniciar-btn");
        var tipoRelatorio = $("#tipo-relatorio");
        var tabela = $("#tabela-dados");

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
            conteudoTabela.empty();
            var option = document.createElement("option");
            option.value = undefined;
            option.textContent = "Selecione um Estabelecimento para continuar...";
            option.title = "Selecione um Estabelecimento para continuar...";

            // #region Bindings

            funcionariosSelectListBox.empty();
            funcionariosSelectListBox.append(option);
            funcionariosSelectListBox.unbind("change");
            funcionariosSelectListBox.on("change", funcionariosSelectListBoxOnChange);
            funcionariosSelectListBox.change();
            clientesSelectListBox.unbind("change");
            clientesSelectListBox.on("change", clientesSelectListBoxOnChange);
            clientesSelectListBox.change();
            dataInicio.unbind("change");
            dataInicio.on("change", dataInicioOnChange);
            dataFim.unbind("change");
            dataFim.on("change", dataFimOnChange);
            tipoRelatorio.unbind("change");
            tipoRelatorio.val("Sintético");
            tipoRelatorio.on("change", tipoRelatorioOnChange);

            pesquisarBtn.unbind("click");
            pesquisarBtn.on("click", function () {
                dataInicio.change();
                dataFim.change();
                getRelatorioMovimentacaoGotas(form.clientesId, form.gotasId, form.funcionariosId, form.dataInicio, form.dataFim, form.tipoRelatorio);
            });

            reiniciarBtn.unbind("click");
            reiniciarBtn.on("click", init);

            imprimirBtn.unbind("click");
            imprimirBtn.on("click", imprimirRelatorio);

            var option = document.createElement("option");
            option.value = null;
            option.textContent = "<Selecione um Estabelecimento para continuar>";
            gotasSelectListBox.append(option);
            gotasSelectListBox.on("change", gotasSelectListBoxOnChange);

            // #endregion

            // Inicializa campos date
            dataInicio.datepicker().datepicker("setDate", dataAtual);
            dataFim.datepicker().datepicker("setDate", dataAtual);

            // Dispara todos os eventos que precisam de inicializar
            tipoRelatorioOnChange();
            getClientesList();

            // Desabilita botão de imprimir até que usuário faça alguma consulta
            imprimirBtn.addClass("disabled");
            imprimirBtn.addClass("readonly");
            imprimirBtn.unbind("click");
        }

        /**
         * relatorio_gotas.js::funcionariosSelectListBoxOnChange()
         *
         * Comportamento ao trocar o brinde selecionado
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-11
         *
         * @return void
         */
        function funcionariosSelectListBoxOnChange() {
            var funcionario = parseInt(funcionariosSelectListBox.val());

            funcionario = isNaN(funcionario) ? undefined : funcionario;
            form.funcionariosId = funcionario;

            if (funcionario !== undefined) {
                funcionariosSelectedItem = funcionarios.find(x => x.id == funcionario);
            }
        }

        /**
         * Clientes On Change
         *
         * Comportamento ao trocar o cliente selecionado
         *
         * relatorio_gotas.js::clientesSelectListBoxOnChange()
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

            // Obtem dados pertencentes ao posto
            if (form.clientesId > 0) {
                funcionariosSelectListBox.prop("disabled", false);
                gotasSelectListBox.prop("disabled", false);
                getFuncionariosList(form.clientesId);
                getGotasCliente(form.clientesId);
            } else {
                // Limpa o select de funcionários e gotas

                funcionariosSelectListBox.prop("disabled", true);
                gotasSelectListBox.prop("disabled", true);

                funcionarios = [];
                funcionariosSelectedItem = {};
                funcionariosSelectListBox.empty();
                gotas = [];
                gotasSelectedItem = {};
                gotasSelectListBox.empty();

                var option = document.createElement("option");
                option.value = null;
                option.textContent = "Selecione um Estabelecimento para continuar...";

                var option2 = document.createElement("option");
                option2.value = null;
                option2.textContent = "Selecione um Estabelecimento para continuar...";

                funcionariosSelectListBox.append(option);
                gotasSelectListBox.append(option2);

                funcionariosSelectListBox.change();
                gotasSelectListBox.change();
            }
        }

        /**
         * Gotas On Change
         *
         * Comportamento ao alterar seleção de gotas
         *
         * relatorio_gotas.js::gotasSelectListBoxOnChange()
         *
         * @param {int} clientesId Id do cliente
         *
         * @return void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-11
         */
        function gotasSelectListBoxOnChange() {
            var gota = parseInt(this.value);

            gota = isNaN(gota) ? 0 : gota;

            if (gota > 0) {
                gotasSelectedItem = gotas.find(x => x.id === gota);
                form.gotasId = gotasSelectedItem.id;
            } else {
                form.gotasId = undefined;
                gotasSelectedItem = {};
            }
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
            $.ajax({
                type: "GET",
                url: "/api/clientes/get_clientes_list",
                data: {},
                dataType: "JSON",
                success: function (res) {
                    if (res.data.clientes.length > 0) {
                        clientes = [];
                        clientesSelectListBox.empty();

                        var cliente = {
                            id: undefined,
                            nomeFantasia: "Todos"
                        };

                        clientes.push(cliente);
                        clientesSelectListBox.prop("disabled", false);

                        res.data.clientes.forEach(cliente => {
                            var cliente = {
                                id: cliente.id,
                                nomeFantasia: cliente.nome_fantasia
                            };

                            clientes.push(cliente);
                        });

                        clientes.forEach(cliente => {
                            var option = document.createElement("option");
                            option.value = cliente.id;
                            option.textContent = cliente.nomeFantasia;

                            clientesSelectListBox.append(option);
                        });

                        // Se só tem 2 registros, significa que
                        if (clientes.length == 2) {
                            clientesSelectedItem = clientes[1];

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
                complete: function () {
                    clientesSelectListBox.change();
                }
            });
        }

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
            $.ajax({
                type: "GET",
                url: "/api/usuarios/get_funcionarios_list",
                data: {
                    clientes_id: clientesId
                },
                dataType: "JSON",
                success: function (response) {

                    if (response.data !== undefined) {
                        funcionariosSelectListBox.empty();
                        funcionarios = [];

                        var data = response.data.usuarios;
                        var collection = [];
                        var options = [];
                        var option = document.createElement("option");
                        option.title = "Selecionar Funcionário para filtro específico";
                        option.textContent = "Todos";
                        options.push(option);
                        funcionarios = [];

                        data.forEach(dataItem => {
                            var option = document.createElement("option");
                            var item = {
                                id: dataItem.id,
                                nome: dataItem.nome
                            };

                            option.value = item.id;
                            option.textContent = item.nome;
                            collection.push(item);
                            options.push(option);

                            funcionarios.push(item);
                        });

                        funcionariosSelectListBox.append(options);
                        funcionarios = collection;
                    }
                },
                error: function (response) {
                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.errors);
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
         * @param {int} gotasId id de Gotas
         * @param {int} funcionariosId Id de Funcionario
         * @param {datetime} dataInicio Data Inicio
         * @param {datetime} dataFim DataFim
         * @param {string} tipoRelatorio Analítico / Sintético
         *
         * @returns HtmlTable
         */
        function getRelatorioMovimentacaoGotas(clientesId, gotasId, funcionariosId, dataInicio, dataFim, tipoRelatorio) {
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
                funcionarios_id: funcionariosId,
                data_inicio: dataInicioEnvio,
                data_fim: dataFimEnvio,
                tipo_relatorio: tipoRelatorio
            };

            $.ajax({
                type: "GET",
                url: "/api/pontuacoes/get_relatorio_movimentacao_gotas",
                data: data,
                dataType: "JSON",
                success: function (response) {
                    imprimirBtn.removeClass("disabled");
                    imprimirBtn.removeClass("readonly");
                    imprimirBtn.unbind("click");
                    imprimirBtn.on("click", imprimirRelatorio);

                    var data = response.data;

                    if (data !== undefined) {
                        conteudoTabela.empty();

                        $(tabela).hide();
                        $(tabela).fadeIn(500);
                    }

                    var rows = [];

                    if (form.tipoRelatorio == "Analítico") {
                        if (data.total_gotas == 0 && data.total_litros == 0 && data.total_reais == 0) {
                            var rowEmpty = document.createElement("tr");
                            var cell = document.createElement("td");
                            var label = document.createElement("strong");

                            label.textContent = "Não há registros à serem exibidos!";
                            cell.append(label);
                            cell.colSpan = 7;
                            cell.classList.add("text-center");
                            rowEmpty.append(cell);
                            rows.push(rowEmpty);

                            imprimirBtn.addClass("disabled");
                            imprimirBtn.addClass("readonly");
                            imprimirBtn.unbind("click");

                        } else {
                            data.pontuacoes.forEach(element => {
                                // Linhas periodos
                                var periodos = element.periodos;

                                // O Javascript é uma desgraça. se eu retorno um array com índices declaradas, ele entende como um object
                                var periodosKeys = Object.keys(periodos);

                                // #region Dados do Estabelecimento
                                var rowCliente = document.createElement("tr");

                                var labelCliente = document.createElement("strong");
                                labelCliente.textContent = "Estabelecimento: ";
                                var cellLabelCliente = document.createElement("td");
                                cellLabelCliente.classList.add("font-weight-bold");
                                cellLabelCliente.append(labelCliente);

                                var cellInfoCliente = document.createElement("td");
                                var infoCliente = document.createElement("strong");
                                infoCliente.textContent = element.cliente.nome_fantasia + " / " + element.cliente.razao_social;
                                cellInfoCliente.colSpan = 4;
                                cellInfoCliente.classList.add("text-right");
                                cellInfoCliente.append(infoCliente);

                                rowCliente.append(cellLabelCliente);
                                rowCliente.append(cellInfoCliente);
                                rows.push(rowCliente);

                                //#endregion

                                if (periodosKeys.length == 0) {
                                    // Se não teve registro, adiciona uma linha informando que não teve movimentação

                                    var rowEmpty = document.createElement("tr");
                                    var cell = document.createElement("td");
                                    var label = document.createElement("strong");

                                    label.textContent = "Não há registros para o Estabelecimento!";
                                    cell.append(label);
                                    cell.colSpan = 5;
                                    cell.classList.add("text-center");
                                    rowEmpty.append(cell);
                                    rows.push(rowEmpty);
                                } else {
                                    periodosKeys.forEach(periodoKey => {
                                        var periodo = periodos[periodoKey];

                                        //#region Dados Período

                                        var mesAtual = '';
                                        var rowPeriodo = document.createElement("tr");

                                        var cellPeriodoLabel = document.createElement("td");
                                        var labelPeriodo = document.createElement("strong");
                                        labelPeriodo.textContent = "Mês Referência";
                                        cellPeriodoLabel.append(labelPeriodo);

                                        var cellPeriodoTextoLabel = document.createElement("td");
                                        var labelPeriodoValue = document.createElement("strong");

                                        mesAtual = moment(periodoKey, "YYYY/MM").format("MM/YYYY");
                                        labelPeriodoValue.textContent = mesAtual;
                                        cellPeriodoTextoLabel.append(labelPeriodoValue);
                                        cellPeriodoTextoLabel.colSpan = 6;
                                        cellPeriodoTextoLabel.classList.add("text-right");

                                        rowPeriodo.append(cellPeriodoLabel);
                                        rowPeriodo.append(cellPeriodoTextoLabel);

                                        rows.push(rowPeriodo);

                                        //#endregion

                                        if (periodo.pontuacoes.length == 0) {
                                            // Se não teve registro, adiciona uma linha informando que não teve movimentação

                                            var rowEmpty = document.createElement("tr");
                                            var cell = document.createElement("td");
                                            var label = document.createElement("strong");

                                            label.textContent = "Não há registros para o Estabelecimento!";
                                            cell.append(label);
                                            cell.colSpan = 5;
                                            cell.classList.add("text-center");
                                            rowEmpty.append(cell);
                                            rows.push(rowEmpty);
                                        } else {
                                            // Se tiver registro de pontuações, monta header

                                            var rowHeaderCompra = document.createElement("tr");

                                            var cellFuncionario = document.createElement("td");
                                            var textFuncionario = document.createElement("strong");
                                            textFuncionario.textContent = "Funcionário";
                                            cellFuncionario.append(textFuncionario);

                                            var cellGota = document.createElement("td");
                                            var textGota = document.createElement("strong");
                                            textGota.textContent = "Gota";
                                            cellGota.append(textGota);

                                            var cellQteGotas = document.createElement("td");
                                            var textQteGotas = document.createElement("strong");
                                            textQteGotas.textContent = "Qte. Gotas";
                                            cellQteGotas.append(textQteGotas);

                                            var cellQteLitros = document.createElement("td");
                                            var textQteLitros = document.createElement("strong");
                                            textQteLitros.textContent = "Qte. Litros";
                                            cellQteLitros.append(textQteLitros);

                                            var cellQteReais = document.createElement("td");
                                            var textQteReais = document.createElement("strong");
                                            textQteReais.textContent = "R$";
                                            cellQteReais.append(textQteReais);

                                            rowHeaderCompra.append(cellFuncionario);
                                            rowHeaderCompra.append(cellGota);
                                            rowHeaderCompra.append(cellQteGotas);
                                            rowHeaderCompra.append(cellQteLitros);
                                            rowHeaderCompra.append(cellQteReais);

                                            rows.push(rowHeaderCompra);
                                        }

                                        periodo.pontuacoes.forEach(pontuacao => {
                                            // Descrição das Gotas

                                            var rowCompra = document.createElement("tr");

                                            var cellFuncionario = document.createElement("td");
                                            var textFuncionario = document.createElement("span");
                                            textFuncionario.textContent = pontuacao.funcionario.nome;
                                            cellFuncionario.append(textFuncionario);

                                            var cellGota = document.createElement("td");
                                            var textGota = document.createElement("span");
                                            textGota.textContent = pontuacao.gota.nome_parametro;
                                            cellGota.append(textGota);

                                            var cellQteGotas = document.createElement("td");
                                            var textQteGotas = document.createElement("span");
                                            textQteGotas.textContent = pontuacao.quantidade_gotas;
                                            cellQteGotas.classList.add("text-right");
                                            cellQteGotas.append(textQteGotas);

                                            var cellQteLitros = document.createElement("td");
                                            var textQteLitros = document.createElement("span");
                                            var qteLitros = parseFloat(pontuacao.quantidade_litros);
                                            textQteLitros.textContent = qteLitros.toFixed(2);
                                            cellQteLitros.classList.add("text-right");
                                            cellQteLitros.append(textQteLitros);

                                            var cellQteReais = document.createElement("td");
                                            var textQteReais = document.createElement("span");
                                            var quantidadeReais = parseFloat(pontuacao.quantidade_reais);

                                            if (isNaN(quantidadeReais)) {
                                                quantidadeReais = 0;
                                            }

                                            textQteReais.textContent = "R$ " + quantidadeReais.toFixed(2);
                                            cellQteReais.classList.add("text-right");
                                            cellQteReais.append(textQteReais);

                                            rowCompra.append(cellFuncionario);
                                            rowCompra.append(cellGota);
                                            rowCompra.append(cellQteGotas);
                                            rowCompra.append(cellQteLitros);
                                            rowCompra.append(cellQteReais);

                                            rows.push(rowCompra);
                                        });

                                        //#region Total Periodo

                                        var rowTotalPeriodo = document.createElement("tr");

                                        var cellSomaPeriodo = document.createElement("td");
                                        var textSomaPeriodo = document.createElement("strong");
                                        textSomaPeriodo.textContent = "Soma:";
                                        cellSomaPeriodo.colSpan = 2;
                                        cellSomaPeriodo.append(textSomaPeriodo);

                                        var cellSomaGotas = document.createElement("td");
                                        var textSomaGotas = document.createElement("strong");
                                        textSomaGotas.textContent = periodo.soma_gotas;
                                        cellSomaGotas.classList.add("text-right");
                                        cellSomaGotas.append(textSomaGotas);

                                        var cellSomaLitros = document.createElement("td");
                                        var textSomaLitros = document.createElement("strong");
                                        var somaLitros = parseFloat(periodo.soma_litros);
                                        textSomaLitros.textContent = somaLitros.toFixed(2);
                                        cellSomaLitros.classList.add("text-right");
                                        cellSomaLitros.append(textSomaLitros);

                                        var cellSomaReais = document.createElement("td");
                                        var textSomaReais = document.createElement("strong");
                                        textSomaReais.textContent = "R$ " + parseFloat(periodo.soma_reais).toFixed(2);
                                        cellSomaReais.classList.add("text-right");
                                        cellSomaReais.append(textSomaReais);

                                        rowTotalPeriodo.append(cellSomaPeriodo);
                                        rowTotalPeriodo.append(cellSomaGotas);
                                        rowTotalPeriodo.append(cellSomaLitros);
                                        rowTotalPeriodo.append(cellSomaReais);

                                        rows.push(rowTotalPeriodo);

                                        //#endregion
                                    });
                                }

                                // #region Linha Total Estabelecimento
                                if (periodosKeys.length > 0) {

                                    var rowTotal = document.createElement("tr");
                                    var cellLabelTotal = document.createElement("td");
                                    var labelTotal = document.createElement("strong");

                                    labelTotal.classList.add("text-bold");
                                    labelTotal.textContent = "Total Estabelecimento";
                                    cellLabelTotal.colSpan = 2;
                                    cellLabelTotal.append(labelTotal);

                                    var cellTotalGotas = document.createElement("td");
                                    var textTotalGotas = document.createElement("strong");
                                    textTotalGotas.textContent = element.estabelecimento_gotas !== undefined ? element.estabelecimento_gotas : 0;
                                    cellTotalGotas.classList.add("text-right");
                                    cellTotalGotas.append(textTotalGotas);

                                    var cellTotalLitros = document.createElement("td");
                                    var textTotalLitros = document.createElement("strong");
                                    var estabelecimentoLitros = element.estabelecimento_litros !== undefined ? parseFloat(element.estabelecimento_litros) : 0;
                                    textTotalLitros.textContent = estabelecimentoLitros.toFixed(2);
                                    cellTotalLitros.classList.add("text-right");
                                    cellTotalLitros.append(textTotalLitros);

                                    var cellTotalReais = document.createElement("td");
                                    var textTotalReais = document.createElement("strong");

                                    var totalReais = parseFloat(element.estabelecimento_reais);

                                    if (isNaN(totalReais)) {
                                        totalReais = 0;
                                    }

                                    textTotalReais.textContent = "R$ " + totalReais.toFixed(2);
                                    cellTotalReais.classList.add("text-right");
                                    cellTotalReais.append(textTotalReais);

                                    rowTotal.append(cellLabelTotal);
                                    rowTotal.append(cellTotalGotas);
                                    rowTotal.append(cellTotalLitros);
                                    rowTotal.append(cellTotalReais);

                                    rows.push(rowTotal);
                                }
                                //#endregion
                            });
                            // #region Linha Total Geral

                            var rowTotal = document.createElement("tr");
                            var cellLabelTotal = document.createElement("td");
                            var labelTotal = document.createElement("strong");

                            labelTotal.classList.add("text-bold");
                            labelTotal.textContent = "Total Geral";
                            cellLabelTotal.colSpan = 2;
                            cellLabelTotal.append(labelTotal);

                            var cellTotalGotas = document.createElement("td");
                            var textTotalGotas = document.createElement("strong");
                            textTotalGotas.textContent = data.total_gotas !== undefined ? data.total_gotas : 0;
                            cellTotalGotas.classList.add("text-right");
                            cellTotalGotas.append(textTotalGotas);

                            var cellTotalLitros = document.createElement("td");
                            var textTotalLitros = document.createElement("strong");
                            var totalLitros = data.total_litros !== undefined ? parseFloat(data.total_litros) : 0;
                            textTotalLitros.textContent = totalLitros.toFixed(2);
                            cellTotalLitros.classList.add("text-right");
                            cellTotalLitros.append(textTotalLitros);

                            var cellTotalReais = document.createElement("td");
                            var textTotalReais = document.createElement("strong");

                            var totalReais = parseFloat(data.total_reais);

                            if (isNaN(totalReais)) {
                                totalReais = 0;
                            }

                            textTotalReais.textContent = "R$ " + totalReais.toFixed(2);
                            cellTotalReais.classList.add("text-right");
                            cellTotalReais.append(textTotalReais);

                            rowTotal.append(cellLabelTotal);
                            rowTotal.append(cellTotalGotas);
                            rowTotal.append(cellTotalLitros);
                            rowTotal.append(cellTotalReais);

                            rows.push(rowTotal);
                        }
                        //#endregion
                    } else {
                        if (data.total_gotas == 0 && data.total_litros == 0 && data.total_reais == 0) {
                            var rowEmpty = document.createElement("tr");
                            var cell = document.createElement("td");
                            var label = document.createElement("strong");

                            label.textContent = "Não há registros à serem exibidos!";
                            cell.append(label);
                            cell.colSpan = 7;
                            cell.classList.add("text-center");
                            rowEmpty.append(cell);
                            rows.push(rowEmpty);

                            imprimirBtn.addClass("disabled");
                            imprimirBtn.addClass("readonly");
                            imprimirBtn.unbind("click");

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

                                // Título de Pontuações

                                var rowHeaderQuantidades = document.createElement("tr");
                                var cellHeaderQuantidadeGotas = document.createElement("td");
                                var labelHeaderQuantidadeGotas = document.createElement("strong");
                                labelHeaderQuantidadeGotas.textContent = "Quantidade de Gotas";
                                cellHeaderQuantidadeGotas.append(labelHeaderQuantidadeGotas);

                                var cellHeaderQuantidadeLitros = document.createElement("td");
                                var labelHeaderQuantidadeLitros = document.createElement("strong");
                                labelHeaderQuantidadeLitros.textContent = "Quantidade Litros";
                                cellHeaderQuantidadeLitros.append(labelHeaderQuantidadeLitros);

                                var cellHeaderQuantidadeReais = document.createElement("td");
                                var labelHeaderQuantidadeReais = document.createElement("strong");
                                labelHeaderQuantidadeReais.textContent = "Reais";
                                cellHeaderQuantidadeReais.append(labelHeaderQuantidadeReais);

                                rowHeaderQuantidades.append(cellHeaderQuantidadeGotas);
                                rowHeaderQuantidades.append(cellHeaderQuantidadeLitros);
                                rowHeaderQuantidades.append(cellHeaderQuantidadeReais);

                                // Valores de Pontuações

                                var rowSomaEstabelecimento = document.createElement("tr");
                                var cellLabelQuantidadeGotas = document.createElement("td");
                                var labelQuantidadeGotas = document.createElement("strong");
                                var quantidadeGotas = parseFloat(element.pontuacoes.quantidade_gotas);

                                if (isNaN(quantidadeGotas)) {
                                    quantidadeGotas = 0;
                                }

                                labelQuantidadeGotas.textContent = quantidadeGotas.toFixed(2);;
                                cellLabelQuantidadeGotas.classList.add("text-right");
                                cellLabelQuantidadeGotas.append(labelQuantidadeGotas);

                                var cellLabelQuantidadeLitros = document.createElement("strong");
                                var quantidadeLitros = parseFloat(element.pontuacoes.quantidade_litros);

                                if (isNaN(quantidadeLitros)) {
                                    quantidadeLitros = 0;
                                }

                                cellLabelQuantidadeLitros.textContent = quantidadeLitros.toFixed(2);
                                var cellQuantidadeLitros = document.createElement("td");
                                cellQuantidadeLitros.classList.add("text-right");
                                cellQuantidadeLitros.append(cellLabelQuantidadeLitros);

                                var cellLabelReais = document.createElement("strong");
                                var reais = parseFloat(element.pontuacoes.quantidade_reais);

                                if (isNaN(reais)) {
                                    reais = 0;
                                }

                                cellLabelReais.textContent = reais.toFixed(2);
                                var cellReais = document.createElement("td");
                                cellReais.classList.add("text-right");
                                cellReais.append(cellLabelReais);

                                rowSomaEstabelecimento.append(cellLabelQuantidadeGotas);
                                rowSomaEstabelecimento.append(cellQuantidadeLitros);
                                rowSomaEstabelecimento.append(cellReais);

                                rows.push(rowCliente);
                                rows.push(rowHeaderQuantidades);
                                rows.push(rowSomaEstabelecimento);
                            });

                            //#region Total

                            var rowTotal = document.createElement("tr");
                            var cellTotalGotas = document.createElement("td");
                            var labelTotalGotas = document.createElement("strong");
                            var totalGotas = parseFloat(data.total_gotas).toFixed(2);
                            labelTotalGotas.textContent = totalGotas;
                            cellTotalGotas.classList.add("text-right");
                            cellTotalGotas.append(labelTotalGotas);

                            var cellTotalLitros = document.createElement("strong");
                            var totalLitros = parseFloat(data.total_litros);
                            cellTotalLitros.textContent = totalLitros.toFixed(2);
                            var labelTotalLitros = document.createElement("td");
                            labelTotalLitros.classList.add("text-right");
                            labelTotalLitros.append(cellTotalLitros);

                            var cellTotalReais = document.createElement("strong");
                            var totalReais = parseFloat(data.total_reais);
                            cellTotalReais.textContent = "R$ " + totalReais.toFixed(2);
                            var labelTotalReais = document.createElement("td");
                            labelTotalReais.classList.add("text-right");
                            labelTotalReais.append(cellTotalReais);

                            rowTotal.append(cellTotalGotas);
                            rowTotal.append(labelTotalLitros);
                            rowTotal.append(labelTotalReais);

                            rows.push(rowTotal);

                            //#endregion
                        }
                    }
                    conteudoTabela.append(rows);
                },
                error: function (response) {
                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.errors);
                }
            });
        }

        /**
         * Obtem Gotas Cliente
         *
         * Obtem dados de Gotas do estabelecimento selecionado
         *
         * relatorio_gotas.js::getGotasCliente()
         *
         * @param {int} clientesId Id do cliente
         *
         * @return void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-11
         */
        function getGotasCliente(clientesId) {
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
                    gotas = [];

                    var option = document.createElement("option");
                    option.value = null;
                    option.textContent = "<Todas>";
                    gotasSelectListBox.append(option);

                    response.data.gotas.forEach(element => {

                        var gota = {
                            id: element.id,
                            nomeParametro: element.nome_parametro

                        };

                        var option = document.createElement("option");
                        option.value = gota.id;
                        option.textContent = gota.nomeParametro;
                        gotasSelectListBox.append(option);

                        gotas.push(gota);
                    });
                },
                error: function (response) {
                    var mensagem = response.responseJSON.mensagem;

                    callModalError(mensagem.message, mensagem.errors);
                }
            });
        }

        // #endregion

        // #endregion

        // "Constroi" a tela
        init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(function (e) {
        closeLoaderAnimation();
        console.log(e);
    });
