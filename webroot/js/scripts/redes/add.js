/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\redes\add.js
 * @since 1.2.3
 * @date 2020-05-05
 *
 */
var redesAdd = {
    /**
     * Altera estado de habilitado para Checkbox de "Mensagem de Distância ao Comprar"
     *
     * @param {Event} e Evento click
     * @return this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-06
     */
    changeEnabledAppPersonalizado: function (e) {
        'use strict';
        let self = this;
        var checked = $("#app-personalizado").prop("checked");

        if (!checked) {
            $(".items_app_personalizado").prop("checked", false);
        }
        $(".items_app_personalizado").prop("readonly", checked ? "readonly" : "");
        $(".items_app_personalizado").prop("disabled", !checked ? "disabled" : "");

        return self;
    },
    /**
     * Método que obtem informações de crop image e atribui aos campos à serem enviados
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-05
     */
    getAreaImage: function (param) {
        'use strict';
        $("#crop-height").val(param.height);
        $("#crop-width").val(param.width);
        $("#crop-x1").val(param.x);
        $("#crop-x2").val(param.scaleX);
        $("#crop-y1").val(param.y);
        $("#crop-y2").val(param.scaleY);
    },
    /**
     * Constructor
     */
    init: function () {
        let self = this;

        document.title = 'GOTAS - Adicionar Rede';
        $("#nome-rede").focus();
        $(".img-crop-logo").data("cropper");
        $("#custo-referencia-gotas").maskMoney();
        fixMoneyValue($("#custo-referencia-gotas"));
        $("#app-personalizado")
            .off("click")
            .on("click", self.changeEnabledAppPersonalizado);

        // Eventos
        $(document)
            .off("change", "#nome-img")
            .on("change", "#nome-img", self.treatUploadImage);
        $(document)
            .off("click", "#redes-form #btn-save")
            .on("click", "#redes-form #btn-save", self.formSubmit);

        return self;
    },
    /**
     * Dispara submit ao clicar no botão de salvar do form
     * @param {*} evt
     */
    formSubmit: function (evt) {
        'use strict';
        let self = this;
        evt.preventDefault();

        if (redesEdit.validateForm("#redes-form").form()) {
            redesEdit.save($("#redes-form"));
        }

        return self;
    },
    /**
     * Trata os dados antes de submeter ao salvar
     *
     * @param {FormElement} FormElement
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-06
     */
    save: async function (form) {
        let self = this;
        // serializa o form e remove espaços em branco, transforma em array
        let objToTreat = $(form).serialize().replace(/%20/gi, " ").split("&");
        let objPost = {};

        /**
         * Todos os elementos da tela que não precisam de tratamento, são convertidos em objeto
         * Caso alguma das propriedades precise de um tratamento adicional, faça após o foreach
         */
        objToTreat.forEach(index => {
            let item = index.split("=");
            objPost[item[0]] = item[1];
        });

        try {
            let response = await redesService.save(objPost);

            if (response === undefined || response === null || !response) {
                toastr.error(response.mensagem.message);
                return false;
            }

            // Gravação feita com sucesso, redireciona
            toastr.success(response.mensagem.message);
            window.location = "#/redes/index";
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

        return self;
    },

    /**
     * Trata o envio de uma imagem
     *
     * @param {OnChangeEvent} image
     * @returns this
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-06
     */
    treatUploadImage: async function (image) {
        try {
            let self = this;
            // infelizmente em método de change, o this/self é o próprio elemento
            let response = await redesService.uploadImage(image);

            if (response === undefined || response === null || (response !== undefined && !response.mensagem.status)) {
                toastr.error(response.mensagem.message);
                return false;
            }

            let arquivo = undefined;

            if (response.files.filesUploaded.length > 0) {
                // só pode enviar um, pega o primeiro disponível
                arquivo = response.files.filesUploaded[0];
            }
            // Exibe as divs
            $(".img-crop-container").show();
            $(".img-crop-logo-preview").show();

            callLoaderAnimation("Carregando imagem...");

            $(".img-crop-logo").attr("src", arquivo.path);
            $(".img-upload").val(arquivo.file);

            $(".img-crop-logo").on("load", function () {
                closeLoaderAnimation();
            });

            $(".img-crop-logo").cropper("destroy");
            $(".img-crop-logo").cropper({
                preview: ".img-crop-logo-preview",
                autoCrop: true,
                dragDrop: true,
                movable: true,
                resizable: true,
                zoomable: true,
                crop: function (event) {
                    redesAdd.getAreaImage(event.detail);
                }
            });

            $(".img-crop-logo").data("cropper");
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

        return self;
    },
    /**
     * Realiza validação em um formulário selecionado
     * @param {Form} form Formulário alvo
     * @returns jQuery.Validation Validação em um Formulário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-07
     */
    validateForm: function (form) {
        var self = this;
        return $(form).validate({
            rules: self.validationOptions.rules,
            messages: self.validationOptions.messages
        });
    },
    /**
     * Opções de validação
     */
    validationOptions: {
        messages: {
            nome_rede: {
                required: "Informe o Nome da Rede",
                minlength: "Nome da Rede deve conter ao menos 3 letras"
            },
            quantidade_pontuacoes_usuarios_dia: {
                required: "Informe o Máximo de Pontuações Diárias por Usuário",
                min: "Mínimo 1 Pontuações por dia",
                max: "Máximo 365 Pontuações por dia"
            },
            quantidade_consumo_usuarios_dia: {
                required: "Informe o Máximo de Consumos (Usos de Brinde) Diários por Usuário",
                min: "Mínimo 1 Consumo por dia",
                max: "Máximo 365 Consumos por dia"
            },
            qte_mesmo_brinde_resgate_dia: {
                required: "Informe o Máximo de Resgates de Brinde por dia por Usuário",
                min: "Mínimo 1 Resgate por dia",
                max: "Máximo 10 Resgate por dia"
            },
            tempo_expiracao_gotas_usuarios: {
                required: "Informe o Tempo de Expiração de Pontos dos Usuários (em meses)",
                min: "Mínimo 1",
                max: "Máximo 99999"
            },
            custo_referencia_gotas: {
                required: "Informe Custo de Referência Gotas",
                min: 0.01
            },
            media_assiduidade_clientes: {
                required: "Informe Média de Assiduidade de Clientes (Por mês)",
                min: "Mínimo 1",
                max: "Máximo 30"
            },
            qte_gotas_minima_bonificacao: {
                required: "Informe Quantidade de Gotas/Pontos Mínima para Bonificação Extra"
            },
            qte_gotas_bonificacao: {
                required: "Informe a Quantidade de Gotas/Pontos de Bonificação para Usuário"
            }
        },
        rules: {
            nome_rede: {
                required: true,
                minlength: 3
            },
            quantidade_pontuacoes_usuarios_dia: {
                required: true,
                min: 1,
                max: 365
            },
            quantidade_consumo_usuarios_dia: {
                required: true,
                min: 1,
                max: 365
            },
            qte_mesmo_brinde_resgate_dia: {
                required: true,
                min: 1,
                max: 10
            },
            tempo_expiracao_gotas_usuarios: {
                required: true,
                min: 1,
                max: 99999
            },
            custo_referencia_gotas: {
                required: true,
                min: 0.01
            },
            media_assiduidade_clientes: {
                required: true,
                min: 1,
                max: 30
            },
            qte_gotas_minima_bonificacao: "required",
            qte_gotas_bonificacao: "required"
        },
    },
};
