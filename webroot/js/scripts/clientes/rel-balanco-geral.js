/**
 * @file webroot\js\scripts\pontuacoes\rel_ranking_operacoes.js
 *
 * Arquivo de funções para Relatório de Ranking de Operações
 *
 * @author  Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2020-03-09
 */

$
    (function () {
        'use strict';
        // #region Properties

        var form = {};
        var redesList = [];
        var redesSelectListBox = $("#redes-list");
        var redesSelectedItem = {};
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

        /**
         * Constructor
         */
        function init() {
            // Inicializa campos date
            dataInicio.datepicker().datepicker("setDate", dataAtual);
            dataFim.datepicker().datepicker("setDate", dataAtual);

            // Dispara todos os eventos que precisam de inicializar
            dataInicio.on("change", dataInicioOnChange);
            dataFim.on("change", dataFimOnChange);
            dataInicio.change();
            dataFim.change();

            redesSelectListBox.unbind("change");
            redesSelectListBox.on("change", redesSelectListBoxOnChange);

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
        async function exportarExcel() {
            try {
                let response = await getBalancoGeral(form.redesId, form.dataInicio, form.dataFim, "Excel");

                if (response === undefined || response === null) {
                    return false;
                }

                var content = "data:application/vnd.ms-excel," + encodeURIComponent(response.data);
                var downloadLink = document.createElement("a");
                downloadLink.href = content;
                downloadLink.download = "Balanço Geral.xls";

                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);

            } catch (error) {
                var msg = {};
                if (error.responseJSON !== undefined) {
                    msg = error.responseJSON.mensagem;
                    callModalError(msg.message, msg.errors);
                } else if (error.responseText !== undefined) {
                    msg = error.responseText;
                    callModalError(msg);
                } else {
                    msg = error;
                    callModalError(msg);
                }
            }
        }

        /**
         * Obtem dados de Balanço Geral de Estabelecimentos
         *
         * @param {int} redesId Id da Rede. Se não informado, pesquisa por todos da rede
         * @param {DateTime} dataInicio Data de Início da pesquisa
         * @param {DateTime} dataFim Data de Fim da pesquisa
         * @param {string} tipoExportacao Tipo de Exportação (Table / Excel / Object)
         *
         * @returns $promise Retorna uma jqAjax Promise
         */
        function getBalancoGeral(redesId, dataInicio, dataFim, tipoExportacao) {
            var data = {
                redes_id: redesId,
                data_inicio: dataInicio,
                data_fim: dataFim,
                tipo_exportacao: tipoExportacao
            };

            if (redesId === undefined || redesId === 0) {
                callModalError("É necessário escolher uma rede para pesquisar!");
                return false;
            }

            return Promise.resolve($.ajax({
                type: "GET",
                url: "/api/clientes/balanco_geral",
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
                let response = await getBalancoGeral(form.redesId, form.dataInicio, form.dataFim, "Table");

                if (response === undefined || response === null) {
                    return false;
                }

                imprimirBtn.removeClass("disabled");
                imprimirBtn.removeClass("readonly");
                imprimirBtn.unbind("click");
                imprimirBtn.on("click", imprimirRelatorio);
                exportarBtn.removeClass("disabled");
                exportarBtn.removeClass("readonly");
                exportarBtn.unbind("click");
                exportarBtn.on("click", exportarExcel);

                $(containerReport).empty();
                var indexesData = Object.keys(response.data);
                indexesData.forEach(element => {
                    $(containerReport).append(response.data[element]);
                });
            } catch (error) {
                var msg = {};
                if (error.responseJSON !== undefined) {
                    msg = error.responseJSON.mensagem;
                    callModalError(msg.message, msg.errors);
                } else if (error.responseText !== undefined) {
                    msg = error.responseText;
                    callModalError(msg);
                } else {
                    msg = error;
                    callModalError(msg);
                }
            }
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
            } else {
                form.redesId = 0;
                var option = document.createElement("option");
                option.value = 0;
                option.textContent = "Selecione uma Rede para continuar...";
                option.title = "Selecione uma Rede para continuar...";
            }
        }

        // #region Get / Set REST Services

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
