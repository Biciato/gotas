'use strict';

$(document).ready(function () {

    // ------------ FUNÇÕES ------------

    /**
     * brindes_form::validarForm
     *
     * Remove validação padrão, e adiciona comportamento específico da tela.
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-03-31
     */
    var validarForm = function (value) {
        $(".botao-confirmar").unbind("click");

        if (value) {

            $(".botao-confirmar").on("click", function (e) {
                var form = e.target.form;

                // remove mascara dos campos de valor, e se valor for 0, limpa.
                defineFormatoPrecosVenda(false);
                verificaPreenchimentoCamposPreco();
                // form é válido?
                if (form.checkValidity()) {
                    callLoaderAnimation();

                } else {
                    // return false;
                    defineFormatoPrecosVenda(true);
                }
            });
        } else {
            validacaoGenericaForm();
        }
    }


    /**
     * brindes_form.js::tipo-equipamento-onchange
     *
     * Define obrigatoriedade de campos 'Código Brinde' e 'Tempo Uso brinde' se equipamento = RTI
     *
     * @param Object {value} Valor a ser validado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-04-22
     *
     * @return void
     */
    var tipoEquipamentoOnChange = function (value) {
        var obrigatorio = false;
        var labelCodigoPrimario = $("label[for=codigo_primario]").text().replace("*", "");
        var labelTempoUsoBrinde = $("label[for=tempo_uso_brinde]").text().replace("*", "");

        if (value == "Equipamento RTI") {
            obrigatorio = true;
            labelCodigoPrimario += "*";
            labelTempoUsoBrinde += "*";
        }
        $(".codigo-primario").attr("required", obrigatorio);
        $("label[for=codigo_primario]").text(labelCodigoPrimario);
        $(".tempo-uso-brinde").attr("required", obrigatorio);
        $("label[for=tempo_uso_brinde]").text(labelTempoUsoBrinde);
    };
    $(".tipo-equipamento").on("change", function (e) {
        tipoEquipamentoOnChange(e.target.value);
    }).change();


    var editMode = $("#edit_mode").val();
    var nome = $("#nome").val();


    /**
     * Define obrigatoriedades de campos de preço em gotas
     *
     * @param {bool} True / False
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-03-29
     *
     */
    var defineObrigatorioPrecoPadraoGotas = function (value) {
        // var label = "Preço Padrão Gotas";
        var label = $("label[for=preco_padrao]").text();
        label = label.replace("*", "");
        $("#preco_padrao").attr("required", false);
        $("label[for=preco_padrao]").text(label);
        if (value) {
            $("label[for=preco_padrao]").text(label + "*");
            $("#preco_padrao").attr("required", true);
        }
    };

    /**
     * Define obrigatoriedades de campos de preço em reais
     *
     * @param {bool} True / False
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-03-29
     *
     */
    var defineObrigatorioPrecoPadraoAvulso = function (value) {
        // var label = "Preço Padrão Venda Avulsa (R$)";
        var label = $("label[for=valor_moeda_venda_padrao]").text();
        label = label.replace("*", "");
        $("#valor_moeda_venda_padrao").attr("required", false);
        $("label[for=valor_moeda_venda_padrao]").text(label);
        if (value) {
            $("label[for=valor_moeda_venda_padrao]").text(label + "*");
            $("#valor_moeda_venda_padrao").attr("required", true);
        }
    };

    // Tipo de Venda

    $(".tipo-venda").on("change", function (e) {

        console.log(this.value);
        var selecionado = this.value;

        switch (selecionado) {
            case "Isento": {

                defineObrigatorioPrecoPadraoGotas(false);
                defineObrigatorioPrecoPadraoAvulso(false);
                validarForm(false);
                break;
            }
            case "Com Desconto": {
                defineObrigatorioPrecoPadraoGotas(true);
                defineObrigatorioPrecoPadraoAvulso(true);
                validarForm(false);
                break;
            }
            case "Gotas ou Reais": {
                validarForm(true);
                break;
            }
            default: {

                break;
            }
        }
    }).change();




    var defineFormatoPrecosVenda = function (value) {
        if (value) {
            $("#preco_padrao").on("focus", function () {
                $("#preco_padrao").maskMoney({ clearIncomplete: true });
                $("#preco_padrao").attr("maxlength", 10);
            });

            $("#valor_moeda_venda_padrao").on("focus", function () {
                $("#valor_moeda_venda_padrao").maskMoney({ clearIncomplete: true });
                $("#valor_moeda_venda_padrao").attr("maxlength", 10);
            });
        } else {
            $("#preco_padrao").maskMoney('destroy');
            $("#preco_padrao").attr("maxlength", 10);
            $("#valor_moeda_venda_padrao").maskMoney('destroy');
            $("#valor_moeda_venda_padrao").attr("maxlength", 10);
        }
    };

    defineFormatoPrecosVenda(true);

    /**
     * brindes_form::verificaPreenchimentoCamposPreco
     *
     * Verifica se o preenchimento de campos está de acordo com a regra no submit
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-03-30
     *
     * @return void
     */
    var verificaPreenchimentoCamposPreco = function () {
        var valorGotas = $("#preco_padrao").val();

        if (parseFloat(valorGotas) == 0 || valorGotas.length == 0) {
            $("#preco_padrao").val(null);
            $("#valor_moeda_venda_padrao").attr("required", true);
        } else {
            $("#valor_moeda_venda_padrao").attr("required", false);
        }

        var valorMoeda = $("#valor_moeda_venda_padrao").val();

        if (parseFloat(valorMoeda) == 0 || valorMoeda.length == 0) {
            $("#valor_moeda_venda_padrao").val(null);
            $("#preco_padrao").attr("required", true);
        } else {
            $("#preco_padrao").attr("required", false);
        }

        // defineFormatoPrecosVenda(true);
    }

    var equipamentoRTIPadrao = $("#tipos_brindes_redes_id").val();
    /**
     * Verifica se está em edição e se o brinde sendo editado é banho.
     * Se for, seleciona um índice correspondente e não permite alterar o nome
     */
    if (editMode == 1 && equipamentoRTIPadrao <= 4) {
        $("#nome").attr("readonly", true);
        $("#tipos_brindes_redes_id").attr("readonly", true);
        // $("#tipos_brindes_redes_id").attr("disabled", true);

        $("#ilimitado").attr("checked", true);
        $("#ilimitado").attr("disabled", true);
    } else if (editMode == 1) {
        $("#nome").attr("readonly", false);
        $("#tipos_brindes_redes_id").attr("readonly", true);
        // $("#tipos_brindes_redes_id").attr("disabled", true);
    }

    $("#tipos_brindes_redes_id").on("change", function (obj) {
        var nome =
            this.value != undefined && this.value.length > 0
                ? $("#tipos_brindes_redes_id option:selected").text().trim() : "";

        console.log(obj);
        $("#nome").val(nome);

        // Obriga o campo a ser obrigatório se for brinde smart shower
        var obrigatorio = jQuery("option:selected", this).data("obrigatorio");
        if (obrigatorio) {
            $("#tempo_uso_brinde").attr("required", obrigatorio);
        } else {
            $("#tempo_uso_brinde").removeAttr("required");
        }

        var tipoPrincipal = jQuery("option:selected", this).data("tipo-principal-codigo-brinde");

        var marcarIlimitado = tipoPrincipal >= 1 && tipoPrincipal <= 4;
        $("#ilimitado").attr("checked", marcarIlimitado);
        $("#ilimitado").attr("disabled", marcarIlimitado);

        // if (this.value != undefined && this.value <= 4 && this.value.length > 0) {
        //     $("#nome").attr("readonly", true);
        //     $("#ilimitado").attr("checked", true);
        //     $("#ilimitado").on("click", function () { return false; });
        // } else {
        //     $("#nome").attr("readonly", false);

        //     $("#ilimitado").attr("checked", false);
        //     $("#ilimitado").on("click", function () { return true; });
        // }
    });
    $("#tempo_uso_brinde").on("blur", function () {
        if ($("#tipos_brindes_redes_id").val() <= 4) {
            if (this.value > 20) {
                this.value = 20;
            } else if (this.value < 0) {
                this.value = 0;
            }
            var nome =
                $("#tipos_brindes_redes_id option:selected").text().trim();

            if (nome.indexOf("<Selecionar>") < 0) {
                $("#nome").val(nome);
            }
        }
    });

    // Upload de Imagem

    $("#nome-img").on("change", function (image) {
        var formData = new FormData();

        formData.append("file", image.target.files[0]);

        callLoaderAnimation("Enviando Imagem...");

        $.ajax({
            url: "/Brindes/enviaImagemBrinde",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            mimeType: "application/x-www-form-urlencoded",
            cache: false,
            xhr: function () {
                // Custom XMLHttpRequest
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    // Avalia se tem suporte a propriedade upload
                    myXhr.upload.addEventListener(
                        "progress",
                        function (event) {
                            var percentComplete = event.loaded / event.total;
                            percentComplete = parseInt(percentComplete * 100);
                            console.log(percentComplete);

                            callLoaderAnimation("Enviando Imagem... " + percentComplete + "% ");

                            /* faz alguma coisa durante o progresso do upload */
                        },
                        false
                    );
                }
                return myXhr;
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
            },
            success: function (e) {
                // console.log(e);
            },
            error: function(e){
                console.log(e);
                closeLoaderAnimation();
            },
            complete: function (e) {
                closeLoaderAnimation();

                var result = JSON.parse(e.responseText);
                if (result.mensagem.status) {
                    $(".img-crop-container").show();
                    $(".img-crop-preview").show();

                    // No caso de imagem de rede, retornar o único item da lista

                    var arquivos = null;

                    if (result.arquivos != undefined && result.arquivos.filesUploaded != undefined && result.arquivos.filesUploaded.length > 0) {
                        var arquivo = null;

                        result.arquivos.filesUploaded.forEach(element => {
                            arquivo = element;
                        });

                        if (arquivo != undefined) {
                            $(".img-crop").attr("src", arquivo.path);
                            $(".img-upload").val(arquivo.file);

                            var image = $(".img-crop");

                            $(".img-crop").cropper("destroy");
                            image.cropper({
                                aspectRatio: 1 / 1,
                                preview: ".img-crop-preview",
                                autoCrop: true,
                                dragDrop: true,
                                movable: true,
                                resizable: true,
                                zoomable: false,
                                crop: function (event) {
                                    coordenadas(event.detail);
                                }
                            });

                            var cropper = image.data("cropper");
                        }
                    }
                } else {
                    callModalError(result.mensagem.message);
                }
            }
        });
    });

    var coordenadas = function (c) {
        $("#crop-height").val(c.height);
        $("#crop-width").val(c.width);
        $("#crop-x1").val(c.x);
        $("#crop-x2").val(c.scaleX);
        $("#crop-y1").val(c.y);
        $("#crop-y2").val(c.scaleY);
        console.log(c);
    };


});
