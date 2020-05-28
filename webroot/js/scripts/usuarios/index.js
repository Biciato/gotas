/**
 * Arquivo de funcionalidades do template src/Templates/Usuarios/index.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-25-5
 */

const usuariosIndex = {
    /**
     * 'Constructor'
     */
    init: function () {
        'use strict';
        var self = this;
        document.title = 'GOTAS - Usuarios';

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
            .off("click", ".usuarios-index #btn-search")
            .on("click", ".usuarios-index #btn-search", self.refreshDataTable);

        $(document)
            .off("click", ".usuarios-index #data-table .delete-item")
            .on("click", ".usuarios-index #data-table .delete-item", self.deleteNetworkOnClick);
        $(document)
            .off("click", ".usuarios-index #data-table .change-status")
            .on("click", ".usuarios-index #data-table .change-status", self.changeStatusOnClick);

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
                data: "tipo_perfil",
                width: "10%",
                title: "Tipo Perfil",
                className: "text-center",
                orderable: false,
            },
            {
                data: "nome",
                title: "Nome",
                orderable: true,
            },
            {
                data: "cpf",
                title: "CPF",
                orderable: false,
            },
            {
                data: "email",
                title: "E-mail",
                orderable: false,
            },
            {
                data: "actions",
                title: "Ações",
                orderable: false,
            }
        ];

        initPipelinedDT(
            ".usuarios-index #data-table",
            columns,
            '/api/usuarios',
            undefined,
            function (d) {
                var filters = {};

                let tipoPerfil = $("#form #tipo_perfil").val();
                let nome = $("#form #nome").val();
                let email = $("#form #email").val();
                let cpf = $("#form #cpf").val();

                if (tipoPerfil !== undefined && tipoPerfil !== null) {
                    filters.tipo_perfil = nomeRede;
                }

                if (nome !== undefined && nome !== null) {
                    filters.nome = nome;
                }

                if (email !== undefined && email !== null) {
                    filters.email = email;
                }

                if (cpf !== undefined && cpf !== null) {
                    filters.cpf = cpf;
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

                let actionView = btnHelper.generateLinkViewToDestination(`#/usuarios/view/${rowData.id}`, btnHelper.ICON_CONFIG, null, "Ver Detalhes/Configurar");
                let editView = btnHelper.generateLinkEditToDestination(`#/usuarios/edit/${rowData.id}`, null, "Editar");
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
