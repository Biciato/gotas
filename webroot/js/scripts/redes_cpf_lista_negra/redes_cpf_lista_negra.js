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

        var form = {};
        var redesList = [];
        var redesSelectListBox = $("#redes-list");
        var redesSelectedItem = {};
        var pesquisarBtn = $("#btn-pesquisar");
        var dataTable = $("#data-table");
        var newBtn = $("#new-button");
        var backBtn = $("#back-button");

        // #endregion

        // #region Functions

        /**
         * Constructor
         */
        function init() {
            // Inicializa campos date
            redesSelectListBox.unbind("change");
            redesSelectListBox.on("change", redesSelectListBoxOnChange);

            newBtn.unbind("click");
            newBtn.on("click", showNewRegion);
            backBtn.unbind("click");
            backBtn.on("click", showIndexRegion);

            // Atribuições de clicks aos botões de obtenção de relatório
            pesquisarBtn.on("click", pesquisar);

            getRedesList();
        }

        /**
         * Realiza pesquisa dos dados
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.6
         * @date 2020-03-05
         */
        async function pesquisar() {
            try {
                let response = await getCPFBlackList(form.redesId, form.dataInicio, form.dataFim, "Table");

                if (response === undefined || response === null) {
                    return false;
                }

                // @todo adicionar tratamento com datatable
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

        function showIndexRegion() {
            newBtn.css("display", "block");
            backBtn.css("display", "none");
        }

        function showNewRegion() {
            $("#dados").hide();
            $("#region-add").show();

            newBtn.css("display", "none");
            backBtn.css("display", "block");
        }

        // #region Get / Set REST Services

        function saveUpdate(redesId, cpf, typeRequest) {
            var cpf = $("#cpf-save");
            var text = cpf.val();
            var data = {
                redes_id: 0,
                cpf: text
            }
            return Promise.resolve($.ajax({
                type: typeRequest,
                url: "/api/redes_cpf_lista_negra",
                data: data,
                dataType: "JSON"
            }));
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
        function getCPFBlackList(redesId, dataInicio, dataFim, tipoExportacao) {
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
                url: "/api/redes_cpf_lista_negra/",
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
