/**
 *
 */

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
        var containerReport = $("#container-report");
        var pesquisarBtn = $("#btn-pesquisar");
        var imprimirBtn = $("#btn-imprimir");
        var exportarBtn = $("#btn-exportar");

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

            // Inicializa campos date
            dataInicio.datepicker().datepicker("setDate", dataAtual);
            dataFim.datepicker().datepicker("setDate", dataAtual);

            // Dispara todos os eventos que precisam de inicializar
            dataInicio.on("change", dataInicioOnChange);
            dataFim.on("change", dataFimOnChange);

            redesSelectListBox.unbind("change");
            redesSelectListBox.on("change", redesSelectListBoxOnChange);
            clientesSelectListBox.unbind("change");
            clientesSelectListBox.on("change", clientesSelectListBoxOnChange);

            // Atribuições de clicks aos botões de obtenção de relatório
            pesquisarBtn.on("click", pesquisar);
            imprimirBtn.addClass("disabled");
            imprimirBtn.addClass("readonly");
            imprimirBtn.unbind("click");
            imprimirBtn.on("click", imprimirRelatorio);
            exportarBtn.addClass("disabled");
            exportarBtn.addClass("readonly");
            exportarBtn.unbind("click");
            exportarBtn.on("click", exportarExcel);

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
            var promise = getRankingOperacoes(form.clientesId, form.dataInicio, form.dataFim, "Excel");

            if (promise === undefined || promise === null) {
                return false;
            }

            promise.success(function (response) {
                console.log(response);
                var content = "data:application/vnd.ms-excel," + encodeURIComponent(response.data);
                var downloadLink = document.createElement("a");
                downloadLink.href = content;
                downloadLink.download = "Relatório de Ranking de Operações.xls";

                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            }).error(function (response) {
                var msg = response.responseJSON.mensagem;
                callModalError(msg.message, msg.error);
            });
        }

        /**
         * Obtem dados de Ranking de Operações
         *
         * @param {int} redesId Id da Rede. Se não informado, pesquisa por todos da rede
         * @param {int} clientesId Id do Estabelecimento. Se não informado, pesquisa por todos da rede
         * @param {DateTime} dataInicio Data de Início da pesquisa
         * @param {DateTime} dataFim Data de Fim da pesquisa
         * @param {string} tipoExportacao Tipo de Exportação (Table / Excel / Object)
         *
         * @returns $promise Retorna uma jqAjax Promise
         */
        function getRankingOperacoes(redesId, clientesId, dataInicio, dataFim, tipoExportacao) {
            var data = {
                redes_id: redesId,
                data_inicio: dataInicio,
                data_fim: dataFim,
                tipo_exportacao: tipoExportacao
            };

            if (clientesId !== undefined && clientesId > 0) {
                data.clientes_id = form.clientesId;
            }

            if (redesId === undefined || redesId === 0) {
                callModalError("É necessário escolher uma rede para pesquisar!");
                return false;
            }

            return Promise.resolve($.ajax({
                type: "GET",
                url: "/api/clientes/ranking_operacoes",
                data: data,
                dataType: "JSON",
            }));
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
        };

        /**
         * Realiza pesquisa dos dados
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.6
         * @date 2020-03-05
         */
        async function pesquisar() {
            try {
                let response = await getRankingOperacoes(form.redesId, form.clientesId, form.dataInicio, form.dataFim, "Table");

                if (response === undefined || response === null) {
                    return false;
                }

                console.log(response);
                imprimirBtn.removeClass("disabled");
                imprimirBtn.removeClass("readonly");
                imprimirBtn.unbind("click");
                imprimirBtn.on("click", imprimirRelatorio);
                exportarBtn.removeClass("disabled");
                exportarBtn.removeClass("readonly");
                exportarBtn.unbind("click");
                exportarBtn.on("click", exportarExcel);

                console.log(response);

                $(containerReport).empty();

                $(containerReport).append(response.data.resumo_funcionario);
                $(containerReport).append(response.data.relatorio);


            } catch (error) {
                console.log(error);
                callModalError(error);
                // var msg = error.responseJSON.mensagem;
                // callModalError(msg.message, msg.error);
            }
            // var promise = getRankingOperacoes(form.redesId, form.clientesId, form.dataInicio, form.dataFim, "Table");


        };

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
                form.redesId = redesSelectedItem.id;
                getClientesList(redesSelectedItem.id);
            } else {
                form.redesId = 0;
                var option = document.createElement("option");
                option.value = 0;
                option.textContent = "Selecione uma Rede para continuar...";
                option.title = "Selecione uma Rede para continuar...";

                clientesList = [];
                clientesSelectListBox.empty();
                clientesSelectListBox.append(option);
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

        // #endregion

        // "Constroi" a tela
        init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
