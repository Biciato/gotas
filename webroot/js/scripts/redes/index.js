/**
 * Arquivo de funcionalidades do template src/Templates/Redes/index.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-04-22
 */

var redesIndex = {
    // #region Functions

    /**
     * Evento de alterar estado da rede e suas unidades
     *
     * @param {any} event Evento
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-28
     */
    changeStatusOnClick: async function (event) {
        event.preventDefault();
        let redesId = event.target.getAttribute('data-id');
        let redesNome = event.target.getAttribute('data-name');
        let redesStatus = event.target.getAttribute('data-status');
        let question = "Deseja :param a rede :network e suas unidades?"
            .replace(":param", redesStatus ? "ativar" : "desativar")
            .replace(":network", redesNome);

        let buttons = [{
                label: "Cancelar",
                action: ((dialogItSelf) => dialogItSelf.close())
            },
            {
                label: "OK",
                action: async function (dialogItSelf) {
                    try {
                        let response = await redesService.changeStatus(redesId);

                        if (response === undefined || response === null || !response) {
                            return false;
                        }

                        $(".redes-index #btn-search").click();
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
     * Evento de alterar estado da rede e suas unidades
     *
     * @param {any} event Evento
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-28
     */
    deleteNetworkOnClick: async function (evt) {
        var self = this;
        event.preventDefault();

        let redesId = event.target.getAttribute('data-id');
        let redesNome = event.target.getAttribute('data-name');
        let question = "Deseja apagar a rede :network e suas unidades?"
            .replace(":network", redesNome);

        bootbox.prompt({
            title: question,
            message: `<p>
                    Confirme sua senha para continuar
                </p>`,
            locale: "pt",
            inputType: 'password',
            callback: async function (result) {
                if (result === null || result === undefined) {
                    return false;
                }
                try {
                    let response = await redesService.delete(redesId, result);

                    if (response === undefined || response === null || !response) {
                        return false;
                    }

                    toastr.success(response.mensagem.message);
                    $(".redes-index #btn-search").click();
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
     * 'Constructor'
     */
    init: function () {
        'use strict';
        var self = this;
        document.title = 'GOTAS - Redes';

        self.initDataTable();

        // Adiciona enter dentro do form, pesquisar
        $(document)
            .off("keydown", "#form")
            .on("keydown", "#form", function (evt) {
                if (evt.keyCode == 13) {
                    evt.preventDefault();
                    self.refreshDataTable(evt);
                    return false;
                }
            });

        $(document)
            .off("click", ".redes-index #btn-search")
            .on("click", ".redes-index #btn-search", self.refreshDataTable);

        $(document)
            .off("click", ".redes-index #data-table .delete-item")
            .on("click", ".redes-index #data-table .delete-item", self.deleteNetworkOnClick);
        $(document)
            .off("click", ".redes-index #data-table .change-status")
            .on("click", ".redes-index #data-table .change-status", self.changeStatusOnClick);

        return self;
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
            ".redes-index #data-table",
            columns,
            '/api/redes',
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
        if (typeof window['.redes-index #data-table'] !== 'undefined') {
            window['.redes-index #data-table'].clearPipeline().draw();
        }
    },

    //#endregion
};
