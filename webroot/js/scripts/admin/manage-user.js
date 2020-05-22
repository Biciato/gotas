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
     * Realiza configuração de eventos dos campos da tela
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-12
     */
    configureEvents: function () {
        'use strict';
        var self = this;

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

        $(document)
            .off("click", ".manage-user #data-table .manage-user-btn")
            .on("click", ".manage-user #data-table .manage-user-btn", self.manageUser);
        return self;
    },
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

        self.configureEvents();

        self.triggerEvents();

        return self;
    },
    /**
     * Preenche o select box de Lista de Redes
     *
     * @param {HTMLElement} element elemento à ser preenchido
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-18
     */
    fillRedesSelectList: async function (element) {
        try {
            var response = await redesService.getList();
            if (response === undefined || response === null || !response) {
                return false;
            }

            $(document).find(element).empty();
            $(document).find(element).append(new Option(`<Todos>`, null));

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
    /**
     * Preenche o select box de Tipos de Perfis
     *
     * @param {HTMLElement} element elemento à ser preenchido
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-18
     */
    fillTipoPerfisSelectList: async function (element) {
        try {
            var response = await usuariosService.getPerfisList();
            if (response === undefined || response === null || !response) {
                return false;
            }

            $(document).find(element).empty();
            $(document).find(element).append(new Option(`<Todos>`, null));

            // @TODO Arrumar essa gambiarra depois ¬¬
            let keys = Object.keys(response.filter);
            keys.forEach(key => {
                $(document).find(element).append(new Option(response.filter[key], key));
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
                data: "foto_perfil_completo",
                width: "10%",
                title: "",
                className: "text-center",
                orderable: false,
            },
            {
                data: "nome",
                title: "Nome",
                orderable: true,
            },
            {
                data: "email",
                title: "E-mail",
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

                let nome = $("#manage-user-form #nome").val();
                let email = $("#manage-user-form #email").val();
                let tipoPerfil = $("#tipo-perfis-select-list").val();
                let rede = $("#redes-select-list").val();

                if (nome !== undefined && nome !== null) {
                    filters.nome = nome;
                }

                if (email !== undefined && email !== null) {
                    filters.email = email;
                }

                filters.tipo_perfil = tipoPerfil === "null" ? null : tipoPerfil;
                filters.redes_id = rede === "null" ? null : rede;
                filters.tipo_perfil_max = PROFILE_TYPE_MANAGER;
                filters.tipo_perfil_min = PROFILE_TYPE_ADMIN_NETWORK;

                d.filtros = filters;

                return d;
            },
            [5, 15, 20, 100],
            undefined,
            function (rowData) {
                let attributes = {
                    id: rowData.id,
                    active: rowData.ativado,
                    name: rowData.nome
                };

                if (rowData.foto_perfil_completo !== undefined && rowData.foto_perfil_completo !== null) {
                    rowData["foto_perfil_completo"] = imgHelper.generateDefaultImage(rowData.foto_perfil_completo, "Foto de perfil", "Foto de perfil", "foto-perfil-logo").outerHTML;
                }

                let manageBtn = btnHelper.generateSimpleButton(attributes, btnHelper.ICON_DELETE_V4, "Gerenciar", "Gerenciar", btnHelper.ICON_CONFIG, "manage-user-btn");

                let buttons = [manageBtn];
                let buttonsString = "";

                buttons.forEach(x => {
                    buttonsString += x.outerHTML + " ";
                });

                rowData["actions"] = buttonsString;
                return rowData;
            });
    },

    /**
     * Questiona se deseja iniciar gerenciamento do usuário selecionado
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-18
     */
    manageUser: function (event) {
        event.preventDefault();
        let userId = $(this).attr('data-id');
        let userName = $(this).attr('data-name');
        let question = "Deseja gerenciar o usuário :userName?"
            .replace(":userName", userName);

        let buttons = [{
                label: "Cancelar",
                action: ((dialogItSelf) => dialogItSelf.close())
            },
            {
                label: "OK",
                action: async function (dialogItSelf) {
                    try {
                        let response = await usuariosService.startManageUser(userId);

                        if (response === undefined || response === null || !response) {
                            return false;
                        }
                        dialogItSelf.close();
                        window.location.href = "#/";
                        window.location.reload();
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
     * Atualiza tabela de dados
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-18
     */
    refreshDataTable: function (evt) {
        'use strict';
        evt.preventDefault();
        if (typeof window['.manage-user #data-table'] !== 'undefined') {
            window['.manage-user #data-table'].clearPipeline().draw();
        }
    },

    /**
     * Dispara todos os eventos necessários à tela
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-18
     */
    triggerEvents: function () {
        var self = this;
        $(document)
            .find("#manage-user-form #btn-search").trigger('click');

        return self;
    }

    //#endregion
};
