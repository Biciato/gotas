/**
 * Arquivo de funcionalidades do template webroot\view\admin\import-sefaz-products\index.tpl
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-19
 */
var correctionUserPoints = {
    //#region Variables
    // Redes data list
    networks: [],
    // Rede
    networkSelectedItem: undefined,
    // Pontos à enviar do usuário
    pointsToSend: 0.000,
    // Lista de usuários
    usersList: [],
    // Usuário
    userSelectedItem: undefined,
    // Pesquisar Por
    userOptionSelectedItem: {},

    vehicle: {},
    //#endregion

    //#region Functions

    /**
     * Altera a máscara do campo de usuário conforme filtro selecionado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-12
     */
    changeMaskUserFilter: function () {
        let self = this;

        // Reseta as definições de digitação do campo de pesquisa de usuário
        $(document)
            .find("#correction-user-points-search-form #user-input-search")
            .unbind()
            // .removeData()
            .removeAttr("maxlength");

        $(document)
            .off("blur", "#correction-user-points-search-form #user-input-search")
            .off("focus", "#correction-user-points-search-form #user-input-search")
            .off("keydown", "#correction-user-points-search-form #user-input-search")
            .off("keyup", "#correction-user-points-search-form #user-input-search")
            .on("keyup", "#correction-user-points-search-form #user-input-search", function (event) {
                if (this.value.length === 0) {
                    $(document).find("#correction-user-points-search-form #btn-search").addClass("disabled");
                } else {
                    $(document).find("#correction-user-points-search-form #btn-search").removeClass("disabled");
                }

                if (event.keyCode === 13) {
                    event.preventDefault();
                    // se enter, dispara o botão pesquisar
                    $(document).find("#correction-user-points-search-form #btn-search").trigger("click");
                }
            })
            // remove a mascara de placa
            .unmask();

        if (self.userOptionSelectedItem !== undefined && self.userOptionSelectedItem !== null) {
            // para máscara de telefone e CPF, permite somente a inserção de números no campo.
            if (self.userOptionSelectedItem === "telefone") {
                $(document).find("#correction-user-points-search-form #user-input-search").MaskTelephone({
                    maxlength: 11
                });
            } else if (self.userOptionSelectedItem === "cpf") {
                $(document).find("#correction-user-points-search-form #user-input-search").MaskCPF();
            } else if (self.userOptionSelectedItem === "placa") {
                // No caso de placa, por enquanto é melhor utilizar o próprio jquery.mask, visto que atende bem
                $(document).find("#correction-user-points-search-form #user-input-search").mask("AAA9B99", {
                    'translation': {
                        A: {
                            pattern: /[A-Za-z]/
                        },
                        9: {
                            pattern: /[0-9]/
                        },
                        B: {
                            pattern: /\D*/
                        }
                    },
                    onKeyPress: function (value, event) {
                        event.currentTarget.value = value.toUpperCase();
                    }
                });
            }
        }

        return self;
    },
    /**
     * Realiza configuração de eventos dos campos da tela
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-12
     */
    configureEvents: function () {
        var self = this;

        $(document)
            .off("change", "#correction-user-points-search-form #network-select-list")
            .on("change", "#correction-user-points-search-form #network-select-list", self.selectNetwork);
        $(document)
            .off("change", "#correction-user-points-search-form #users-select-list")
            .on("change", "#correction-user-points-search-form #users-select-list", self.selectUserFilter);
        $(document)
            .off("click", "#correction-user-points-search-form #btn-search")
            .on("click", "#correction-user-points-search-form #btn-search", self.searchUser);

        $(document)
            .off("click", "#correction-user-points #btn-refresh")
            .on("click", "#correction-user-points #btn-refresh", self.resetWindow);

        $(document)
            .off("keydown", "#correction-user-points-search-form #user-region #user-points-send")
            .on("keydown", "#correction-user-points-search-form #user-region #user-points-send", function (event) {
                if (event.keyCode === 13) {
                    $(document).find("#correction-user-points-search-form #user-region #btn-save").trigger("click");
                }
            })
            .off("keyup", "#correction-user-points-search-form #user-region #user-points-send")
            .on("keyup", "#correction-user-points-search-form #user-region #user-points-send", function (event) {
                let self = correctionUserPoints;
                if (parseFloat(this.value) === 0) {
                    $(document).find("#correction-user-points-search-form #user-region #btn-save").addClass("disabled");
                } else if (self.networkSelectedItem !== undefined && self.userSelectedItem !== undefined && this.value !== 0) {
                    $(document).find("#correction-user-points-search-form #user-region #btn-save").removeClass("disabled");
                }

                self.pointsToSend = parseFloat(this.value);

                return self;
            });
        $(document).find("#correction-user-points-search-form #user-region #user-points-send")
            .MaskFloat({
                max: 9999,
                allowNegative: true,
                decimals: 0,
                separator: "."
            });

        $(document)
            .off("click", "#correction-user-points-search-form #user-region #btn-save")
            .on("click", "#correction-user-points-search-form #user-region #btn-save", self.save);

        // --------------------------
        // Atenção: O método correctionUserPoints.changeMaskUserFilter provêm recursos dinâmicos à esta tela
        // --------------------------

        return self;
    },
    /**
     * Preenche dados da tela com dados obtidos na SEFAZ
     *
     * @param {Redes} rede Dados de Rede
     * @param {Clientes} cliente Dados de Cliente
     * @param {Produtos} produtos Dados de Produtos da Sefaz
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-21
     */
    fillData: function (users, vehicle = undefined) {
        var self = this;

        let btnHelper = new ButtonHelper(3, 5);

        let columns = [{
                data: "id",
                name: "Id",
                orderable: true,
                visible: false,
            },
            {
                data: "nome",
                title: "Nome",
                orderable: true,
            },
            {
                data: "telefone",
                title: "Telefone",
                orderable: true,
                render: function (value) {
                    if (value === undefined || value === null)
                        return "";

                    value = value.replace(/\D/g, "");

                    let mask = {
                        areaCode: 2,
                        prefix: value.length === 11 ? 5 : 4,
                        suffix: 4
                    };

                    let replace = `(\\d{${mask.areaCode}})(\\d{${mask.prefix}})(\\d{${mask.suffix}})`;
                    let regex = new RegExp(replace, "g");

                    // Define máscara ao perder o foco e define limite de caracteres na string de retorno
                    return value.replace(regex, "($1)$2-$3").substr(0, mask.areaCode + mask.prefix + mask.suffix + 3);
                }
            },
            {
                data: "data_nasc",
                title: "Data Nasc.",
                orderable: true,
                render: function (value) {
                    return value === undefined || value === null || value.length === 0 ? "" : moment(value, "YYYY-MM-DD").format("DD/MM/YYYY");
                }
            },
            {
                data: "acoes",
                title: "Ações",
                orderable: false,
                render: function (data, item, row, meta) {
                    let attributes = {
                        id: row.id,
                        nome: row.nome
                    };

                    // 'Copia' as informações de attributes para attributesModal
                    let attributesModal = new Object();
                    Object.assign(attributesModal, attributes);

                    let selectButton = btnHelper.generateAddRemovBtn(attributes, !row.importar, null, "Selecionar", "select-item");

                    let buttons = [selectButton];
                    let buttonsString = "";
                    buttons.forEach(x => buttonsString += x.outerHTML + " ");

                    return buttonsString;
                }
            }
        ];

        let dataTable = "#correction-user-points-search-form #data-table";

        if ($.fn.DataTable.isDataTable(dataTable)) {
            $(dataTable).DataTable().clear();
            $(dataTable).DataTable().destroy();
        }

        let callback = function () {
            // Modifica o valor de multiplicador
            $(document)
                .off("click", ".select-item")
                .on("click", ".select-item", async function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    let self = correctionUserPoints;

                    // Obtem o registro clicado
                    let id = event.currentTarget.getAttribute('data-id');
                    let user = correctionUserPoints.users.find(x => x.id === parseInt(id));

                    // Define as informações do registro selecionado
                    correctionUserPoints.userSelectedItem = user;
                    $(document).find("#correction-user-points-search-form #user-region #user-name").val(user.nome);

                    try {
                        await correctionUserPoints.getUserPoints(correctionUserPoints.networkSelectedItem.id, correctionUserPoints.userSelectedItem.id);

                        // Esconde a tabela de usuários e veículo encontrados
                        $(document).find("#correction-user-points-search-form #users-table-region").hide();
                        $(document).find("#correction-user-points-search-form #vehicle-region").hide();
                    } catch (error) {
                        // Este catch não precisa fazer nada, já foi tudo encapsulado no throw anterior.
                        // Mas irá garantir que a tabela não se oculte em caso de erro
                        console.log(error);
                    }

                    return self;
                });
        }

        generateDataTable(dataTable, columns, users, null, null, callback);

        $(document).find("#correction-user-points-search-form #vehicle-region #veiculo-placa").val(vehicle === undefined ? "" : vehicle.placa);
        $(document).find("#correction-user-points-search-form #vehicle-region #veiculo-modelo").val(vehicle === undefined ? "" : vehicle.modelo);
        $(document).find("#correction-user-points-search-form #vehicle-region #veiculo-fabricante").val(vehicle === undefined ? "" : vehicle.fabricante);
        $(document).find("#correction-user-points-search-form #vehicle-region #veiculo-ano").val(vehicle === undefined ? "" : vehicle.ano);

        $(document).find("#correction-user-points-search-form #vehicle-region").hide();

        if (vehicle !== undefined) {
            $(document).find("#correction-user-points-search-form #vehicle-region").show();
        }

        $(document).find("#users-table-region").hide();

        if (users !== undefined && users.length > 0) {
            $(document).find("#users-table-region").show();
        }

        return self;
    },
    /**
     * Preenche o select de Redes
     *
     * @param {HTMLElement} element Selector do elemento à ser preenchido
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-22
     */
    fillNetworkSelectList: async function (element) {
        let self = this;

        try {
            var response = await redesService.getList();
            if (response === undefined || response === null || !response) {
                return false;
            }

            self.networks = response;

            $(document).find(element).empty();
            $(document).find(element).append(new Option(`<Selecionar>`, null));

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
     * Obtem dados de QR Code da SEFAZ
     *
     * @param {OnClick} event Evento de Click
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-21
     */
    getQRCodeProducts: async function (event) {
        let self = this;
        let qrCode = $("#qrcode-search-form #qr-code").val();

        try {
            let response = await sefazService.getDetailsQRCode(qrCode);

            importSefazProducts.fillData(response.data.rede, response.data.cliente, response.data.sefaz.produtos.itens);
        } catch (error) {
            console.log(error);
            if (error === undefined || error === null || !error) {
                toastr.error("Erro na obtenção de dados da SEFAZ. Tente mais tarde", "Erro");
            } else if (!error.responseJSON.mensagem.status) {
                toastr.error(error.responseJSON.mensagem.errors.join(" "), error.responseJSON.mensagem.message);
            } else {
                toastr.error(error);
            }

            return false;
        }

        return self;
    },
    /**
     * Chama service que obtem pontos de usuário
     *
     * @param {Integer} networkId Id de Rede
     * @param {Integer} userId Id de usuário
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-26
     */
    getUserPoints: async function (networkId, userId) {
        let self = this;

        try {
            let response = await pontuacoesService.getUserPoints(networkId, userId);

            $("#correction-user-points-search-form #user-region #user-balance").val(response !== undefined ? response.saldo : 0);

            toastr.success("Dados carregados com sucesso!");
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
            throw msg;
        }

        return self;
    },

    /**
     *
     * Método 'construtor'
     */
    init: function () {
        let self = this;
        document.title = "GOTAS - Correção de Pontos de Usuário";

        self.configureEvents();
        self.triggerEvents();

        self.fillNetworkSelectList("#correction-user-points-search-form #network-select-list");

        return self;
    },
    /**
     * Reseta janela
     * Reseta todos os campos da janela
     *
     * @param {OnClick} event Evento On Click
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-26
     */
    resetWindow: function (event) {
        var self = correctionUserPoints;

        // reseta as variaveis
        self.networkSelectedItem = undefined;
        self.userSelectedItem = undefined;
        self.users = [];
        self.pointsToSend = 0;

        // reseta os campos na tela

        $("#correction-user-points-search-form #network-select-list").val(null).trigger('change');
        $("#correction-user-points-search-form #users-select-list").val("telefone").trigger('change');
        $("#correction-user-points-search-form #user-input-search").val(null).trigger('keyup');
        $("#correction-user-points-search-form #user-region #user-name").val(null);
        $("#correction-user-points-search-form #user-region #user-balance").val(null);
        $("#correction-user-points-search-form #user-region #user-points-send").val(0).trigger('keyup');

        return self;
    },

    /**
     * Grava os dados da tela.
     * Retorna mensagem em caso de erro
     *
     * @param {OnClick} event Evento de Click
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-26
     */
    save: async function (event) {
        let self = correctionUserPoints;

        if ($(this).hasClass("disabled")) {
            event.preventDefault();
            return false;
        }

        let hasErrors = self.networkSelectedItem === undefined || self.userSelectedItem === undefined || self.pointsToSend === 0;

        if (self.networkSelectedItem === undefined) {
            toastr.error("É necessário selecionar uma rede!", "Erro!");
        }

        if (self.userSelectedItem === undefined) {
            toastr.error("É necessário selecionar um usuário!", "Erro!");
        }

        if (self.pointsToSend === 0) {
            toastr.error("É necessário informar a quantidade de pontos à ser ajustado!", "Erro!");
        }

        if (hasErrors)
            return;

        try {
            let response = await pontuacoesComprovantesService.setPointsUserManual(self.networkSelectedItem.id, self.userSelectedItem.id, self.pointsToSend);
            toastr.success(response.mensagem.message);

            // Gravação concluída, limpa os dados
            $(document).find("#correction-user-points #btn-refresh").trigger("click")
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

        return this;
    },
    /**
     * Realiza pesquisa de usuário
     *
     * @param {OnClick} event Evento de click
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-22
     */
    searchUser: async function (event) {
        event.preventDefault();

        // Ao pesquisar, desabilita botão de gravar
        $(document).find("#correction-user-points-search-form #btn-save").addClass("disabled");
        // Limpa campo de quantidade de pontos e nome de usuário selecionado
        $(document).find("#correction-user-points-search-form #user-name").val(null);
        $(document).find("#correction-user-points-search-form #user-balance").val(null);
        $(document).find("#correction-user-points-search-form #user-points-send").val(0);

        let self = correctionUserPoints;
        self.users = [];
        self.vehicle = undefined;
        self.userSelectedItem = undefined;

        let nome = self.userOptionSelectedItem === "nome" ? $("#correction-user-points-search-form #user-input-search").val() : undefined;
        let cpf = self.userOptionSelectedItem === "cpf" ? $("#correction-user-points-search-form #user-input-search").val() : undefined;
        let telefone = self.userOptionSelectedItem === "telefone" ? $("#correction-user-points-search-form #user-input-search").val() : undefined;
        let placa = self.userOptionSelectedItem === "placa" ? $("#correction-user-points-search-form #user-input-search").val() : undefined;

        // Faz a pesquisa conforme a opção selecionada
        try {
            if (self.userOptionSelectedItem === "placa") {
                self.vehicle = await veiculosService.getUsuariosByVeiculo(placa);

                self.users = self.vehicle.usuarios_has_veiculos.map(function (data) {
                    return data.usuario;
                });
            } else {
                self.users = await usuariosService.getUsuariosFinais(nome, cpf, telefone);
            }

            // Preenche elementos de exibição
            self.fillData(self.users, self.vehicle);
            toastr.success("Dados carregados com sucesso!");
        } catch (error) {
            console.log(error);
            var msg = {};

            self.users = [];
            self.vehicle = undefined;
            self.fillData(self.users, self.vehicle);

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
     * Seleciona rede ao selecionar no list box
     *
     * @param {Event} event OnChange Event
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-22
     */
    selectNetwork: function (event) {
        let self = correctionUserPoints;
        let value = Number.parseInt(this.value);

        self.networkSelectedItem = {};

        if (!Number.isNaN(value)) {
            // localiza a rede
            self.networkSelectedItem = self.networks.find(x => x.id === value);
        }

        return self;
    },

    /**
     * Altera o tipo de pesquisa de usuário
     *
     * @param {OnChange} event onChange Event
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-26
     */
    selectUserFilter: function (event) {
        let self = correctionUserPoints;

        self.userOptionSelectedItem = this.value;
        $("#correction-user-points-search-form #user-input-search").val(null);

        self.changeMaskUserFilter();

        return self;
    },

    /**
     * Dispara todos os eventos de todos os elementos da tela que são necessários
     *
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-25
     */
    triggerEvents: function () {
        let self = this;

        $(document)
            .find("#correction-user-points-search-form #network-select-list").trigger("change");

        $(document).find("#correction-user-points-search-form #users-select-list").trigger("change");
        $(document).find("#correction-user-points-search-form #user-input-search").trigger("keyup");

        return self;
    }
    //#endregion
}
