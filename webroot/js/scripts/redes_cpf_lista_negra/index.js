/**
 * @file webroot\js\scripts\redes_cpf_lista_negra\index.js
 *
 * Arquivo de funções para Lista Negra de CPF
 *
 * @author  Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2020-03-09
 */

$(function () {
        'use strict';
        // #region Properties

        var formSearch = {};
        var formSave = {};
        var redesList = [];
        var redesSelectListBox = $("#redes-list");
        var redesSelectedItem = {};
        var cpfSelectedItem = {};
        // var cpfFormSearch = $("#cpf-form-search");
        var pesquisarBtn = $("#btn-pesquisar");
        var dataTable = $("#data-table");
        var newBtn = $("#new-button");
        var backBtn = $("#back-button");
        var cpfFormSave = $(".region-add #cpf-save");
        var saveBtn = $(".region-add #btn-save");
        var removeBtn = $("#modal-remover #confirmar");

        // #endregion

        // #region Functions

        /**
         * Constructor
         */
        function init() {
            // Inicializa campos date
            redesSelectListBox.unbind("change");
            redesSelectListBox.on("change", redesSelectListBoxOnChange);

            // cpfFormSearch.mask('999.999.999-99');
            cpfFormSave.mask('999.999.999-99');
            cpfFormSave.unbind("keydown");
            cpfFormSave.on("keydown", cpfFormSaveOnChange);

            newBtn.unbind("click");
            newBtn.on("click", showNewRegion);
            backBtn.unbind("click");
            backBtn.on("click", showIndexRegion);

            saveBtn.unbind("click");
            saveBtn.on("click", saveCpf);

            removeBtn.unbind("click");
            removeBtn.on("click", deleteCpf);

            // Atribuições de clicks aos botões de obtenção de relatório
            pesquisarBtn.on("click", pesquisar);

            // Ao obter a lista de redes, pesquisa os registros
            getRedesList();
        }

        /**
         * Atualiza dados de formulario ao modificar campo de pesquisa de cpf
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.8
         * @date 2020-03-13
         */
        function cpfFormSaveOnChange(event) {
            formSave.cpf = this.value;

            if (event.keyCode === 13) {
                saveCpf();
            }
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
                let response = await getCPFBlackList(formSearch.redesId);

                if (response === undefined || response === null || !response) {
                    return false;
                }

                var data = [];

                response.data.forEach(row => {
                    var cpfFormatted = row.cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
                    var selectButton =
                        "<button class='btn btn-danger btn-xs btn-cpf-delete' data-id='" + row.id + "' data-cpf='" + cpfFormatted + "' title='Remover'> <i class=' fas fa-trash'></i></button>";

                    data.push({
                        id: row.id,
                        cpf: cpfFormatted,
                        acoes: selectButton
                    });
                });

                if ($.fn.DataTable.isDataTable("#" + dataTable.attr('id'))) {
                    dataTable.DataTable().clear();
                    dataTable.DataTable().destroy();
                }

                dataTable.DataTable({
                    language: {
                        "url": "/webroot/js/DataTables/i18n/dataTables.pt-BR.lang"
                    },
                    columns: [{
                            data: "id",
                            title: "Id",
                            orderable: true,
                            visible: false,
                        },
                        {
                            data: "cpf",
                            title: "CPF",
                            orderable: true,
                        },
                        {
                            data: "acoes",
                            title: "Ações",
                            orderable: false,
                        }
                    ],
                    data: data
                });

                $("#data-table tbody").unbind("click", "button");
                // para todo registro, ao chamar a modal questionando, atribui o id de registro e prepara o remove
                // Após renderizar a tabela, remove e reassocia evento de click dos botões
                $("#data-table tbody").on("click", "button", function () {
                    var cpf = $(this).data('cpf');
                    cpfSelectedItem = data.find(x => x.cpf === cpf);
                    console.log(cpfSelectedItem);

                    // Garante que o registro está selecionado
                    if (cpfSelectedItem !== null && cpfSelectedItem !== undefined) {
                        $("#modal-remover").modal();
                        $("#modal-remover #nome-registro").text(cpfSelectedItem.cpf);
                    }
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

        function showIndexRegion() {
            newBtn.css("display", "block");
            backBtn.css("display", "none");
            $("#dados").show();
            $("#region-add").hide();
            pesquisar();
        }

        function showNewRegion() {
            $("#dados").hide();
            $("#region-add").show();

            newBtn.css("display", "none");
            backBtn.css("display", "block");

            cpfFormSave.val(null);
        };

        /**
         * Remove um registro e atualiza tabela
         */
        async function deleteCpf() {
            try {
                let promise = await restDeleteCpf(cpfSelectedItem.id);

                $("#modal-remover").modal('hide');
                callModalGeneric(promise.mensagem.message);
                pesquisar();

            } catch (error) {
                console.log(error);
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
         * Salva um registro e exibe a tela index.
         */
        async function saveCpf() {
            try {
                let promise = await restSaveCpf();

                callModalSave();
                showIndexRegion();
                pesquisar();
                cpfFormSave.val(null);

            } catch (error) {
                console.log(error);
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


        // #region Get / Set REST Services


        /**
         * Chama serviço rest para salvar o registro
         */
        var restDeleteCpf = function (id) {

            if (id === undefined || id === null) {
                callModalError("É necessário escolher um registro para remover!");
                return false;
            }


            var promise = Promise.resolve($.ajax({
                type: "DELETE",
                url: "/api/redes_cpf_lista_negra/" + id,
                dataType: "JSON"
            }));

            return promise;
        };

        /**
         * Chama serviço rest para salvar o registro
         */
        var restSaveCpf = function () {
            var data = {
                redes_id: redesSelectedItem.id,
                cpf: cpfFormSave.val()
            }
            var promise = Promise.resolve($.ajax({
                type: "POST",
                url: "/api/redes_cpf_lista_negra",
                data: data,
                dataType: "JSON"
            }));

            return promise;
        };

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
        function getCPFBlackList(redesId) {
            var data = {
                redes_id: redesId
            };

            if (redesId === undefined || redesId === 0) {
                callModalError("É necessário escolher uma rede para pesquisar!");
                return false;
            }

            return Promise.resolve($.ajax({
                type: "GET",
                url: "/api/redes_cpf_lista_negra",
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

                    pesquisar();
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
