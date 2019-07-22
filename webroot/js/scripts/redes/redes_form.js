/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\redes\redes_form.js
 * @date 2018/05/18
 *
 */

$(document).ready(function () {
    // ------------------------------------------------------------------
    // Métodos de inicialização
    // ------------------------------------------------------------------

    var image = $(".img-crop");

    var cropper = image.data("cropper");

    $("#custo_referencia_gotas").maskMoney();

    fixMoneyValue($("#custo_referencia_gotas"));

    $("#nome-img").on("change", function (image) {

        var formData = new FormData();

        var file = image.target.files[0];

        if (file.size >= (2 * (1024 * 1024))) {
            callModalError("É permitido apenas o envio de imagens menores que 2MB!");
            return;
        }

        formData.append("file", image.target.files[0]);

        $.ajax({
            url: "/Redes/enviaImagemRede",
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            mimeType: "application/x-www-form-urlencoded",
            cache: false,
            xhr: function () {  // Custom XMLHttpRequest
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) { // Avalia se tem suporte a propriedade upload
                    myXhr.upload.addEventListener('progress', function (event) {
                        var percentComplete = event.loaded / event.total;
                        percentComplete = parseInt(percentComplete * 100);
                        console.log(percentComplete);

                        callLoaderAnimation("Enviando Imagem... " + percentComplete + "% ");

                        /* faz alguma coisa durante o progresso do upload */
                    }, false);
                }
                return myXhr;
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
            },
            success: function (e) {
                // console.log(e);
            },
            error: function (e) {
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

                        result.arquivos.filesUploaded.forEach(element => {
                            arquivo = element;
                        });

                        if (arquivo != undefined) {

                            callLoaderAnimation("Carregando imagem...");

                            $(".img-crop").attr("src", arquivo.path);
                            $(".img-upload").val(arquivo.file);

                            var image = $(".img-crop");

                            $(".img-crop").on("load", function () {
                                closeLoaderAnimation();
                            });

                            $(".img-crop").cropper('destroy');
                            image.cropper({
                                // aspectRatio: 1/1,
                                preview: ".img-crop-preview",
                                autoCrop: true,
                                dragDrop: true,
                                movable: true,
                                resizable: true,
                                // zoomable: false,
                                zoomable: true,
                                crop: function (event) {
                                    coordenadas(event.detail);
                                },
                            });

                            var cropper = image.data("cropper");
                        }
                    }
                }
                else {
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

    }
});
