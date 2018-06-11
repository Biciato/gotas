$(document).ready(function () {
    $("#preco_padrao").maskMoney();
    $("#preco_padrao").attr('maxlength', 10);

    var editMode = $("#edit_mode").val();

    var nome = $("#nome").val();

    var equipamentoRTIPadrao = $("#genero_brindes_id").val();
    /**
     * Verifica se está em edição e se o brinde sendo editado é banho.
     * Se for, seleciona um índice correspondente e não permite alterar o nome
     */
    if (editMode == 1 && equipamentoRTIPadrao <= 4) {
        $("#nome").attr('readonly', true);
        $("#genero_brindes_id").attr("readonly", true);
        $("#genero_brindes_id").attr("disabled", true);
        $("#tempo_rti_shower").attr('readonly', false);

        $("#ilimitado").attr('checked', true);
        $("#ilimitado").attr('disabled', true);
    } else if (editMode == 1) {
        $("#genero_brindes_id").attr("readonly", true);
        $("#genero_brindes_id").attr("disabled", true);
        $("#tempo_rti_shower").attr('readonly', true);

    }

    $("#genero_brindes_id").on("change", function (obj) {


        var nome = (this.value != undefined && this.value.length > 0) ? $("#genero_brindes_id option:selected").text() : "";

        $("#nome").val(nome);

        if (this.value != undefined && this.value <= 4 && this.value.length > 0) {
            $("#tempo_rti_shower").attr('readonly', false);

            $("#ilimitado").attr('checked', true);
            $("#ilimitado").attr('disabled', true);
        } else {
            $("#tempo_rti_shower").attr('readonly', true);
            $("#ilimitado").attr('checked', false);
            $("#ilimitado").attr('disabled', false);
        }
    })
    $("#tempo_rti_shower").on('blur', function () {

        if ($("#genero_brindes_id").val() <= 4) {
            if (this.value > 20) {
                this.value = 20;
            } else if (this.value < 0) {
                this.value = 0;
            }

            var nome = $("#genero_brindes_id option:selected").text() + this.value + " minutos";

            $("#nome").val(nome);

        }
    });

    $("#equipamento_rti_shower").on('change', function () {

        if (this.checked) {
            $("#nome").attr('readonly', true);
            $("#nome").val("Smart Shower Tempo de Banho: ");

            $("#tempo_rti_shower").attr('readonly', false);

            $("#ilimitado").attr('checked', true);
            $("#ilimitado").attr('disabled', true);
        } else {
            $("#nome").attr('readonly', false);
            $("#tempo_rti_shower").attr('readonly', true);
            $("#tempo_rti_shower").val(null);
            $("#ilimitado").attr('checked', false);
            $("#ilimitado").attr('disabled', false);
        }
    });

    // Upload de Imagem

    $("#nome-img").on("change", function (image) {

        var formData = new FormData();

        formData.append("file", image.target.files[0]);

        $.ajax({
            url: "/Brindes/enviaImagemBrinde",
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
            complete: function (e) {
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

                            $(".img-crop").attr("src", arquivo.path);
                            $(".img-upload").val(arquivo.file);

                            var image = $(".img-crop");

                            $(".img-crop").cropper('destroy');
                            image.cropper({
                                aspectRatio: 16 / 9,
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
