var redesServices = {
    /**
     * Obtem registro por Id
     *
     * @param {Integer} id Id
     * @returns $.Promise(\App\Model\Entity\Rede.php) Promise|Object Rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-07
     */
    getById: function (id) {
        return Promise.resolve(
            $.ajax({
                type: "GET",
                url: `/api/redes/${id}`,
                dataType: "JSON"
            })
        );
    },
    /**
     * Realiza inserção de uma nova rede
     *
     * @param {any} data
     * @returns $.Promise Promise jQuery
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-06
     */
    save: function (data) {
        return Promise.resolve(
            $.ajax({
                type: "POST",
                url: "/api/redes",
                data: data,
                dataType: "JSON"
            })
        );
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
