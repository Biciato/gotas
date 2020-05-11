/**
 * Arquivo de funcionalidades do template src/Templates/Redes/index.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-04-22
 */

var redesView = {
    id: {},
    // #region Functions

    /**
     * Faz solicitação de alterar estado de habilitado do Estabelecimento
     *
     * @param {int} id Id de registro
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-11
     */
    changeStatusEstablishment: async function (evt) {
        let id = event.target.getAttribute('data-id');
        let networkName = event.target.getAttribute('data-name');
        let status = event.target.getAttribute('data-status');
        let question = "Deseja :param o estabelecimento :establishment?"
            .replace(":param", status ? "ativar" : "desativar")
            .replace(":establishment", networkName);

        let buttons = [{
                label: "Cancelar",
                action: ((dialogItSelf) => dialogItSelf.close())
            },
            {
                label: "OK",
                action: async function (dialogItSelf) {
                    try {
                        let response = await clientesServices.changeStatus(id);

                        if (response === undefined || response === null || !response) {
                            toastr.error(response.mensagem.message);
                            return false;
                        }

                        dialogItSelf.close();
                    } catch (error) {
                        console.log(error);
                        var msg = {};

                        if (error.responseJSON !== undefined) {
                            toastr.error(error.responseJSON.mensagem.errors.join(" "), error.responseJSON.mensagem.message);
                            return false;
                        } else if (error.responseText !== undefined) {
                            msg = error.responseText;
                        } else {
                            msg = error;
                        }

                        toastr.error(msg);
                        return false;
                    }

                    // tudo ok, faz reload
                    $("#form").find("#btn-search").trigger("click");
                    return false;
                }
            }
        ];

        let param = {
            message: question,
            title: "Atenção!",
            type: BootstrapDialog.TYPE_DANGER,
            buttons: buttons
        };

        BootstrapDialog.show(param);
    },

    /**
     * Faz solicitação de remover um estabelecimento do sistema
     *
     * @param {int} id Id de registro
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-11
     */
    deleteEstablishment: function (event) {
        event.preventDefault();

        let id = event.target.getAttribute('data-id');
        let establishmentName = event.target.getAttribute('data-name');
        let question = "Deseja apagar o estabelecimento :establishment?"
            .replace(":establishment", establishmentName);

        bootbox.prompt({
            title: question,
            message: `
            <strong>Não será possível recuperar os dados deste estabelecimento!</strong>
            <p>
                    Confirme sua senha para continuar
                </p>`,
            locale: "pt",
            inputType: 'password',
            callback: async function (result) {
                if (result === null || result === undefined) {
                    return false;
                }
                try {
                    let response = await clientesServices.delete(id, result);

                    if (response === undefined || response === null || !response) {
                        return false;
                    }

                    toastr.success(response.mensagem.message);

                    // tudo ok, faz reload
                    $("#form").find("#btn-search").trigger("click");
                    return false;
                } catch (error) {
                    console.log(error);
                    var msg = {};

                    if (error.responseJSON !== undefined) {
                        toastr.error(error.responseJSON.mensagem.errors.join(" "), error.responseJSON.mensagem.message);
                        return false;
                    } else if (error.responseText !== undefined) {
                        msg = error.responseText;
                    } else {
                        msg = error;
                    }

                    toastr.error(msg);
                    return false;
                }
            },
        });
    },
    /**
     * Método 'construtor' da tela
     *
     * @param {Integer} id Id da Rede
     */
    init: async function (id) {
        'use strict';
        var self = this;
        document.title = 'GOTAS - Informações de Rede';
        self.id = id;

        // Máscaras
        $("#cnpj").mask("##.###.###/####-##");

        // Define comportamento do botão de pesquisar estabelecimentos
        $(document)
            .off("click", "#form #btn-search")
            .on("click", "#form #btn-search", self.refreshDataTable);
        // Adiciona enter dentro do form, pesquisar
        $(document)
            .off("keydown", "#form")
            .on("keydown", "#form", function (evt) {
                if (evt.keyCode == 13) {
                    evt.preventDefault();
                    self.refreshDataTable(evt);
                }
            });

        $(document)
            .off("click", "#clientes-table .delete-item")
            .on("click", "#clientes-table .delete-item", self.deleteEstablishment);
        $(document)
            .off("click", "#clientes-table .change-status")
            .on("click", "#clientes-table .change-status", self.changeStatusEstablishment);

        try {
            let rede = await redesServices.getById(id);

            self.fillData(rede);
            self.getClientes(id);
        } catch (error) {
            console.log(error);
            var msg = {};

            if (error.responseJSON !== undefined) {
                toastr.error(error.responseJSON.mensagem.errors.join(" "), error.responseJSON.mensagem.message);
                return false;
            } else if (error.responseText !== undefined) {
                msg = error.responseText;
            } else {
                msg = error;
            }

            toastr.error(msg);
            return false;
        }

        return self;
    },

    /**
     * Preenche input do form
     *
     * @param {Rede} data
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-22
     */
    fillData: function (data) {
        if (data === undefined || data === null || data.id === undefined) {
            // Se informação vazia, limpa todos os campos

            // Título
            $("span[id=nome-rede]").text(null);

            // Formulário
            $("input[id=nome-rede]").val(null);
            $("#nome-img").prop("src", null);
            $("#ativado").prop("checked", false);
            $("#tempo-expiracao-gotas-usuarios").val(null);
            $("#quantidade-pontuacoes-usuarios-dia").val(null);
            $("#quantidade-consumo-usuarios-dia").val(null);
            $("#qte-mesmo-brinde-resgate-dia").val(null);
            $("#qte-gotas-minima-bonificacao").val(null);
            $("#qte-gotas-bonificacao").val(null);
            $("#propaganda-img").prop("src", null);
            $("#propaganda-link").prop("href", null);
            $("#custo-referencia-gotas").val(null);
            $("#media-assiduidade-clientes").val(null);
            $("#app-personalizado").prop("checked", false);
            $("#msg-distancia-compra-brinde").prop("checked", false);
            $("#pontuacao-extra-produto-generico").val(false);

        } else {
            // Preenche todos os campos do form

            // Título
            $("span[id=nome-rede]").text(data.nome_rede);

            // Formulário
            $("input[id=nome-rede]").val(data.nome_rede);
            $("#nome-img").prop("src", data.nome_img_completo);
            $("#ativado").prop("checked", data.ativado);
            $("#tempo-expiracao-gotas-usuarios").val(data.tempo_expiracao_gotas_usuarios);
            $("#quantidade-pontuacoes-usuarios-dia").val(data.quantidade_pontuacoes_usuarios_dia);
            $("#quantidade-consumo-usuarios-dia").val(data.quantidade_consumo_usuarios_dia);
            $("#qte-mesmo-brinde-resgate-dia").val(data.qte_mesmo_brinde_resgate_dia);
            $("#qte-gotas-minima-bonificacao").val(data.qte_gotas_minima_bonificacao);
            $("#qte-gotas-bonificacao").val(data.qte_gotas_bonificacao);
            $("#propaganda-img").prop("src", data.propaganda_img_completo);
            $("#propaganda-link").prop("href", data.propaganda_link);
            $("#custo-referencia-gotas").val(data.custo_referencia_gotas);
            $("#media-assiduidade-clientes").val(data.media_assiduidade_clientes);
            $("#app-personalizado").prop("checked", data.app_personalizado);
            $("#msg-distancia-compra-brinde").prop("checked", data.msg_distancia_compra_brinde);
            $("#pontuacao-extra-produto-generico").prop("checked", data.pontuacao_extra_produto_generico);
        }
    },
    /**
     * Obtem dados de Clientes e popula tabela
     *
     * @param {Int} redesId
     * @param {String} nome_fantasia Nome Fantasia
     * @param {String} razao_social Razao Social
     * @param {String} cnpj CNPJ
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-22
     */
    getClientes: function (redesId, nome_fantasia, razao_social, cnpj) {
        let url = `/api/clientes/`;

        let btnHelper = new ButtonHelper();
        let columns = [{
                data: "id",
                title: "Id",
                orderable: true,
                visible: false,
            },
            {
                data: "nome_fantasia",
                title: "Nome Fantasia",
                orderable: true,
            },
            {
                data: "razao_social",
                title: "Razão Social",
                orderable: true,
            },
            {
                data: "cnpj",
                title: "CNPJ",
                orderable: true,
            },
            {
                data: "actions",
                title: "Ações",
                orderable: false,
            }
        ];

        initPipelinedDT(
            "#clientes-table",
            columns,
            url,
            undefined,
            function (d) {
                var filters = {};
                filters.redes_id = redesId;

                let nomeFantasia = $("#form #nome-fantasia").val();
                let razaoSocial = $("#form #razao_social").val();
                let cnpj = $("#form #cnpj").val();
                let ativado = $("#form #ativado").val();

                if (nomeFantasia !== undefined && nomeFantasia !== null) {
                    filters.nome_fantasia = nomeFantasia;
                }

                if (razaoSocial !== undefined && razaoSocial !== null) {
                    filters.razao_social = razaoSocial;
                }

                if (cnpj !== undefined && cnpj !== null) {
                    filters.cnpj = cnpj.replace("/\D/", "");
                }

                if (ativado !== undefined && ativado !== null) {
                    filters.ativado = ativado;
                }

                d.filtros = filters;

                return d;
            },
            [5, 15, 20, 100],
            undefined,
            function (rowData) {
                let attributes = {
                    id: rowData.id,
                    active: rowData.ativado,
                    name: rowData.nome_fantasia
                };
                let actionView = btnHelper.generateLinkViewToDestination(`#/redes/view/${redesId}/clientes/view/${rowData.id}`, btnHelper.ICON_INFO, null, "Ver Detalhes");
                let editView = btnHelper.generateLinkEditToDestination(`#/clientes/edit/${rowData.id}`, null, "Editar");
                let deleteBtn = btnHelper.genericImgDangerButton(attributes, undefined, undefined, "delete-item", undefined);
                let changeStatus = btnHelper.generateImgChangeStatus(attributes, rowData.ativado, undefined, undefined, "change-status");

                let buttons = [actionView, editView, changeStatus, deleteBtn];
                let buttonsString = "";

                buttons.forEach(x => {
                    buttonsString += x.outerHTML + " ";
                });

                rowData["actions"] = buttonsString;
                return rowData;
            }
        );
    },
    /**
     * Filtra os registros do datatable
     *
     * @param {Event} e Evento
     */
    refreshDataTable: function (event) {
        event.preventDefault();
        if (typeof window['#clientes-table'] !== 'undefined') {
            window['#clientes-table'].clearPipeline().draw();
        }
    },

    //#endregion
};
