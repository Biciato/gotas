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
            .on("click", "#form #btn-search", self.filterEstablishments);
        // Adiciona enter dentro do form, pesquisar
        $(document)
            .off("keyup", "#form")
            .on("keyup", "#form", function (evt) {
                evt.preventDefault();
                if (evt.keyCode == 13) {
                    if (typeof window['#clientes-table'] !== 'undefined') {
                        window['#clientes-table'].clearPipeline().draw();
                    }
                }
            })

        try {
            let redeResponse = await self.getRedeInfoRest(id);

            self.fillData(redeResponse.data);
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
     *
     * @param {Rede} data
     */
    fillData: function (data) {
        console.log(data);
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
            $("#pontuacao-extra-produto-generico").val(data.pontuacao_extra_produto_generico);
        }

    },

    filterEstablishments: function (e) {
        e.preventDefault();
        if (typeof window['#clientes-table'] !== 'undefined') {
            window['#clientes-table'].clearPipeline().draw();
        }
    },

    //#region Services

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

                if (nomeFantasia !== undefined && nomeFantasia !== null) {
                    filters.nome_fantasia = nomeFantasia;
                }

                if (razaoSocial !== undefined && razaoSocial !== null) {
                    filters.razao_social = razaoSocial;
                }

                if (cnpj !== undefined && cnpj !== null) {
                    filters.cnpj = cnpj.replace("/\D/", "");
                }

                d.filtros = filters;

                return d;
            },
            [5, 15, 20, 100],
            undefined,
            function (rowData) {

                let columnIndex = 4;
                let column = rowData[columnIndex];

                let attributes = {
                    id: rowData.id,
                    active: rowData.ativado,
                    name: rowData.nome_rede
                };

                let actionView = btnHelper.generateLinkViewToDestination(`#/clientes/view/${rowData.id}`, btnHelper.ICON_INFO, null, "Ver Detalhes");
                let editView = btnHelper.generateLinkEditToDestination(`#/clientes/edit/${rowData.id}`, null, "Editar");
                let deleteBtn = btnHelper.genericImgDangerButton(attributes, undefined, undefined, "delete-item", undefined);
                let changeStatus = btnHelper.generateImgChangeStatus(attributes, rowData.ativado, undefined, undefined, "change-status");

                let buttons = [actionView, editView, deleteBtn, changeStatus];
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
     * Obtêm redes
     *
     * @param {String} nomeRede Nome da rede
     * @param {Boolean} ativado Rede Ativada
     * @param {Boolean} appPersonalizado Rede com Aplicativos Personalizados
     * @returns JSON data
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-22
     */
    getRedeInfoRest: function (idRede) {
        let url = `/api/redes/${idRede}`;

        return Promise.resolve(
            $.ajax({
                type: "GET",
                url: url,
                dataType: "JSON"
            }));
    }

    //#endregion
    //#endregion
};
