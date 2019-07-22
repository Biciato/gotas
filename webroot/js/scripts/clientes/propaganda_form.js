/**
 * @author Gustavo Souza Gonçalves
 * @since 2018/08/06
 * @
 *
 */

$(document).ready(function () {
    // ------------------------------------------------------------------
    // Métodos de inicialização
    // ------------------------------------------------------------------


    var image = $(".img-crop");

    var cropper = image.data("cropper");


    $("#propaganda_img").on("change", function (image) {

        if (image.target.files[0].type != "image/jpeg" && image.target.files[0].type != "image/jpg" && image.target.files[0].type != "image/png") {
            callModalError("Somente arquivos de extensão .jpg, .jpeg ou .png são suportados!");
        } else {
            var formData = new FormData();

            formData.append("file", image.target.files[0]);

            $.ajax({
                url: "/api/clientes/envia_imagem_propaganda",
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

                        if (result != undefined && result.filesUploaded != undefined && result.filesUploaded.length > 0) {

                            result.filesUploaded.forEach(element => {
                                arquivo = element;
                            });

                            if (arquivo != undefined) {

                                $(".img-crop").attr("src", arquivo.path);
                                $(".img-upload").val(arquivo.file);

                                var image = $(".img-crop");

                                $(".img-crop").cropper('destroy');
                                image.cropper({
                                    // aspectRatio: 1 / 1,
                                    aspectRatio: 8.57,
                                    preview: ".img-crop-preview",
                                    autoCrop: true,
                                    dragDrop: true,
                                    movable: true,
                                    resizable: true,
                                    zoomable: false,
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
        }
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
