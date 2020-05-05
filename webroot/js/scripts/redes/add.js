/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\redes\add.js
 * @since 1.2.3
 * @date 2020-05-05
 *
 */

var redesAdd = {
    changeEnabledAppPersonalizado: function (e) {
        let self = this;

        var checked = $("#app_personalizado").prop("checked");

        if (!checked) {
            $(".items_app_personalizado").prop("checked", false);
        }
        $(".items_app_personalizado").prop("readonly", checked ? "readonly" : "");
        $(".items_app_personalizado").prop("disabled", !checked ? "disabled" : "");

        return self;
    },
    /**
     * Método que obtem informações de desenho e atribui aos campos à serem enviados
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-05
     */
    coordenadas: function (param) {
        $("#crop-height").val(param.height);
        $("#crop-width").val(param.width);
        $("#crop-x1").val(param.x);
        $("#crop-x2").val(param.scaleX);
        $("#crop-y1").val(param.y);
        $("#crop-y2").val(param.scaleY);
        console.log(param);
    },
    init: function () {
        let self = this;

        document.title = 'GOTAS - Adicionar Rede';
        $("#nome-rede").focus();
        var image = $(".img-crop");
        var cropper = image.data("cropper");

        $("#custo_referencia_gotas").maskMoney();

        fixMoneyValue($("#custo_referencia_gotas"));
        $("#app_personalizado").off("click").on("click", self.changeEnabledAppPersonalizado);

        $(document).off("change", "#nome-img").on("change", "#nome-img", self.treatUploadImage);
        $(document)
            .off("click", "#form-redes-add #btn-save")
            .on("click", "#form-redes-add #btn-save", self.saveClick);

        return self;
    },
    saveClick: async function (evt) {
        evt.preventDefault();
        var data = $("#form-redes-add").serialize();

        console.log(data);

    },

    /**
     *
     * @param {*} data
     */
    saveRest: function (data) {
        return Promise.resolve(
            $.ajax({
                type: "POST",
                url: "/api/redes/add",
                data: data,
                dataType: "JSON"
            })
        );
    },
    treatUploadImage: async function (image) {
        try {
            let self = this;
            // infelizmente em método de change, o this/self é o próprio elemento
            let response = await redesAdd.uploadImage(image);
            console.log(response);

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
            $(".img-crop-preview").show();

            callLoaderAnimation("Carregando imagem...");

            $(".img-crop").attr("src", arquivo.path);
            $(".img-upload").val(arquivo.file);

            var imgCrop = $(".img-crop");

            $(".img-crop").on("load", function () {
                closeLoaderAnimation();
            });

            $(".img-crop").cropper("destroy");
            imgCrop.cropper({
                // aspectRatio: 1/1,
                preview: ".img-crop-preview",
                autoCrop: true,
                dragDrop: true,
                movable: true,
                resizable: true,
                // zoomable: false,
                zoomable: true,
                crop: function (event) {
                    self.coordenadas(event.detail);
                }
            });

            imgCrop.data("cropper");
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
     * Realiza upload de imagem
     * @param {Event} evt Evento
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-05
     */
    uploadImage: async function (image) {
        'use strict';
        let self = this;

        var formData = new FormData();

        var file = image.target.files[0];
        var response = undefined;

        if (file.size >= 2 * (1024 * 1024)) {
            response = {
                mensagem: {
                    message: "É permitido apenas o envio de imagens menores que 2MB!",
                    status: false
                }
            };

            return response;
        }

        formData.append("file", image.target.files[0]);

        // A resposta é retornada como ResponseText
        response = await Promise.resolve(
            $.ajax({
                url: "/api/redes/set_image_network",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                mimeType: "application/x-www-form-urlencoded",
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

                                callLoaderAnimation(
                                    "Enviando Imagem... " + percentComplete + "% "
                                );

                                /* faz alguma coisa durante o progresso do upload */
                            },
                            false
                        );
                    }
                    return myXhr;
                }

            })
        );

        return JSON.parse(response);
    }
};
