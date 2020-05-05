/**
 * Arquivo de funcionalidades do template src/Templates/Redes/index.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-04-22
 */

var sammy = $.sammy.apps['#content-body'];

var rede = {
    // #region Functions
    init: function () {
        'use strict';
        var self = this;
        document.title = 'GOTAS - Redes';

        self.initDataTable();
        $(document).on("click", ".redes-index #btn-search", self.getRedes);

        $(document).on("click", ".redes-index #data-table .delete-item", self.deleteNetworkOnClick);
        $(document).on("click", ".redes-index #data-table .change-status", self.changeStatusOnClick);
        // $(".title-action #redes-new-btn-show").on("click", self.showRedesNewForm);
        $(".redes-add-form #redes-new-btn-cancel").on("click", self.showRedesIndex);
        $("#breadcrumb-item-redes-start").on("click", self.showRedesIndex);
        $("#breadcrumb-item-redes-start").click();

        return this;
    },
    /**
     * Atualiza tabela de dados
     */
    getRedes: function (evt) {
        'use strict';
        evt.preventDefault();
        if (typeof window['.redes-index #data-table'] !== 'undefined') {
            window['.redes-index #data-table'].clearPipeline().draw();
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
        let columns = [{
                data: "id",
                title: "Id",
                orderable: true,
                visible: false,
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
                var post = '';
                d.filtros = post;
                return d;
            },
            [5, 15, 20, 100],
            undefined,
            function (rowData) {

                let columnIndex = 2;
                let column = rowData[columnIndex];

                let attributes = {
                    id: rowData.id,
                    active: rowData.ativado,
                    name: rowData.nome_rede
                };

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
     *
     * @param {Event} evt Evento
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-22
     */
    addNetworkOnClick: async function (evt) {
        // @todo continuar
        evt.preventDefault();



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
                    let response = await deleteRede(redesId, result);

                    if (response === undefined || response === null || !response) {
                        return false;
                    }

                    redesSearchBtnForm.click();
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
                    let response = await changeStatusRede(redesId);

                    if (response === undefined || response === null || !response) {
                        return false;
                    }

                    redesSearchBtnForm.click();
                    dialogItSelf.close();
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
    // showRedesNewForm: function (evt) {
    //     event.preventDefault();
    //     $(".title-action #redes-new-btn-show").hide();
    //     $(".title-action #redes-new-action-btn-display").show();
    //     $(".title-action #redes-new-action-btn-save").show();

    //     $(".redes-index").fadeOut(100);
    //     $(".redes-add-form").fadeIn(500);
    // },
    showRedesIndex: function (evt) {
        var self = this;

        evt.preventDefault();

        $(".title-action #redes-new-btn-show").show();
        $(".redes-add-form").fadeOut(100);
        $(".redes-index").fadeIn(500);

        return this;
    },
    //#region Services
    /**
     * Altera o estado de uma rede
     *
     * @param {int} id Id da Rede
     * @returns Promise|false Promise ou status de false da operação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-28
     */
    changeStatusRede: async function (id) {
        let url = "/api/redes/change-status/" + id;
        return await Promise.resolve($.ajax({
            type: "PUT",
            url: url,
            dataType: "JSON"
        }));
    },

    /**
     * Remove uma rede
     *
     * @param {int} id Id da Rede
     * @returns Promise|false Promise ou status de false da operação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-28
     */
    deleteRede: function (id, password) {
        // @TODO conferir funcionamento
        if (id === undefined || id === null) {
            BootstrapDialog.warning("Necessário informar rede à ser apagada!");
            return false;
        }

        let url = "/api/redes/" + id;

        let dataRequest = {
            password: password
        }

        return Promise.resolve(
            $.ajax({
                type: "DELETE",
                data: dataRequest,
                url: url,
                dataType: "JSON",
            }));
    }
    //#endregion
    //#endregion
};

$(document).ready(function () {
        'use strict';

        rede.init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
$(function () {
        // #region index

        // #region Functions

        // #region Events


        async function changeStatusOnClick(event) {

        }

        async function redesSearchBtnFormOnClick() {

        }

        function showRedesIndex(event) {

        }

        // //#endregion

        // #region Services



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
        function getRedes(nomeRede, ativado, appPersonalizado) {
            var dataRequest = {
                nome_rede: nomeRede,
            };

            if (ativado !== undefined && ativado !== null) {
                dataRequest.ativado = ativado;
            }

            if (appPersonalizado !== undefined && appPersonalizado !== null) {
                dataRequest.app_personalizado = appPersonalizado;
            }

            return Promise.resolve(
                $.ajax({
                    type: "GET",
                    url: "/api/redes",
                    data: dataRequest,
                    dataType: "JSON"
                }));
        }



        //#endregion

        //#endregion

        //#endregion
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
