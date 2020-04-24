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
        };


        // #region Events

        /**
         *
         *
         */
        async function redesSearchBtnFormOnClick() {
            let nomeRede = $(".redes-index #nome-rede");
            let ativado = $(".redes-index #ativado");
            let appPersonalizado = $(".redes-index #app-personalizado");
            let data = await getRedes(nomeRede.val(), ativado.val(), appPersonalizado.val());
            console.log(data);

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

            let func = function () {
                console.log('function called');
            }

            let dataSource = [];

            data.redes.forEach(rede => {
                rede.actions = [];

                let dataTableActionButton = new DataTableActionButton();
                let actionView = dataTableActionButton.generateLinkViewToDestination("/redes/view/:id", dataTableActionButton.ICON_INFO, null, "Ver Detalhes");
                let editView = dataTableActionButton.generateLinkEditToDestination("/redes/edit/:id", null, "Editar");
                // Fazer botões de deletar, alternar estado

                rede.actions.push(actionView);
                rede.actions.push(editView);

                dataSource.push(rede);
            });

            generateDataTable(redesIndexDataTable, columns, dataSource, undefined, null, func);
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

        // Chama a função init da tela
        init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
