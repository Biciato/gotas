var importSefazProducts = {

    cliente: {},
    rede: {},
    produtos: [],

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
            .off("keydown", "#qrcode-search-form #qr-code")
            .on("keydown", "#qrcode-search-form #qr-code", function (event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    $(document)
                        .find("#qrcode-search-form #btn-search").trigger('click');
                }
            });

        $(document)
            .off("click", "#qrcode-search-form #btn-search")
            .on("click", "#qrcode-search-form #btn-search", self.getQRCodeProducts);

        // configura o comportamento de modal
        $(document)
            .off("blur", "#modal-edit-product #multiplicador-gota")
            .off("keydown", "#modal-edit-product #multiplicador-gota")
            .off("keyup", "#modal-edit-product #multiplicador-gota")
            .on("blur", "#modal-edit-product #multiplicador-gota", function (event) {
                let value = parseFloat(this.value);

                if (Number.isNaN(value))
                    value = 0;

                value = value.toFixed(3);

                if (value > 9999.999)
                    value = 9999.999;

                this.value = value;
            })
            .on("keydown", "#modal-edit-product #multiplicador-gota", function (event) {
                let value = this.value;

                if (value !== undefined && value !== null) {
                    value = value.replace(/\D/g, "");
                    value = parseFloat(value) / 1000;

                    if (value > 9999.999)
                        value = 9999.999;

                    this.value = value.toFixed(3);
                }

                if (event.keyCode === 13) {
                    $(document).find("#modal-edit-product #confirm").trigger("click");
                }
            })
            .on("keyup", "#modal-edit-product #multiplicador-gota", function (event) {
                let value = this.value;

                if (value !== undefined && value !== null) {
                    value = value.replace(/\D/g, "");
                    value = parseFloat(value) / 1000;

                    if (value > 9999.999)
                        value = 9999.999;

                    this.value = value.toFixed(3);
                }
            });

        $(document)
            .off("click", "#modal-edit-product #confirm")
            .on("click", "#modal-edit-product #confirm", function (event) {

                let multiplicador = $(document).find("#modal-edit-product #multiplicador-gota").val();

                console.log(multiplicador);

            });
    },
    fillData: function (rede, cliente, produtos) {
        var self = this;

        self.cliente = cliente;
        self.rede = rede;
        self.produtos = produtos;
        console.log(self.produtos);
        $(document).find("#import-sefaz-products-data #redes-nome").val(rede.nome_rede);
        $(document).find("#import-sefaz-products-data #clientes-nome").val(cliente.nome_fantasia_municipio_estado);

        // #import-sefaz-products-data #data-table

        // id
        // nome
        // multiplicadorGota
        // importar

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

                    let attributesModal = attributes;
                    attributesModal.toggle = "modal";
                    attributesModal.target = "#modal-edit-product";

                    let changeStatusTitle = row.importar ? "Remover" : "Adicionar";
                    let editButton = btnHelper.generateSimpleButton(attributes, btnHelper.ICON_INFO, null, `Editar Multiplicador do Produto`, "fas fa-edit", "edit-item");
                    let addRemoveButton = btnHelper.generateAddRemovBtn(attributes, !row.importar, null, `${changeStatusTitle} o produto da importação`, "add-remove-item");

                    let buttons = [editButton, addRemoveButton];
                    let buttonsString = "";

                    buttons.forEach(x => buttonsString += x.outerHTML + " ");
                    return buttonsString;
                }
            }
        ];

        let dataTable = "#import-sefaz-products-data #data-table";

        let callback = function () {
            // Modifica o valor de multiplicador
            $(document)
                .off("click", ".edit-item")
                .on("click", ".edit-item", function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    let self = this;
                    console.log('aaa');

                    // Obtem o registro clicado
                    let id = event.currentTarget.getAttribute('data-id');
                    let product = importSefazProducts.produtos.find(x => x.id = id);

                    // Define as informações do registro selecionado
                    importSefazProducts.produto = product;

                    // Define os Valores do modal
                    // Titulo
                    $(document).find("#modal-edit-product #nome-parametro").text(product.nomeParametro);
                    // Valor
                    $(document).find("#modal-edit-product #multiplicador-gota").val(parseFloat(product.multiplicadorGota).toFixed(3));

                    return self;
                });

            // Define se importa a linha em questão
            $(document)
                .off("click", ".add-remove-item")
                .on("click", ".add-remove-item", function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    let self = this;

                    console.log('aaa');
                    // troca status de importação na lista e reflete na tabela
                    let id = event.currentTarget.getAttribute('data-id');
                    let product = importSefazProducts.produtos.find(x => x.id = id);
                    let indexProduct = importSefazProducts.produtos.indexOf(product);
                    product.importar = !product.importar;
                    importSefazProducts.produtos[indexProduct] = product;

                    // Atualiza a tabela
                    $(dataTable).DataTable().clear();
                    $(dataTable).DataTable().rows.add(importSefazProducts.produtos);
                    $(dataTable).DataTable().draw();

                    return self;
                });
        }

        generateDataTable(dataTable, columns, self.produtos, null, null, callback);

        return self;
    },
    getQRCodeProducts: async function (event) {
        let self = this;
        let qrCode = $("#qrcode-search-form #qr-code").val();
        console.log(qrCode);

        try {
            let response = await sefazService.getDetailsQRCode(qrCode);

            importSefazProducts.fillData(response.data.rede, response.data.cliente, response.data.sefaz.produtos.itens);
        } catch (error) {
            console.log(error);
            if (response === undefined || response === null || !response) {
                toastr.error("Erro na obtenção de dados da SEFAZ. Tente mais tarde", "Erro");
            } else if (!response.mensagem.status) {
                toastr.error(response.mensagem.errors.join(" "), response.mensagem.message);
            } else {
                toastr.error(error);
            }

            return false;
        }

        return self;
    },
    /**
     *
     * Método 'construtor'
     */
    init: function (produtos = undefined) {
        let self = this;
        document.title = "GOTAS - Importação de Produtos da SEFAZ";

        self.configureEvents();

        $(document).find("#qrcode-search-form #qr-code").focus();

        if (produtos !== undefined && produtos !== null) {
            self.produtos = produtos;
        }

        return self;
    }
}
