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
        var funcionariosSelectListBox = $("#funcionarios-list");
        var funcionarios = [];
        var brindes = [];

        var gotasSelectListBox = $("#gotas-list");
        var gotas = [];
        var gotasSelectedItem = {};

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
            brindes = [];
            var option = document.createElement("option");
            option.value = undefined;
            option.textContent = "Selecione um Estabelecimento para continuar...";
            option.title = "Selecione um Estabelecimento para continuar...";

            brindes.push(option);
            funcionariosSelectListBox.empty();
            funcionariosSelectListBox.append(brindes);

            // Inicializa campos date
            dataInicio.datepicker().datepicker("setDate", dataAtual);
            dataFim.datepicker().datepicker("setDate", dataAtual);

            // Dispara todos os eventos que precisam de inicializar
            // dataInicioOnChange();
            // dataFimOnChange();
            tipoRelatorioOnChange();
            getClientesList();

            // #region Bindings

            funcionariosSelectListBox.on("change", brindesSelectListBoxOnChange);
            clientesSelectListBox.on("change", clientesSelectListBoxOnChange);
            dataInicio.on("change", dataInicioOnChange);
            dataFim.on("change", dataFimOnChange);
            tipoRelatorio.on("change", tipoRelatorioOnChange);

            pesquisarBtn.on("click", function () {
                dataInicio.change();
                dataFim.change();
                getRelatorioMovimentacaoGotas(form.clientesId, form.brindesId, form.dataInicio, form.dataFim, form.tipoRelatorio);
            });

            imprimirBtn.on("click", imprimirRelatorio);

            var option = document.createElement("option");
            option.value = null;
            option.textContent = "<Selecione um Estabelecimento para continuar>";
            gotasSelectListBox.append(option);

            // #endregion

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
            getGotasCliente(form.clientesId);
        }

        function gotasSelectlistBoxOnChange() {
            form.gotasId = gotasSelectedItem;
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
                console.log(form);
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

                        var option = document.createElement("option");
                        option.value = undefined;
                        option.textContent = "Todos";

                        clientes.push(option);

                        res.data.clientes.forEach(cliente => {
                            var cliente = {
                                id: cliente.id,
                                nomeFantasia: cliente.nome_fantasia
                            };

                            var option = document.createElement("option");
                            option.value = cliente.id;
                            option.textContent = cliente.nomeFantasia;

                            clientes.push(cliente);

                            clientesSelectListBox.append(option);
                        });
                        clientesSelectedItem = clientes.find(x => x.id == clientesSelectListBox.val());

                        if (clientesSelectedItem !== undefined && clientesSelectedItem.id > 0) {
                            clientesSelectListBox.val(clientesSelectedItem.id);
                        }

                        // Option vazio e mais um Estabelecimento? Desabilita pois só tem uma seleção possível
                        if (clientes.length == 2) {
                            clientesSelectListBox.prop("disabled", true);
                        }
                    }


                },
                error: function (response) {
                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.error);
                },
                complete: function (response) {
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
         * @param {int} gotasId id de Gotas
         * @param {datetime} dataInicio Data Inicio
         * @param {datetime} dataFim DataFim
         * @param {string} tipoRelatorio Analítico / Sintético
         *
         * @returns HtmlTable
         */
        function getRelatorioMovimentacaoGotas(clientesId, gotasId, dataInicio, dataFim, tipoRelatorio) {
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
                    imprimirBtn.on("click", imprimirRelatorio);

                    var data = response.data;

                    if (data !== undefined) {
                        conteudoTabela.empty();

                        $(tabela).hide();
                        $(tabela).fadeIn(500);
                    }

                    var rows = [];

                    if (form.tipoRelatorio == "Analítico") {

                        console.log(response);

                        // return;
                        data.pontuacoes.forEach(element => {
                            // Linhas periodos

                            var rowsPeriodos = [];

                            var periodos = element.periodos;

                            // O Javascript é uma desgraça. se eu retorno um array com índices declaradas, ele entende como um object

                            var periodosKeys = Object.keys(periodos);

                            periodosKeys.forEach(periodoKey => {
                                var periodo = periodos[periodoKey];

                                var pontuacoesLength = periodo.pontuacoes.length;

                                periodo.pontuacoes.forEach(pontuacao => {
                                    // Dados do Estabelecimento
                                    var rowCliente = document.createElement("tr");

                                    var labelCliente = document.createElement("strong");
                                    labelCliente.textContent = "Estabelecimento: ";
                                    var cellLabelCliente = document.createElement("td");
                                    cellLabelCliente.classList.add("font-weight-bold");
                                    cellLabelCliente.addClass("text-right");
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

                            });


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

                            rowHeaderQuantidades.append(cellHeaderQuantidadeGotas);
                            rowHeaderQuantidades.append(cellHeaderQuantidadeLitros);

                            // Valores de Pontuações

                            var rowSomaEstabelecimento = document.createElement("tr");
                            var cellLabelQuantidadeGotas = document.createElement("td");
                            var labelQuantidadeGotas = document.createElement("strong");
                            var quantidadeGotas = parseFloat(element.pontuacoes.quantidade_gotas).toFixed(2);
                            labelQuantidadeGotas.textContent = quantidadeGotas;
                            cellLabelQuantidadeGotas.classList.add("text-right");
                            cellLabelQuantidadeGotas.append(labelQuantidadeGotas);

                            var cellLabelQuantidadeLitros = document.createElement("strong");
                            var quantidadeLitros = parseFloat(element.pontuacoes.quantidade_litros);
                            cellLabelQuantidadeLitros.textContent = quantidadeLitros.toFixed(2);
                            var cellQuantidadeLitros = document.createElement("td");
                            cellQuantidadeLitros.classList.add("text-right");
                            cellQuantidadeLitros.append(cellLabelQuantidadeLitros);

                            rowSomaEstabelecimento.append(cellLabelQuantidadeGotas);
                            rowSomaEstabelecimento.append(cellQuantidadeLitros);

                            rows.push(rowCliente);
                            rows.push(rowHeaderQuantidades);
                            rows.push(rowSomaEstabelecimento);
                        });

                        // Total

                        // Valores de Pontuações

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

                        rowTotal.append(cellTotalGotas);
                        rowTotal.append(labelTotalLitros);

                        rows.push(rowTotal);

                    }
                    conteudoTabela.append(rows);
                },
                error: function (response) {

                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.errors);
                }
            });
        }

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
    .ajaxStop(closeLoaderAnimation);
