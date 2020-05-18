/**
 * Arquivo de funcionalidades do template src/Templates/Redes/index.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-04-22
 */

var manageUser = {
    // #region Functions

    /**
     * 'Constructor'
     */
    init: function () {
        'use strict';
        var self = this;
        document.title = 'GOTAS - Controlar Usuário';

        self.initDataTable();
        self.fillTipoPerfisSelectList("#tipo-perfis-select-list")
        self.fillRedesSelectList("#redes-select-list");

        // $(document).find(".manage-user #tipo-perfis-select-list").select2();
        // $(document).find(".manage-user #redes-select-list").select2();
        // Adiciona enter dentro do form, pesquisar
        $(document)
            .off("keydown", "#manage-user-form")
            .on("keydown", "#manage-user-form", function (evt) {
                if (evt.keyCode == 13) {
                    evt.preventDefault();
                    self.refreshDataTable(evt);
                    return false;
                }
            });

        $(document)
            .off("click", "#manage-user-form #btn-search")
            .on("click", "#manage-user-form #btn-search", self.refreshDataTable);

        // $(document)
        //     .off("click", "#manage-user-form #data-table .btn-manage-user")
        //     .on("click", "#manage-user-form #data-table .btn-manage-user", self.startUserManagement);

        return self;
    },

    fillRedesSelectList: async (element) => {
        try {
            var response = await redesService.getList();
            if (response === undefined || response === null || !response) {
                return false;
            }

            $(document).find(element).empty();
            $(document).find(element).append(new Option(`<Todos>`, 0));

            response.forEach(rede => {
                $(document).find(element).append(new Option(rede.nome_rede, rede.id));
            });

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
    fillTipoPerfisSelectList: async (element) => {
        try {
            var response = await usuariosService.getPerfisList();
            if (response === undefined || response === null || !response) {
                return false;
            }

            $(document).find(element).empty();
            $(document).find(element).append(new Option(`<Todos>`, 0));

            // @TODO Arrumar essa gambiarra depois ¬¬

            let keys = Object.keys(response);
            keys.forEach(key => {
                $(document).find(element).append(new Option(response[key], key));
            });

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
    /**
     * Pesquisa dados e popula datatable
     *
     * @returns DataTables
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-22
     */
    initDataTable: function (evt) {
        'use strict';
        let btnHelper = new ButtonHelper();
        let imgHelper = new ImageHelper();
        let columns = [{
                data: "id",
                title: "Id",
                orderable: true,
                visible: false,
            },
            {
                data: "nome_img_completo",
                width: "10%",
                title: "",
                className: "text-center",
                orderable: false,
            },
            {
                data: "nome_rede",
                title: "Rede",
                orderable: true,
            },
            {
                data: "actions",
                title: "Ações",
                orderable: false,
            }
        ];

        initPipelinedDT(
            ".manage-user #data-table",
            columns,
            '/api/usuarios',
            undefined,
            function (d) {
                var filters = {};

                let nomeRede = $("#form #nome-rede").val();
                let ativado = $("#form #ativado").val();
                let appPersonalizado = $("#form #app-personalizado").val();

                if (nomeRede !== undefined && nomeRede !== null) {
                    filters.nome_rede = nomeRede;
                }

                if (ativado !== undefined && ativado !== null) {
                    filters.ativado = ativado;
                }

                if (appPersonalizado !== undefined && appPersonalizado !== null) {
                    filters.app_personalizado = appPersonalizado;
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
                    name: rowData.nome_rede
                };

                if (rowData.nome_img_completo !== undefined && rowData.nome_img_completo !== null) {
                    rowData["nome_img_completo"] = imgHelper.generateDefaultImage(rowData.nome_img_completo, "Logo da Rede", "Logo da Rede", "nome-img-logo").outerHTML;
                }

                let actionView = btnHelper.generateLinkViewToDestination(`#/redes/view/${rowData.id}`, btnHelper.ICON_CONFIG, null, "Ver Detalhes/Configurar");
                let editView = btnHelper.generateLinkEditToDestination(`#/redes/edit/${rowData.id}`, null, "Editar");
                let deleteBtn = btnHelper.genericImgDangerButton(attributes, undefined, undefined, "delete-item", undefined);
                let changeStatus = btnHelper.generateImgChangeStatus(attributes, rowData.ativado, undefined, undefined, "change-status");

                let buttons = [actionView, editView, deleteBtn, changeStatus];
                let buttonsString = "";

                buttons.forEach(x => {
                    buttonsString += x.outerHTML + " ";
                });

                rowData["actions"] = buttonsString;
                return rowData;
            });
    },
    /**
     * Atualiza tabela de dados
     */
    refreshDataTable: function (evt) {
        'use strict';
        evt.preventDefault();
        if (typeof window['.manage-user #data-table'] !== 'undefined') {
            window['.manage-user #data-table'].clearPipeline().draw();
        }
    },

    //#endregion
};
