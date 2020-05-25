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
    networkSelectedItem: {},
    // Lista de usuários
    usersList: [],
    // Usuário
    userSelectedItem: {},
    // Pesquisar Por
    userOptionSelectedItem: {},
    //#endregion

    //#region Functions
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
            .off("change", "#correction-user-points-search-form #redes-select-list")
            .on("change", "#correction-user-points-search-form #redes-select-list", self.selectNetwork);
        $(document)
            .off("change", "#correction-user-points-search-form #user-options-list")
            .on("change", "#correction-user-points-search-form #user-options-list", self.selectFilterUser);

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

        let dataTable = "#import-sefaz-products-data #data-table";
        $(document).find("#import-sefaz-products-data #redes-nome").val(null);
        $(document).find("#import-sefaz-products-data #clientes-nome").val(null);

        if (products !== undefined && products !== null && products.length > 0) {
            $(document).find("#import-sefaz-products-data #btn-save").removeClass("disabled");
        } else {
            $(document).find("#import-sefaz-products-data #btn-save").addClass("disabled");
        }

        if ($.fn.DataTable.isDataTable($(dataTable))) {
            $(dataTable).DataTable().clear();
            $(dataTable).DataTable().destroy();
        }

        if (network !== undefined && network !== null) {
            self.networkSelectedItem = network;
            self.establishment = establishment;
            self.products = products;
            $(document).find("#import-sefaz-products-data #redes-nome").val(network.nome_rede);
            $(document).find("#import-sefaz-products-data #clientes-nome").val(establishment.nome_fantasia_municipio_estado);

            let btnHelper = new ButtonHelper(3, 5);
            let columns = [{
                    data: "id",
                    title: "Id",
                    orderable: true,
                    visible: false,
                },
                {
                    width: "40%",
                    data: "nomeParametro",
                    title: "Nome",
                    className: "text-center",
                    orderable: false,
                },
                {
                    data: "multiplicadorGota",
                    title: "Multiplicador",
                    className: "text-center",
                    orderable: true,
                    render: function (data) {
                        let value = parseFloat(data);

                        if (isNaN(value))
                            value = 0;

                        return value.toFixed(3);
                    }
                },
                {
                    data: "importar",
                    title: "Importar?",
                    className: "text-center",
                    orderable: true,
                    render: function (data) {
                        return data ? "Sim" : "Não";
                    }
                },
                {
                    data: "actions",
                    title: "Ações",
                    orderable: false,
                    render: function (data, item, row, meta) {
                        let attributes = {
                            id: row.id,
                            nome: row.nomeParametro
                        };

                        // 'Copia' as informações de attributes para attributesModal
                        let attributesModal = new Object();
                        Object.assign(attributesModal, attributes);

                        let changeStatusTitle = row.importar ? "Remover" : "Adicionar";
                        let editButton = btnHelper.generateSimpleButton(attributesModal, btnHelper.ICON_INFO, null, `Editar Multiplicador do Produto`, "fas fa-edit", "edit-item");
                        let addRemoveButton = btnHelper.generateAddRemovBtn(attributes, !row.importar, null, `${changeStatusTitle} o produto da importação`, "add-remove-item");

                        let buttons = [editButton, addRemoveButton];
                        let buttonsString = "";

                        buttons.forEach(x => buttonsString += x.outerHTML + " ");
                        return buttonsString;
                    }
                }
            ];

            let callback = function () {
                // Modifica o valor de multiplicador
                $(document)
                    .off("click", ".edit-item")
                    .on("click", ".edit-item", function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        let self = this;

                        // Obtem o registro clicado
                        let id = event.currentTarget.getAttribute('data-id');
                        let product = importSefazProducts.products.find(x => x.id === parseInt(id));

                        // Define as informações do registro selecionado
                        importSefazProducts.product = product;

                        // Chama modal e define os valores
                        bootbox.prompt({
                            title: `Produto: ${product.nomeParametro}`,
                            message: `<p>
                            Informe o multiplicador para o produto:
                            </p>`,
                            locale: "pt",
                            inputType: 'text',
                            callback: async function (multiplicadorGota) {
                                if (multiplicadorGota === null || multiplicadorGota === undefined) {
                                    return false;
                                }

                                product.multiplicadorGota = Number.parseFloat(multiplicadorGota);

                                // Altera registro na lista de dados
                                let index = importSefazProducts.products.indexOf(importSefazProducts.product);
                                importSefazProducts[index] = product;

                                // Atualiza a tabela no DataTable
                                let dataTable = "#import-sefaz-products-data #data-table";

                                // Atualiza a tabela
                                $(dataTable).DataTable().clear();
                                $(dataTable).DataTable().rows.add(importSefazProducts.products);
                                $(dataTable).DataTable().draw();

                                importSefazProducts.product = {};
                            },
                        });

                        $(".bootbox-input-text").MaskFloat({
                            max: 9999.999
                        }).val(product.multiplicadorGota.toFixed(3));

                        return self;
                    });

                // Define se importa a linha em questão
                $(document)
                    .off("click", ".add-remove-item")
                    .on("click", ".add-remove-item", function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        let self = this;

                        // troca status de importação na lista e reflete na tabela
                        let id = event.currentTarget.getAttribute('data-id');
                        let product = importSefazProducts.products.find(x => x.id === parseInt(id));
                        let indexProduct = importSefazProducts.products.indexOf(product);
                        product.importar = !product.importar;
                        importSefazProducts.products[indexProduct] = product;

                        // Valida se tem ao menos 1 item para importação e (des)habilita o botão
                        let count = importSefazProducts.products.filter(x => x.importar === true).length;

                        if (count > 0) {
                            $(document).find("#import-sefaz-products-data #btn-save").removeClass("disabled");
                        } else {
                            $(document).find("#import-sefaz-products-data #btn-save").addClass("disabled");
                        }

                        // Atualiza a tabela
                        $(dataTable).DataTable().clear();
                        $(dataTable).DataTable().rows.add(importSefazProducts.products);
                        $(dataTable).DataTable().draw();

                        return self;
                    });
            }

            generateDataTable(dataTable, columns, self.products, null, null, callback);
        } else {
            $(document).find("#qrcode-search-form #qr-code").val(null);
        }

        return self;
    },
    /**
     * Obtem dados de QR Code da SEFAZ
     *
     * @param {Event} event Evento de Click
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
     *
     * Método 'construtor'
     */
    init: function () {
        let self = this;
        document.title = "GOTAS - Correção de Pontos de Usuário";

        self.configureEvents();
        self.triggerEvents();

        self.fillNetworkSelectList("#correction-user-points-search-form #redes-select-list");

        return self;
    },
    /**
     * Grava os dados da tela.
     * Retorna mensagem em caso de erro
     *
     * @param {Event} event Evento de Click
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-21
     */
    save: async function (event) {
        let self = importSefazProducts;

        if ($(this).hasClass("disabled")) {
            event.preventDefault();
            return false;
        }

        // Obtem somente os registros à serem importados. transforma no padrão desejado
        let productsToSend = self.products
            .filter(x => x.importar === true)
            .map(function (row) {
                return new Object({
                    id: row.id,
                    nome_parametro: row.nomeParametro,
                    multiplicador_gota: row.multiplicadorGota
                });
            });

        // Raro desta situação acontecer, visto que desabilito o click. Mesmo assim, faz a validação
        if (productsToSend === undefined || productsToSend === null || productsToSend.length === 0) {
            toastr.error("É necessário enviar ao menos um produto!", "Erro!");

            return false;
        }

        try {
            let response = await gotasService.setGotasCliente(self.establishment.id, productsToSend);
            toastr.success(response.mensagem.message);

            // Gravação concluída, limpa os dados
            self.products = [];
            self.product = {};
            self.network = {};
            self.establishment = {};

            // Faz o refresh dos campos
            $(document).find("#import-sefaz-products-breadcrumb #btn-refresh").trigger("click");
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

    selectFilterUser: function (event) {
        let self = correctionUserPoints;
        console.log(this.value);

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
     * Dispara todos os eventos de todos os elementos da tela que são necessários
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-12
     */
    triggerEvents: function () {
        let self = this;

        $(document)
            .find("#correction-user-points-search-form #redes-select-list").trigger("change");

        $(document).find("#correction-user-points-search-form #user-options-list").trigger("change");

        return self;
    }
    //#endregion
}
