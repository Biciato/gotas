/**
 * @file webroot/js/scripts/gotas/gotas_input_form_without_ocr.js
 * @author Gustavo Souza Gonçalves
 * @date 13/09/2017
 * @
 *
 */
$(document).ready(function () {

    var video = null;

    // --------------- Inicialização de campos na tela ---------------

    // Lista de seleção de parâmetros
    $(".gotas-camera-manual-insert #list_parametros").prop('disabled', true);

    // Campo de chave de acesso da Nota Fiscal Eletrônica
    $(".gotas-camera-manual-insert #chave_nfe").mask('999999');

    // Máscara para quantidade
    $(".gotas-camera-manual-insert #quantidade_input").mask("####.###", {
        reverse: true
    });

    // trata se valor for inferior à 1
    $(".gotas-camera-manual-insert #quantidade_input").on('blur', function () {
        if (this.value.indexOf('.') == -1) {

            if (parseInt(this.value) <= 9) {
                this.value = '0.0' + parseInt(this.value);
            } else if (parseInt(this.value) <= 99) {
                this.value = '0.' + parseInt(this.value);
            }
        }
    });

    // Botão de Salvar Recibo
    $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").prop('disabled', true);

    // --------------- Propriedades ---------------

    var chave_nfe_valid = {
        isValid: false,
        get: function () {
            return this.isValid;
        },
        set: function (value) {
            this.isValid = value;
        }
    };

    // Array de parâmetros de gotas populados (para gravar no BD)
    var arrayParametrosGravar = {
        array: [],
        get: function () {
            return this.array;
        },
        set: function (array) {
            this.array = array;
        },
        add: function (item) {
            this.array.push(item);
        },
        remove: function (key) {
            var arrayToRemove = [];

            $.each(this.array, function (index, value) {
                if (value.key != key) {
                    arrayToRemove.push(value);
                }
            })

            this.array = arrayToRemove;
        },
        clear: function () {
            this.array = [];
        }
    };

    // Array de gotas (parâmetros)
    var arrayGotas = {
        array: [],
        get: function () {
            return this.array;
        },
        findByKey: function (key) {
            var item = $.grep(this.array, function (value, index) {
                if (value.gotas_id == key)
                    return value;
            });

            return item[0];
        },
        set: function (array) {
            this.array = array;
        }
    };

    // --------------- Funções ---------------

    $(".gotas-camera-manual-insert .call-modal-how-it-works").on('click', function () {
        var id = $(this.attributes['target-id']).val();

        callHowItWorks(id);
    });

    /**
     *
     */
    var initializeSelectClicks = function () {
        $(".gotas-camera-manual-insert .select-button").on('click', function () {

            var key = parseInt($(this)[0].attributes['value'].value);
            arrayParametrosGravar.remove(key);
            updateTableParametrosGravar();
        });
    }

    /**
     * Verifica por cupom repetido
     *
     * @param {function} function_execute Função à ser executada no final
     */
    var checkTaxCouponRepeated = function (function_execute) {
        var chave_nfe = $(".gotas-camera-manual-insert #chave_nfe").val();
        var chave_nfe_original = chave_nfe;

        if (chave_nfe_original.length < 6) {
            $(".gotas-camera-manual-insert #list_parametros").attr('disabled', true);
        } else {

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
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json");
                    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                },
                error: function (response) {
                    console.log(response);
                    closeLoaderAnimation();
                }
            }).done(function (result) {
                closeLoaderAnimation();

                if (result.found) {
                    callModalError("Este registro já foi importado previamente, não sendo possível a importação!");

                    $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").attr('disabled', true);
                    $(".gotas-camera-manual-insert #list_parametros").val(null);
                    $(".gotas-camera-manual-insert #list_parametros").attr('disabled', true);
                    $(".gotas-camera-manual-insert #list_parametros").change();

                } else {
                    $(".gotas-camera-manual-insert #list_parametros").attr('disabled', false);

                    if (arrayParametrosGravar.get().length > 0) {
                        $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").attr('disabled', false);

                        if (function_execute !== undefined) {
                            function_execute();
                        }
                    }
                }
            })
                ;
        }
    }

    var exibeTelaCapturaMG = function(){
        // window.localStorage.setItem("dadosCupom", arrayParametrosGravar.get());

        $(".gotas-camera-manual-insert").hide();

        manualInstascanReceipt();
    }

    /**
     * Configurações para campo #chave_nfe
     */

    $(".gotas-camera-manual-insert #chave_nfe").on('keydown', function (data) {
        if (!isNaN(data.key)) {
            return;
        }
    });

    /**
     * Verifica se a chave já foi inserida quando o usuário a digitar no campo de Chave da Nota Fiscal
     */
    $(".gotas-camera-manual-insert #chave_nfe").on('keyup', function (data) {
        enableSelectParameter();

        checkTaxCouponRepeated();
    });

    /**
     * Habilita o botão de selecionar parâmetro
     */
    var enableSelectParameter = function () {
        if ($(".gotas-camera-manual-insert #list_parametros").val() !== "") {
            $(".gotas-camera-manual-insert .select-parameter").prop('disabled', false);

        } else {
            $(".gotas-camera-manual-insert .select-parameter").prop('disabled', true);
        }
    }

    /**
     *  Carrega todos os parâmetros disponíveis se estado é MG
     */
    if ($("#estado_funcionario").val() == 'MG') {

        callLoaderAnimation();

        $.ajax({
            type: "post",
            url: "/Gotas/getGotasByCliente",
            data: JSON.stringify({
                clientes_id: $("#clientes_id").val()
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            },
            error: function (response) {
                console.log(response);
                closeLoaderAnimation();
            }
        }).done(function (result) {
            closeLoaderAnimation();

            if (result.gotas !== undefined && result.gotas.length > 0) {
                arrayGotas.set(result.gotas);
                $(".gotas-camera-manual-insert #list_parametros").append($("<option>"));

                $.each(result.gotas, function (index, value) {

                    $(".gotas-camera-manual-insert #list_parametros").append($("<option>", {
                        value: value.gotas_id,
                        text: value.nome_parametro
                    }));

                });
            } else {
                callModalError("Não há gotas configuradas para o estabelecimento, não será possível realizar a <strong>Atribuição de Gotas!</strong>. <br /> \
                Comunique seu gestor");
            }

        });
    }

    // Configura botão de adicionar registro

    // $(".select-parameter").on('click', function() {

    $(".gotas-camera-manual-insert #list_parametros").on('change', function () {
        var key = $(this).val();

        var parameter = arrayGotas.findByKey(key);

        parameter == undefined ? $(".gotas-camera-manual-insert #quantidade_input").prop('disabled', true) : $(".gotas-camera-manual-insert #quantidade_input").prop('disabled', false);

        delete parameter;

    });

    $(".gotas-camera-manual-insert #list_parametros").on('change', function () {
        enableSelectParameter();
    });

    /**
     * Formulário 'Parâmetro à ser inserido'
     */

    // Campo de quantidade

    $(".gotas-camera-manual-insert #quantidade_input").on('keyup', function () {
        if ($(this).val() != "") {
            $(".gotas-camera-manual-insert .add-parameter").prop('disabled', false);
        } else {
            $(".gotas-camera-manual-insert .add-parameter").prop('disabled', true);
        }
    });

    // Configura botão de adicionar parâmetro
    $(".gotas-camera-manual-insert .add-parameter").prop('disabled', true);

    $(".gotas-camera-manual-insert .add-parameter").on('click', function () {

        var key = $(".gotas-camera-manual-insert #list_parametros").val();

        var item = arrayGotas.findByKey(key);

        var itemToAdd = {
            key: arrayParametrosGravar.get().length,
            id: item.id,
            gotas_id: item.gotas_id,
            nome_parametro: item.nome_parametro,
            quantidade_multiplicador: parseFloat($(".gotas-camera-manual-insert #quantidade_input").val())
        };

        arrayParametrosGravar.add(itemToAdd);

        updateTableParametrosGravar();
    });

    /**
     * Atualiza tabela de dados à enviar via POST ao banco
     */
    var updateTableParametrosGravar = function () {
        $(".gotas-camera-manual-insert .gotas-products-table >tbody").html('');

        if (arrayParametrosGravar.get().length > 0) {
            $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").prop('disabled', false);
        } else {
            $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").prop('disabled', true);
        }

        $.each(arrayParametrosGravar.get(), function (index, value) {

            var html = "<tr><td>" + value.nome_parametro + "</td><td>" + value.quantidade_multiplicador + "</td><td>" + "<div class='btn btn-danger btn-xs select-button' value='" + value.key + "'>Remover</div>" + "</td></tr>";

            $(".gotas-camera-manual-insert .gotas-products-table ").append(html);
        });

        initializeSelectClicks();
    };

    /**
     * Configura botão de salvar
     */
    $(".gotas-camera-manual-insert .user-btn-proceed-picture-mg").on('click', function () {

        // verifica primeiro se registro já existe.
        // se não existir, executa a função passada via parâmetro
        checkTaxCouponRepeated(exibeTelaCapturaMG);

    });

    /**
     * Salva os registros
     */
    var saveReceipt = function (image) {
        var data = [];

        $.each(arrayParametrosGravar.get(), function (index, value) {
            value.clientes_id = $("#clientes_id").val();
            value.usuarios_id = $("#usuarios_id").val();

            var chave_nfe = $(".gotas-camera-manual-insert #chave_nfe").val();

            while (chave_nfe.indexOf(" ") != -1) {
                chave_nfe = chave_nfe.replace(" ", "");
            }

            value.chave_nfe = chave_nfe;

            value.nome_img = $("#image_name").val();
            value.estado_nfe = $("#estado_funcionario").val();

            data.push(value);
        });

        $.ajax({
            type: "POST",
            url: "/PontuacoesComprovantes/saveManualReceipt",
            data: JSON.stringify({
                data: data,
                image: image
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");

                callLoaderAnimation();
            },
            error: function (response) {
                console.log(response);
                closeLoaderAnimation();
            }
        }).done(function (result) {
            console.log(result);
            if (result.success) {
                //success
                var content = prepareContentPontuacoesDisplay(result.data);

                callModalSave(content);

                // callModalSave();

                $(".gotas-camera-manual-insert #chave_nfe").val(null);
                $(".gotas-camera-manual-insert #list_parametros").val(0);
                $(".gotas-camera-manual-insert #list_parametros").change();

                $(".gotas-camera-manual-insert #quantidade_input").val(null);
                arrayParametrosGravar.clear();

                updateTableParametrosGravar();

                resetLayout();

                closeLoaderAnimation();
            } else {
                // erro
                callModalError("Houve um erro no processamento.");

                closeLoaderAnimation();
            }
        });
    }







     /**
     * Inicia gravação de câmera para captura de imagem
     */
    // var startScanCapture = function (regionCapture, videoElement, canvasElement) {
        var startScanCapture = function (regionCapture, videoElement) {

            $("." + regionCapture).show();

            video = null;
            video = document.querySelector("#" + videoElement);

            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

            var hdConstraints = {
                video: {
                    optional: [{
                        minWidth: 320
                    }, {
                        minWidth: 640
                    }, {
                        minWidth: 1024
                    }, {
                        minWidth: 1280
                    }, {
                        minWidth: 1920
                    }, {
                        minWidth: 2560
                    },],

                },
                audio: false
            };

            if (navigator.getUserMedia) {
                navigator.getUserMedia(hdConstraints, handleVideo, videoError);
            }

            function handleVideo(stream) {
                // window.localStream = stream;
                // video.src = window.URL.createObjectURL(stream);
                video.srcObject = stream;
                video.play();

            }

            function videoError(e) { // do something
            }
        };

        /**
         * Interrompe captura da Webcam
         */
        var stopCamRecording = function () {

            var interval = 0;
            var retries = 0;
            interval = setInterval(function () {
                if (window.localStream !== undefined) {
                    window.localStream.getVideoTracks()[0].stop();
                }
                clearInterval(interval);

                // necessário aguardar pelo menos 1 segundo para evitar efeito de imagem escurecida

            }, 1000);
        }

        /**
     * Oculta região de captura de imagem e interrompe o dispositivo webcam
     */
    var stopScanDocument = function () {
        stopCamRecording();

        $(".group-video-capture").hide();
    };

    var stopQRCodeCapture = function () {
        $(".video-gotas-scanning-container").hide();
        $(".group-capture-qr-code").hide();

    }

      /**
     * Interrompe captura de QR Code e exibe captura de imagem
     */
    var manualInstascanReceipt = function () {
        stopQRCodeCapture();

        // startScanCapture("video-receipt-capture-container", "video-receipt-capture", "canvas-instascan-gotas");

        startScanCapture("video-receipt-capture-container", "video-receipt-capture");
        // startScanCapture("video-gotas-capture-container", "group-video-capture-gotas");
    }

    $(".capture-receipt-snapshot").on('click', function () {
        canvas = $("#canvas-instascan-gotas")[0];

        var height = canvas.height;
        var width = canvas.width;

        var canvasContext = canvas.getContext('2d');
        canvasContext.drawImage(video, 0, 0, width, height);

        $(".video-receipt-capture-container").hide();
        $(".video-receipt-captured-region").fadeIn(500);

        stopScanDocument();
    });

    $(".store-receipt").on('click', function () {
        var canvas = $("#canvas-instascan-gotas")[0];
        var img = canvas.toDataURL('image/jpeg');

        var items = arrayParametrosGravar.get();

        saveReceipt(img, items);
    });

});
