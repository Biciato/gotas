/**
 * Arquivo de funcionalidades do template src/Templates/Redes/index.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-04-22
 */

$(function () {
        'use strict';
        // #region index

        // #region Fields

        var redesSearchBtnForm = $(".redes-index #btn-search");
        var redesIndexDataTable = $(".redes-index #data-table");

        //#endregion

        // #region Functions

        function init() {
            redesSearchBtnForm.on("click", redesSearchBtnFormOnClick);

            redesSearchBtnForm.click();

            $(document).on("click", ".redes-index #data-table .delete-item", deleteNetworkOnClick);
            $(document).on("click", ".redes-index #data-table .change-status", changeStatusOnClick);
        };


        // #region Events

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
        async function changeStatusOnClick(event) {
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
        }

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
        async function deleteNetworkOnClick(event) {
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
                buttons: {
                    confirm: {
                        label: 'Ok',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'Cancelar',
                        className: 'btn-danger'
                    }
                },
                inputType: 'password',
                callback: async function (result) {
                    if (result === null || result === undefined) {
                        return false;
                    }

                    let response = await deleteRede(redesId, result);

                    if (response === undefined || response === null || !response) {
                        return false;
                    }

                    redesSearchBtnForm.click();
                }
            });
        }

        /**
         * Pesquisa dados e popula datatable
         *
         * @returns DataTables
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.2.3
         * @date 2020-04-22
         */
        async function redesSearchBtnFormOnClick() {
            let nomeRede = $(".redes-index #nome-rede");
            let ativado = $(".redes-index #ativado");
            let appPersonalizado = $(".redes-index #app-personalizado");
            let data = await getRedes(nomeRede.val(), ativado.val(), appPersonalizado.val());

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

            let dataSource = [];

            data.redes.forEach(rede => {
                rede.actions = [];

                let attributes = {
                    id: rede.id,
                    active: rede.ativado,
                    name: rede.nome_rede
                };

                let btnHelper = new ButtonHelper();
                let actionView = btnHelper.generateLinkViewToDestination("/redes/view/:id", btnHelper.ICON_INFO, null, "Ver Detalhes");
                let editView = btnHelper.generateLinkEditToDestination("/redes/edit/:id", null, "Editar");
                let deleteBtn = btnHelper.genericImgDangerButton(attributes, undefined, undefined, "delete-item", undefined);
                let changeStatus = btnHelper.generateImgChangeStatus(attributes, rede.ativado, undefined, undefined, "change-status");

                // Fazer botões de deletar, alternar estado

                rede.actions.push(actionView);
                rede.actions.push(editView);
                rede.actions.push(deleteBtn);
                rede.actions.push(changeStatus);

                dataSource.push(rede);
            });

            generateDataTable(redesIndexDataTable, columns, dataSource, undefined, null);
        }

        // //#endregion

        // #region Services

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
        function deleteRede(id, password) {
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

        async function changeStatusRede(id) {
            let url = "/api/redes/change-status/" + id;
            return await Promise.resolve($.ajax({
                type: "PUT",
                url: url,
                dataType: "JSON"
            }));
        }

        //#endregion

        //#endregion

        //#endregion

        // Chama a função init da tela
        init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
