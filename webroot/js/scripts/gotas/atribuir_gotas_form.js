/**
 * @file webroot/js/scripts/gotas/atribuir_gotas_form.js
 * @author Gustavo Souza Gonçalves
 * @date 05/09/2017
 * @
 *
 */

$(document).ready(function() {
    // ---------------------------- Propriedades -----------------------------

    // ------------------- Inicialização de campos na tela -------------------

    // region Campos Obrigatórios

    $("label[for=parametro]").text("Parâmetro*");
    $("#parametro").prop("required", true);

    // endregion

    $("#parametro").focus();

    resetLayout();

    // let scanner;

    // campos para captura de webcam

    var canvas = null;
    // var canvasContext = null;
    var video = null;

    $(".user-btn-proceed").prop("disabled", true);

    // ------------------------------- Propriedades -------------------------------



    // ------------------------------- Funções -------------------------------

    /**
     * Exibe container de captura das gotas, se for estado = MG
     */
    $(".user-btn-proceed").on("click", function() {
        $(".user-result").hide(500);
        $(".video-capture-gotas-user-select-container").hide();

        var estado = $("#estado_funcionario").val();

        if (estado != "MG") {
            startQRCodeCapture();
        } else {
            // startScanCapture("video-gotas-capture-container", "video-gotas-capture", "canvas-cam-gotas");
            // startScanCapture("video-gotas-capture-container", "video-gotas-capture");
            exibeContainerCupomMG();
        }
    });

    /**
     *
     */
    var initializeSelectClicks = function() {
        $(".gotas-camera-manual-insert .select-button").on("click", function() {
            var key = parseInt($(this)[0].attributes["value"].value);
            arrayParametrosGravar.remove(key);
            updateTableParametrosGravar();
        });
    };

    // ------------------------- Funções Estendidas -------------------------

    /**
     * Permite proceder quando o usuário for selecionado
     */
    $("#usuarios_id").change(function() {
        var interval = 0;

        interval = setInterval(function() {
            if (
                $("#usuarios_id").val() != undefined &&
                $("#usuarios_id").val() > 0
            ) {
                $(".user-btn-proceed").removeClass("disabled");
                $(".user-btn-proceed").prop("disabled", false);
                clearInterval(interval);
            }
        }, 250);
    });

    /**
     * Extende o método key up definido em filtro_usuarios_ajax
     */
    $("#parametro").on("keyup", function(event) {
        if (event.keyCode == 13) {
            $("#usuarios_id").change();
        }
    });

    /**
     * Extende o método click definido em filtro_usuarios_ajax
     */
    $("#searchUsuario").on("click", function() {
        $("#usuarios_id").change();
    });

    // ----------------------- Funções Compartilhadas -----------------------

    var startQRCodeCapture = function() {
        // $(".group-capture-qr-code").show();
        $(".video-gotas-scanning-container").show();
        $(".qr_code_reader").focus();
        $(".qr_code_reader").val(null);
    };

    var stopQRCodeCapture = function() {
        $(".video-gotas-scanning-container").hide();
        // $(".group-capture-qr-code").hide();
    };

    var exibeContainerCupomMG = function() {
        // var data = new Date().toLocaleString();

        initializeDateTimePicker(
            "data_processamento",
            "data_processamento_save",
            true,
            moment(new Date(), "YYYY-MM-DD HH:mm A").format("DD/MM/YYYY HH:mm")
        );
        // $(".gotas-camera-manual-insert #data_processamento").val(data);
        $(".gotas-camera-manual-insert").fadeIn(500);
    };



    /**
     * Interrompe captura da Webcam
     */
    var stopCamRecording = function() {
        var interval = 0;
        var retries = 0;
        interval = setInterval(function() {
            if (window.localStream !== undefined) {
                window.localStream.getVideoTracks()[0].stop();
            }
            clearInterval(interval);

            // necessário aguardar pelo menos 1 segundo para evitar efeito de imagem escurecida
        }, 1000);
    };

    /**
     * Oculta região de captura de imagem e interrompe o dispositivo webcam
     */
    var stopScanDocument = function() {
        stopCamRecording();

        $(".group-video-capture").hide();
    };

    /**
     * Interrompe scanner caso clique em uma aba da tela
     */
    $(".atalhos").on("click", function() {
        stopScanDocument();
        resetLayout();
    });

    // ---------------- Funções para Escaneamento de QR Code ----------------

    $(".qr_code_reader").on("keydown", function(event) {
        // console.log(event);
        // return;
        if (event.keyCode == 13) {
            populateFuelWords(this.value.trim());
        }
    });

    /**
     * Teste de chamada AJAX
     * TODO: Remover após demonstrar testes
     */
    // $(".test-ajax").on('click', function () {
    //     // var url = "https://nfe.sefaz.ba.gov.br/servicos/nfce/modulos/geral/NFCEC_consulta_chave_acesso.aspx?chNFe=29161220808212000107650010000403311854501730&nVersao=100&tpAmb=1&dhEmi=323031362d31322d30365431343a32313a34342d30333a3030&vNF=95.16&vICMS=0.00&digVal=7a5047795668546d544369514e4d366465645856576a434541736f3d&cIdToken=000001&cHashQRCode=4d1425cea75c0d413a738ec9d7d6067b0c220f3e";
    //     var url = "https://www.sefaz.mt.gov.br/nfce/consultanfce?chNFe=51160407534929000140650010000691921177103192&nVersao=100&tpAmb=1&dhEmi=323031362D30342D30335431373A32383A35342D30343A3030&vNF=9.00&vICMS=0.00&digVal=5734656C564D4F31322B61705344364250796B45356A5252544C413D&cIdToken=000001&cHashQRCode=68C2E521AAD9B666BDDAD324EF3A2947DD427DBD";

    //     // var url = "https://www.sefaz.mt.gov.br/nfce/consultanfce?chNFe=51160407534929000140650010000691921177103192&nVersao=100&tpAmb=1&dhEmi=323031362D30342D30335431373A32383A35342D30343A3030&vNF=9.00&vICMS=0.00&digVal=5734656C564D4F31322B61705344364250796B45356A5252544C413D&cHashQRCode=68C2E521AAD9B666BDDAD324EF3A2947DD427DBD";
    //     // url com erro de leitura
    //     // var url = "https://www.sefaz.mt.gov.br/nfce/consultanfce?chNFe=51160407534929000140650010000691921177103192&nVersao=100&tpAmb=1&dhEmi=323031362D30342D30335431373A32383A35342D30343A3030&vNF=9.00&vICMS=0.00&digVal=5734656C564#4F31322B61705344364250796B45356A5252544C413D&cIdToken=000001&cHashQRCode=68C2E521AAD9B666BDDAD324EF3A2947DD427DBD";

    //     // var url = "https://www.sefaz.mt.gov.br/nfce/consultanfcechNFe=51160401289412000156650020000990311297144132&nVersao=100&tpAmb=1&cDest=01289412000156&dhEmi=03/04/2016";

    //     // var url = "https://www.sefaz.mt.gov.br/nfce/consultanfcechNFe=51160401289412000156650020000990311297144132&nVersao=100&tpAmb=1&cDest=01289412000156&dhEmi=03/04/2016 09:14:56&vNF=197.05&vICMS=0.00&digVal=&cIdToken=01";

    //     populateFuelWords(url);
    // });

    /**
     * Obtêm todos os modos de combustíveis disponíveis
     * @param url
     */
    var populateFuelWords = function(url) {
        callLoaderAnimation();

        $.ajax({
            type: "post",
            url: "/Gotas/getGotasByCliente",
            data: JSON.stringify({
                clientes_id: $("#clientes_id").val()
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader(
                    "Content-Type",
                    "application/json; charset=UTF-8"
                );
            },
            error: function(response) {
                console.log(response);
                callModalError(response.responseText);
                $(".qr_code_reader").val("");
            }
        }).done(function(result) {
            var fuelWords = result.gotas;

            if (url != undefined) {
                checkPreviousCoupon(url, fuelWords);
            } else {
                closeLoaderAnimation();
            }
            $(".qr_code_reader").val("");
        });
    };

    /**
     * Verifica se há cupom inserido anteriormente
     * @param {string} url endereço à verificar
     * @param {array} fuelWords array de palavras que será verificado o conteúdo
     */
    var checkPreviousCoupon = function(url, fuelWords) {
        // chave de autenticação
        var chave_nfe_start = "chNFe=";
        var chave_nfe_start_index =
            url.indexOf(chave_nfe_start) + chave_nfe_start.length;
        var chave_nfe_end_index = url.indexOf("&", chave_nfe_start_index);
        var chave_nfe = url.substr(
            chave_nfe_start_index,
            chave_nfe_end_index - chave_nfe_start_index
        );

        // estado
        var estado_nfe = url
            .substr(url.indexOf("sefaz.") + "sefaz.".length, 2)
            .toUpperCase();

        var data = {
            chave_nfe: chave_nfe,
            estado_nfe: estado_nfe
        };
        $.ajax({
            type: "post",
            url: "/PontuacoesComprovantes/findTaxCoupon",
            data: JSON.stringify(data),
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader(
                    "Content-Type",
                    "application/json; charset=UTF-8"
                );
            },
            error: function(response) {
                console.log(response);
                closeLoaderAnimation();
            }
        }).done(function(result) {
            if (result.found) {
                callModalError(result.message);
            } else {
                // só vai no servidor da SEFAZ se a URL estiver ok
                if (!checkURLConsistency(url)) {
                    saveTaxCoupon(url);
                } else {
                    closeLoaderAnimation();
                }
            }
        });
    };

    /**
     * Chama um serviço REST de salvar comprovante
     * @param {string} url
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     */
    var saveTaxCoupon = function(url) {
        // chave de autenticação

        $.ajax({
            type: "POST",
            url: "/api/pontuacoes_comprovantes/set_comprovante_fiscal_usuario",
            data: JSON.stringify({
                qr_code: url,
                clientes_cnpj: $("#clientesCNPJ").val(),
                usuarios_id: $("#usuarios_id").val(),
                funcionarios_id: $("#funcionarios_id").val()


            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader(
                    "Content-Type",
                    "application/json; charset=UTF-8"
                );
                xhr.setRequestHeader("IsMobile", true);
            },
            error: function(response) {
                console.log(response);

                closeLoaderAnimation();
                callModalError(response.responseJSON.mensagem.message, response.responseJSON.mensagem.errors);
            }
        }).done(function(result) {
            if (result.mensagem.status) {
                var content = prepareContentPontuacoesDisplay(result);

                callModalSave(content);
                resetLayout();
            } else {
                callModalError(result.mensagem.message, result.mensagem.errors);
            }

            closeLoaderAnimation();
        });
    };

    /**
     * Verifica consistência do URL conforme documento da SEFAZ
     * @param {string} url
     */
    var checkURLConsistency = function(url) {

        if (url === undefined || url.toString().length == 0){
            callModalError("Campo QR CODE não informado!");
            return false;
        }

        /**
         * Regras:
         * Key: nome da chave;
         * Size: tamanho que deve constar;
         * FixedSize: tamanho deve ser obrigatoriamente conforme size;
         * isOptional: Se é opcional mas está informado
         * index: indice do registro na url
         */

        var arrayConsistency = [
            {
                key: "chNFe",
                size: 44,
                fixedSize: true,
                isOptional: false,
                content: null,
                index: 0
            },
            {
                key: "nVersao",
                size: 3,
                fixedSize: true,
                isOptional: false,
                content: null,
                index: 0
            },
            {
                key: "tpAmb",
                size: 1,
                fixedSize: true,
                isOptional: false,
                content: null,
                index: 0
            },
            {
                key: "cDest",
                size: 3,
                fixedSize: false,
                isOptional: true,
                content: null,
                index: 0
            },
            {
                key: "dhEmi",
                size: 50,
                fixedSize: false,
                isOptional: false,
                content: null,
                index: 0
            },
            {
                key: "vNF",
                size: 15,
                fixedSize: false,
                isOptional: false,
                content: null,
                index: 0
            },
            {
                key: "vICMS",
                size: 15,
                fixedSize: false,
                isOptional: false,
                content: null,
                index: 0
            },
            {
                key: "digVal",
                size: 56,
                fixedSize: true,
                isOptional: false,
                content: null,
                index: 0
            },
            {
                key: "cIdToken",
                size: 6,
                fixedSize: true,
                isOptional: false,
                content: null,
                index: 0
            },
            {
                key: "cHashQRCode",
                size: 40,
                fixedSize: true,
                isOptional: false,
                content: null,
                index: 0
            }
        ];

        var hasErrors = false;

        var arrayErrors = [];

        $.each(arrayConsistency, function(index, value) {
            console.log(value);

            var key = value.key + "=";

            // aponta o índice para o início do valor

            var keyIndex = url.indexOf(key);
            value.index = keyIndex + key.length;

            // registro é obrigatório?
            if (!value.isOptional) {
                var errorType = "";

                // é obrigatório mas não encontrado?
                if (keyIndex == -1) {
                    errorType =
                        "Campo <strong>" +
                        value.key +
                        "</strong> do QR Code deve ser informado";
                } else {
                    // índice de fim
                    var indexEnd = url.indexOf("&", value.index);

                    // caso extraordinário, trata se o campo for o último da lista
                    if (value.index > indexEnd) {
                        indexEnd = url.length;
                    }

                    // cálculo de tamanho do valor
                    var length = indexEnd - value.index;

                    // captura conteúdo
                    value.content = url.substr(
                        value.index,
                        indexEnd - value.index
                    );

                    // valida se o campo contem espaços (não é permitido)

                    var containsBlank = value.content.indexOf(" ");

                    if (containsBlank == -1) {
                        // valida se o tamanho do campo é fixo
                        if (value.fixedSize) {
                            if (length != value.size) {
                                errorType =
                                    "Campo <strong>" +
                                    value.key +
                                    "</strong> do QR Code deve conter " +
                                    value.size +
                                    " bytes";
                            }
                        }
                    } else {
                        errorType =
                            "Campo <strong>" +
                            value.key +
                            "</strong> contêm espaço em branco.";
                    }
                }

                if (errorType.length > 0) {
                    value.error = errorType;
                    arrayErrors.push(value);
                    hasErrors = true;
                }
            }
        });

        // se houve erro na análise da URL, o usuário deverá informar os dados manualmente
        if (arrayErrors.length > 0) {
            var hasErrorsString = "";

            $.each(arrayErrors, function(index, value) {
                hasErrorsString += value.error + "<br />";
            });

            callModalError(
                `O QR Code informado não está gerado conforme os padrões pré- estabelecidos da SEFAZ. <br />
                        Será necessário realizar a importação manual. <p> Erros encontrados na URL do QR Code (para informar aos desenvolvedores): </p> ` +
                    hasErrorsString
            );
        }

        // retorna true se quantidade de erros é maior que 0
        return arrayErrors.length > 0;
    };

       /**
     * Verifica por cupom repetido
     *
     * @param {function} function_execute Função à ser executada no final
     */
    var checkTaxCouponRepeated = function(function_execute) {
        var chave_nfe = $(".gotas-camera-manual-insert #chave_nfe").val();
        var chave_nfe_original = chave_nfe;


        var data = {
            chave_nfe: chave_nfe,
            estado_nfe: $("#estado_funcionario").val(),
            clientes_id: $("#clientes_id").val()
        };

        callLoaderAnimation();

        $.ajax({
            type: "POST",
            url: "/PontuacoesComprovantes/findTaxCoupon",
            data: JSON.stringify(data),
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader(
                    "Content-Type",
                    "application/json; charset=UTF-8"
                );
            },
            error: function(response) {
                console.log(response);
                closeLoaderAnimation();
            }
        }).done(function(result) {
            closeLoaderAnimation();

            if (result.found) {
                callModalError(
                    "Este registro já foi importado previamente, não sendo possível a importação!"
                );

                $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").attr("disabled", true);
                $(".gotas-camera-manual-insert #list_parametros").val(null);
                $(".gotas-camera-manual-insert #list_parametros").attr(
                    "disabled",
                    true
                );
                $(".gotas-camera-manual-insert #list_parametros").change();
            } else {
                $(".gotas-camera-manual-insert #list_parametros").attr(
                    "disabled",
                    false
                );

                $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").attr("disabled", false);

                if (function_execute !== undefined) {
                    function_execute();
                }
            }
        });
    };

    var buscaParametrosDisponiveis = function(){
        callLoaderAnimation();

        $.ajax({
            type: "post",
            url: "/Gotas/getGotasByCliente",
            data: JSON.stringify({
                clientes_id: $("#clientes_id").val()
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader(
                    "Content-Type",
                    "application/json; charset=UTF-8"
                );
            },
            error: function(response) {
                console.log(response);
                closeLoaderAnimation();
            }
        }).done(function(result) {
            closeLoaderAnimation();

            if (result.gotas !== undefined && result.gotas.length > 0) {
                arrayGotas.set(result.gotas);
                $(".gotas-camera-manual-insert #list_parametros").append(
                    $("<option>")
                );

                $.each(result.gotas, function(index, value) {
                    $(".gotas-camera-manual-insert #list_parametros").append(
                        $("<option>", {
                            value: value.gotas_id,
                            text: value.nome_parametro
                        })
                    );
                });


            } else {
                callModalError(
                    "Não há gotas configuradas para o estabelecimento, não será possível realizar a <strong>Atribuição de Gotas!</strong>. <br /> \
                Comunique seu gestor"
                );
            }
        });
    }

    $(".gotas-camera-manual-insert #list_parametros").on("change", function() {
        if ($(".gotas-camera-manual-insert #list_parametros").val() !== "") {
            $(".gotas-camera-manual-insert .add-parameter").prop("disabled", false);
            $(".gotas-camera-manual-insert #quantidade_input").prop("disabled", false);
        } else {
            $(".gotas-camera-manual-insert .add-parameter").prop("disabled", true);
            $(".gotas-camera-manual-insert #quantidade_input").prop("disabled", true);
        }
    });

    /**
     * Atualiza tabela de dados à enviar via POST ao banco
     */
    var updateTableParametrosGravar = function() {
        $(".gotas-camera-manual-insert .gotas-products-table >tbody").html("");

        if (arrayParametrosGravar.get().length > 0) {
            $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").prop(
                "disabled",
                false
            );
        } else {
            $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").prop(
                "disabled",
                true
            );
        }

        $.each(arrayParametrosGravar.get(), function(index, value) {
            var html =
                $("<tr><td>" +
                value.nome_parametro +
                "</td><td>" +
                value.quantidade_multiplicador_exibicao +
                "</td><td>" +
                "<div class='btn btn-danger btn-xs select-button' value='" +
                value.key +
                "'><i class='fa fa-trash text-danger' title='Remover'></i></div>" +
                "</td></tr>");

            $(".gotas-camera-manual-insert .gotas-products-table ").append(
                html
            );
        });

        initializeSelectClicks();
    };

    $(".manual-input").click(function(){
        $(".video-gotas-scanning-container").hide();
        // wip
        $(".gotas-camera-manual-insert").fadeIn(500);

        // Configura comportamento de campos do form

        // Lista de seleção de parâmetros
        $(".gotas-camera-manual-insert #list_parametros").prop("disabled", true);

        $(".gotas-camera-manual-insert label[for=chave_nfe]").text("Chave NFE");

        $(".gotas-camera-manual-insert #chave_nfe").css("-webkit-text-security", "disc");
        $(".gotas-camera-manual-insert #chave_nfe").css("-moz-text-security", "circle");
        $(".gotas-camera-manual-insert #chave_nfe").css("text-security", "circle");

        $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").attr("disabled", true);

        // Máscara para quantidade
        $(".gotas-camera-manual-insert #quantidade_input").mask("####.###", {
            reverse: true
        });

        // trata se valor for inferior à 1
        $(".gotas-camera-manual-insert #quantidade_input").on("blur", function() {
            if (this.value.indexOf(".") == -1) {
                var sufix = "";
                if (this.value <= 999) {
                    sufix = ".000";
                }
                this.value = this.value + sufix;
            }
        });
        $(".gotas-camera-manual-insert #chave_nfe").on("keyup", function(e){
            if(e.keyCode == 13) {
                // verifica se o qr code já foi inserido. se não foi, busca os parâmetros disponíveis

                $(".gotas-camera-manual-insert #list_parametros").empty();

                if (!checkTaxCouponRepeated(buscaParametrosDisponiveis)){
                    // Configura botão de adicionar parâmetro
                    $(".gotas-camera-manual-insert .add-parameter").prop("disabled", true);

                    $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").unbind("click");
                    $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").on("click", function(){
                        $(".gotas-camera-manual-insert").hide();
                        manualInstascanReceipt();
                    });

                    $(".gotas-camera-manual-insert .add-parameter").unbind("click");
                    $(".gotas-camera-manual-insert .add-parameter").on("click", function() {
                        var key = $(".gotas-camera-manual-insert #list_parametros").val();

                        var item = arrayGotas.findByKey(key);

                        var itemToAdd = {
                            key: arrayParametrosGravar.get().length,
                            id: item.id,
                            gotas_id: item.gotas_id,
                            nome_parametro: item.nome_parametro,
                            quantidade_multiplicador: parseFloat(
                                $(".gotas-camera-manual-insert #quantidade_input").val()
                            ),
                            quantidade_multiplicador_exibicao: $(
                                ".gotas-camera-manual-insert #quantidade_input"
                            ).val()
                        };

                        arrayParametrosGravar.add(itemToAdd);

                        updateTableParametrosGravar();
                    });
                }

            }
        })
    });

    /**
     * Captura imagem da webcam para QR Code e exibe layout de processamento
     */
    $(".take-scanner-snapshot").click(function() {
        manualInstascanReceipt();
    });

    /**
     * Interrompe captura de QR Code e exibe captura de imagem
     */
    var manualInstascanReceipt = function() {
        stopQRCodeCapture();

        // startScanCapture("video-receipt-capture-container", "video-receipt-capture", "canvas-instascan-gotas");
        startScanCapture(
            "video-receipt-capture-container",
            "video-receipt-capture"
        );
    };

    $(".capture-receipt-snapshot").on('click', function () {
        canvas = $("#canvas-instascan-gotas")[0];

        var height = canvas.height;
        var width = canvas.width;
        var video = $(".video-receipt-capture-container .video-cam")[0];
        var canvasContext = canvas.getContext('2d');
        canvasContext.drawImage(video, 0, 0, width, height);

        $(".video-receipt-capture-container").hide();
        $(".video-receipt-captured-region").fadeIn(500);

        stopScanDocument();
    });



    $(".store-receipt").on("click", function() {
        var canvas = $("#canvas-instascan-gotas")[0];
        var img = canvas.toDataURL("image/jpeg");

        var items = arrayParametrosGravar.get();

        saveReceipt(img, items);
    });

    /**
     * Exibe área de captura caso o operador queira tirar nova foto do documento
     */
    $(".video-receipt-capture-again").click(function() {
        canvas = $("#canvas-instascan-gotas")[0];

        var height = canvas.clientHeight;
        var width = canvas.clientWidth;

        var canvasContext = canvas.getContext("2d");

        canvasContext.clearRect(0, 0, canvas.width, canvas.height);
        canvasContext.restore();

        $(".video-receipt-captured-region").hide();
        $(".video-receipt-capture-container").fadeIn(500);
        // startScanCapture("video-receipt-capture-container", "video-receipt-capture", "canvas-instascan-gotas");
        startScanCapture(
            "video-receipt-capture-container",
            "video-receipt-capture"
        );
    });

    $(".store-receipt-image").on("click", function() {
        var canvas = $("#canvas-instascan-gotas")[0];
        var img = canvas.toDataURL("image/jpeg");

        getNewNameForImage(img);
    });

    /**
     * Obtêm novo nome para imagem. Salva imagem se obter com sucesso do servidor
     * @param {base64} imageData
     */
    var getNewNameForImage = function(imageData) {
        $.ajax({
            url: "/PontuacoesComprovantes/getNewReceiptName",
            type: "POST",
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader(
                    "Content-Type",
                    "application/json; charset=UTF-8"
                );
            },
            success: function(response) {
                console.log(response);
            },
            error: function(response) {
                console.log(response.responseText);
            }
        }).done(function(result) {
            saveImageReceipt(imageData, result.nome_img);
        });
    };

    /**
     * Salva imagem recibo no servidor em pasta temporária
     * @param {*} imageData dados de imagem (base 64)
     * @param {*} imageName nome da imagem
     */
    var saveImageReceipt = function(imageData, imageName) {
        callLoaderAnimation();
        var data = {
            image_name: imageName,
            image: imageData,
            _Token: document.cookie.substr(
                document.cookie.indexOf("csrfToken=") + "csrfToken=".length
            )
        };
        $.ajax({
            url: "/PontuacoesComprovantes/saveImageReceipt",
            type: "POST",
            data: JSON.stringify(data),
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader(
                    "Content-Type",
                    "application/json; charset=UTF-8"
                );
            },
            error: function(response) {
                console.log(response.responseText);
                closeLoaderAnimation();
            }
        }).done(function(result) {
            closeLoaderAnimation();

            if (result.success) {
                // chama tela de entrada de dados manual para QR Code

                var estado = $("#estado_funcionario").val();

                if (estado != "MG") {
                    $(".video-receipt-captured-region").hide();
                    $(".gotas-instascan-manual-insert").fadeIn(500);
                } else {
                    // todo wip
                    // populateFuelWords();
                    // $(".video-gotas-captured-region").hide();
                    // $(".gotas-camera-manual-insert").fadeIn(500);
                }

                // guarda nome a ser usado ao salvar no bd
                $("#image_name").val(imageName);
            }
        });
    };

    $(".video-gotas-capture-container .video-gotas-snapshot").on(
        "click",
        function() {
            canvas = $("#canvas-cam-gotas")[0];

            var height = canvas.height;
            var width = canvas.width;

            var canvasContext = canvas.getContext("2d");
            canvasContext.drawImage(video, 0, 0, width, height);

            $(".video-gotas-capture-container").hide();
            $(".video-gotas-captured-region").fadeIn(500);

            stopScanDocument();
        }
    );

    /**
     * Exibe área de captura caso o operador queira tirar nova foto do documento
     */
    $(".video-gotas-capture-again").click(function() {
        canvas = $("#canvas-cam-gotas")[0];

        var height = canvas.clientHeight;
        var width = canvas.clientWidth;

        var canvasContext = canvas.getContext("2d");

        canvasContext.clearRect(0, 0, canvas.width, canvas.height);
        canvasContext.restore();

        $(".video-gotas-captured-region").hide();
        $(".video-gotas-capture-container").fadeIn(500);
        // startScanCapture("video-gotas-capture-container", "video-gotas-capture", "canvas-cam-gotas");
        startScanCapture(
            "video-gotas-capture-container",
            "video-gotas-capture"
        );
    });

    $(".store-gotas-image").on("click", function() {
        var canvas = $("#canvas-cam-gotas")[0];
        var img = canvas.toDataURL("image/jpeg");

        getNewNameForImage(img);
    });
});

/**
 * Reseta o layout da tela
 */
var resetLayout = function() {
    $(".group-video-capture-gotas").show();

    $("#parametro").val(null);
    $("#parametro").focus();
    // video-capture-gotas-user-select-container
    $(".user-query-region").show();

    initializeDateTimePicker(
        "data_processamento",
        "data_processamento_save",
        true,
        moment(new Date(), "YYYY-MM-DD HH:mm A").format("DD/MM/YYYY HH:mm")
    );

    // reset para filtro de estados com qr code
    $(".group-capture-qr-code").hide();
    $(".video-receipt-capture-container").hide();
    $(".video-receipt-captured-region").hide();
    $(".video-gotas-capture-container").hide();
    $(".video-gotas-captured-region").hide();
    $(".video-gotas-scanning-container").hide();
    $(".qr_code_reader").val(null);

    // reset de importação manual

    $(".gotas-instascan-manual-insert").hide();

    // reset para filtro de estados sem qr code
    $(".gotas-camera-manual-insert").hide();
};
