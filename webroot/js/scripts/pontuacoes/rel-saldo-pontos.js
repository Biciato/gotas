/**
 * @file webroot\js\scripts\redes_cpf_lista_negra\index.js
 *
 * Arquivo de funções para Lista Negra de CPF
 *
 * @author  Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2020-03-09
 */

$
    (function () {
        'use strict';
        // #region Properties

        var formSearch = {};
        var redesList = [];
        var redesSelectListBox = $("#redes-list");
        var redesSelectedItem = {};
        var nomeFormSearch = $("#nome-usuario-form-search");
        var containerReport = $("#container-report");
        var searchBtn = $("#btn-pesquisar");
        var printBtn = $("#btn-imprimir");
        var exportBtn = $("#btn-exportar");

        // #endregion

        // #region Functions

        /**
         * Constructor
         */
        function init() {
            // Inicializa campos date
            redesSelectListBox.unbind("change");
            redesSelectListBox.on("change", redesSelectListBoxOnChange);

            nomeFormSearch.on("keydown", nomeFormSearchOnChange);

            // Atribuições de clicks aos botões de obtenção de relatório
            searchBtn.on("click", search);

            // Atribuições de clicks aos botões de obtenção de relatório
            printBtn.addClass("disabled");
            printBtn.addClass("readonly");
            printBtn.unbind("click");
            printBtn.on("click", print);
            exportBtn.addClass("disabled");
            exportBtn.addClass("readonly");
            exportBtn.unbind("click");
            exportBtn.on("click", exportExcel);

            getRedesList();
        }


        /**
         * Gera Excel
         *
         * Realiza pesquisa ao servidor e obtem conteúdo preparado para arquivo em Excel
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.5
         */
        async function exportExcel() {
            try {
                let response = await getSaldoPontos(form.redesId, form.nome, "Excel");
                // let response = await getRankingOperacoes(form.redesId, form.clientesId, "", "", "Table");

                if (response === undefined || response === null) {
                    return false;
                }

                var content = "data:application/vnd.ms-excel," + encodeURIComponent(response.data);
                var downloadLink = document.createElement("a");
                downloadLink.href = content;
                downloadLink.download = "Relatório de Saldo de Pontos de Usuário.xls";

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
         * Comportamento para campo nome de pesquisa de usuários
         *
         * @param {Event} event eventos do field
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.2.0
         * @date 2020-03-16
         */
        function nomeFormSearchOnChange(event) {
            formSearch.nome = this.value.trim();

            if (event.keyCode === 13) {
                search();
            }
        }

        function print() {
            setTimeout($(".print-region").printThis({
                importCss: false
            }), 100);
        }

        /**
         * Realiza pesquisa dos dados
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.6
         * @date 2020-03-05
         */
        async function search() {
            try {
                let response = await getSaldoPontos(formSearch.redesId, formSearch.nome, "Table");

                if (response === undefined || response === null || !response) {
                    return false;
                }

                printBtn.removeClass("disabled");
                printBtn.removeClass("readonly");
                printBtn.unbind("click");
                printBtn.on("click", print);
                exportBtn.removeClass("disabled");
                exportBtn.removeClass("readonly");
                exportBtn.unbind("click");
                exportBtn.on("click", exportExcel);

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
                formSearch.redesId = redesSelectedItem.id;
            } else {
                formSearch.redesId = 0;
                var option = document.createElement("option");
                option.value = 0;
                option.textContent = "Selecione uma Rede para continuar...";
                option.title = "Selecione uma Rede para continuar...";
            }
        }

        // #region Get / Set REST Services

        /**
         * Obtem dados de Balanço Geral de Estabelecimentos
         *
         * @param {int} redesId Id da Rede. Se não informado, pesquisa por todos da rede
         * @param {string} nome Nome de Usuário
         * @param {string} tipoExportacao Tipo de Exportação (Table / Excel / Object)
         *
         * @returns $promise Retorna uma jqAjax Promise
         */
        function getSaldoPontos(redesId, nome, tipoExportacao) {
            var data = {
                redes_id: redesId
            };

            if (redesId === undefined || redesId === 0) {
                callModalError("É necessário escolher uma rede para pesquisar!");
                return false;
            }

            return Promise.resolve($.ajax({
                type: "GET",
                url: "/api/pontuacoes/saldo_pontos",
                data: data,
                dataType: "JSON",
            }));
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
        async function getRedesList() {
            try {
                let promise = await Promise.resolve(
                    $.ajax({
                        type: "GET",
                        url: "/api/redes/get_redes_list",
                        data: {},
                        dataType: "JSON",

                    })
                );

                if (promise === undefined || promise === null) {
                    return false;
                }

                var data = promise.data.redes;

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
                redesSelectListBox.change();

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

        // #endregion

        // #endregion

        // "Constroi" a tela
        init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
