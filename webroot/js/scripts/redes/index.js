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
        };

        /**
         *
         * @param {Object} element Element
         * @param {array} columns Columns array
         * @param {array} data Json data
         * @param {array} lengthMenu Máximo de resultados
         */
        function generateDataTable(element, columns, data, lengthMenu) {
            if ($.fn.DataTable.isDataTable(element)) {
                element.DataTable().clear();
                element.DataTable().destroy();
            }

            if (lengthMenu === undefined) {
                lengthMenu = [10, 25, 50, 100];
            }

            /**
             * No array de data, é esperado uma coluna 'actions'. Se esta coluna não existe, adiciona-a vazia
             */
            let dataRows = [];

            data.forEach(item => {
                if (item.actions === undefined) {
                    item.actions = [];
                }

                dataRows.push(item);
            })

            element.DataTable({
                language: {
                    "url": "/webroot/js/DataTables/i18n/dataTables.pt-BR.lang"
                },
                columns: columns,
                lengthMenu: lengthMenu,
                data: dataRows
            });
        }

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

            generateDataTable(redesIndexDataTable, columns, data.redes);
        };

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
