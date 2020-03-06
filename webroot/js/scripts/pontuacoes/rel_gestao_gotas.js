$
    (function () {
        'use strict';
        // #region Properties

        var form = {};
        var redesList = [];
        var redesSelectListBox = $("#redes-list");
        var redesSelectedItem = {};
        var clientesSelectListBox = $("#clientes-list");
        var clientesSelectedItem = {};
        var clientesList = [];
        // Referência
        var gotasList = [];
        var gotasSelectListBox = $("#gotas-list");
        var gotasSelectedItem = {};
        var brindesSelectListBox = $("#brindes-list");
        var brindesList = [];
        var brindesSelectedItem = {};
        var funcionariosList = [];
        var funcionariosSelectedItem = {};
        var funcionariosSelectListBox = $("#funcionarios-list");

        var containerReport = $("#container-report");

        var tipoMovimentacao = $("#tipo-movimentacao");
        var tipoMovimentacaoSelectedItem = {};
        var tipoRelatorio = $("#tipo-relatorio");
        var pesquisarBtn = $("#btn-pesquisar");
        var imprimirBtn = $("#btn-imprimir");
        var exportarBtn = $("#btn-exportar");

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

            // Na inicialização, pega todos os funcionários da rede
            funcionariosSelectListBox.unbind("change");
            funcionariosSelectListBox.on("change", funcionariosSelectListBoxOnChange);

            // Dispara todos os eventos que precisam de inicializar
            // dataInicioOnChange();
            // dataFimOnChange();
            getRedesList();

            redesSelectListBox.unbind("change");
            redesSelectListBox.on("change", redesSelectListBoxOnChange);

            tipoMovimentacao.unbind("change");
            tipoMovimentacao.on("change", tipoMovimentacaoOnChange);
            tipoMovimentacao.change();

            tipoRelatorioOnChange();

            // Desabilita botão de imprimir até que usuário faça alguma consulta
            imprimirBtn.addClass("disabled");
            imprimirBtn.addClass("readonly");
            imprimirBtn.unbind("click");
            exportarBtn.addClass("disabled");
            exportarBtn.addClass("readonly");
            exportarBtn.unbind("click");

            // Atribuições de clicks aos botões de obtenção de relatório
            imprimirBtn.unbind("click");
            imprimirBtn.on("click", imprimirRelatorio);
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
         * Gera Excel
         *
         * Realiza pesquisa ao servidor e obtem conteúdo preparado para arquivo em Excel
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.5
         */
        function exportarExcel() {

            getPontuacoesRelatorioEntradaSaida(form.clientesId, form.gotasId, form.brindesId, form.funcionariosId, form.dataInicio, form.dataFim, form.tipoRelatorio, tipoMovimentacaoSelectedItem, "Excel");
        }

        /**
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::funcionariosSelectListBoxOnChange
         *
         * Comportamento ao atualizar campo de funcionários
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2020-02-05
         */
        function funcionariosSelectListBoxOnChange() {
            var funcionario = parseInt(this.value);

            if (isNaN(funcionario)) {
                funcionario = 0;
            }

            funcionariosSelectedItem = funcionariosList.find(x => x.id = funcionario);

            form.funcionariosId = funcionario;
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
                getFuncionariosList(redesSelectedItem.id);
            } else {
                var option = document.createElement("option");
                option.value = 0;
                option.textContent = "Selecione uma Rede para continuar...";
                option.title = "Selecione uma Rede para continuar...";

                clientesList = [];
                clientesSelectListBox.empty();
                clientesSelectListBox.append(option);

                funcionariosList = [];
                funcionariosSelectListBox.empty();
                var option = document.createElement("option");
                option.value = 0;
                option.textContent = "Selecione uma Rede/Estabelecimento para continuar...";
                option.title = "Selecione uma Rede/Estabelecimento para continuar...";
                funcionariosSelectListBox.append(option);

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
         * Tipo Movimentação On Change
         *
         * Comportamento ao atualizar tipo de movimentação selecionada
         *
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::tipoMovimentacaoOnChange
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

            // Se sintético, não importa se é entrada ou saída
            var status = form.tipoRelatorio === "Sintético";
            tipoMovimentacao.attr('disabled', status);
            if (status === true) {
                gotasSelectListBox.attr('disabled', status);
                brindesSelectListBox.attr('disabled', status);

                gotasSelectListBox.val(0);
                brindesSelectListBox.val(0);
            } else {
                tipoMovimentacao.change();
            }
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
                                nomeFantasia: cliente.nome_fantasia_municipio_estado
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
                            getFuncionariosList(redesSelectedItem.id, clientesSelectedItem.id);
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
         * webroot\js\scripts\gotas\relatorio_entrada_saida.js::getFuncionariosList
         *
         * Obtem lista de funcionarios disponível para seleção
         *
         * @param {*} redesId Id da Rede
         * @param {*} clientesId Id do Posto
         *
         * @return SelectListBox
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-06
         */
        function getFuncionariosList(redesId, clientesId) {
            var data = {
                tipo_perfil: [5]
            };

            if (redesId !== undefined && redesId > 0) {
                data.redes_id = redesId;
            }

            if (clientesId !== undefined && clientesId > 0) {
                data.clientes_id = clientesId;
            }

            $.ajax({
                type: "GET",
                url: "/api/usuarios/get_funcionarios_list",
                data: data,
                dataType: "JSON",
                success: function (response) {

                    if (response.data !== undefined) {
                        funcionariosSelectListBox.empty();
                        funcionariosSelectListBox.prop("disabled", false);
                        funcionariosList = [];

                        var data = response.data.usuarios;
                        var collection = [];
                        var options = [];
                        var option = document.createElement("option");
                        option.title = "Selecionar Funcionário para filtro específico";
                        option.textContent = "Todos";
                        options.push(option);
                        funcionariosList = [];

                        data.forEach(dataItem => {
                            var option = document.createElement("option");
                            var item = {
                                id: dataItem.usuario.id,
                                nome: dataItem.usuario.nome
                            };

                            option.value = item.id;
                            option.textContent = item.nome;
                            collection.push(item);
                            options.push(option);

                            funcionariosList.push(item);
                        });

                        funcionariosSelectListBox.append(options);
                        funcionariosList = collection;

                        console.log(options);
                        console.log(funcionariosList);

                        if (funcionariosList.length === 1) {
                            funcionariosSelectedItem = funcionariosList[0];

                            // Option vazio e mais um funcionario? Desabilita pois só tem uma seleção possível
                            funcionariosSelectListBox.prop("disabled", true);
                        }

                        if (funcionariosSelectedItem !== undefined && funcionariosSelectedItem.id > 0) {
                            funcionariosSelectListBox.val(funcionariosSelectedItem.id);

                            funcionariosSelectListBox.change();
                        }
                    }
                },
                error: function (response) {
                    var data = response.responseJSON;
                    console.log(data);
                    callModalError(data.mensagem.message, data.mensagem.errors);
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
         * @param {int} funcionariosId Id de funcionário
         * @param {datetime} dataInicio Data Inicio
         * @param {datetime} dataFim DataFim
         * @param {string} tipoRelatorio Analítico / Sintético
         * @param {string} tipoExportacao Tipo de Exportação (Table / Excel)
         *
         * @returns HtmlTable
         */
        function getPontuacoesRelatorioEntradaSaida(clientesId, gotasId, brindesId, funcionariosId, dataInicio, dataFim, tipoRelatorio, tipoMovimentacao, tipoExportacao) {
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
                funcionarios_id: funcionariosId,
                data_inicio: dataInicioEnvio,
                data_fim: dataFimEnvio,
                tipo_relatorio: tipoRelatorio,
                tipo_movimentacao: tipoMovimentacao,
                tipo_exportacao: tipoExportacao,
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
                    exportarBtn.removeClass("disabled");
                    exportarBtn.removeClass("readonly");
                    exportarBtn.unbind("click");
                    exportarBtn.on("click", exportarExcel);

                    console.log(response);

                    if (tipoExportacao === "Table") {
                        $(containerReport).empty();

                        $(containerReport).append(response.data.resumo_funcionario);
                        $(containerReport).append(response.data.relatorio);
                    } else if (tipoExportacao === "Excel") {
                        console.log(response);
                        var content = "data:application/vnd.ms-excel," + encodeURIComponent(response.data);
                        // window.open(content);

                        // var str = "Name, Price\nApple, 2\nOrange, 3";
                        // var uri = 'data:text/csv;charset=utf-8,' + str;

                        var downloadLink = document.createElement("a");
                        downloadLink.href = content;
                        downloadLink.download = "Relatório de Gestão de Gotas.xls";

                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                    }
                },
                error: function (response) {

                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.errors);
                },
                complete: function (response) {
                    // desabilitado a pedido do samuel
                    // getResumoPontuacoesRelatorioEntradaSaida(redesSelectedItem.id, form.clientesId, dataInicio, dataFim);
                }
            });
        }

        /**
         * Obtem Resumo de Brinde
         *
         * Obtem informações de resumo do brinde selecionado
         *
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::getResumoBrinde
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@gmail.com>
         * @since 2019-12-04
         *
         * @param {int} brindesId
         * @param {datetime} dataInicio
         * @param {datetime} dataFim
         *
         * @returns HtmlTable
         */
        function getResumoBrinde(brindesId, dataInicio, dataFim) {
            // Validação
            tabelaResumoBrinde.hide();

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
         * @deprecated 1.1.5 Desabilitado a pedido do samuel
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
            getPontuacoesRelatorioEntradaSaida(form.clientesId, form.gotasId, form.brindesId, form.funcionariosId, form.dataInicio, form.dataFim, form.tipoRelatorio, tipoMovimentacaoSelectedItem, "Table");

            getResumoBrinde(form.brindesId, form.dataInicio, form.dataFim);
        });



        // #endregion

        // #endregion

        // "Constroi" a tela
        init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
