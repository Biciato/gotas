/**
 * Arquivo de services para Redes
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-19
 */
var redesService = {

    /**
     * Altera o estado de uma rede
     *
     * @param {int} id Id da Rede
     * @returns Promise|false Promise ou status de false da operação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-28
     */
    changeStatus: async function (id) {
        if (id === undefined || id === null) {
            throw "Necessário informar id da rede à ser alterado o status!";
        }

        return await Promise.resolve($.ajax({
            type: "PUT",
            url: `/api/redes/change-status/${id}`,
            dataType: "JSON"
        }));
    },

    /**
     * Remove uma rede
     *
     * @param {int} id Id da Rede
     * @returns Promise|false Promise ou status de false da operação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-28
     */
    delete: async function (id, password) {
        if (id === undefined || id === null) {
            throw "Necessário informar rede à ser apagada!";
        }

        let url = "/api/redes/" + id;

        let dataRequest = {
            password: password
        }

        return Promise.resolve(
            $.ajax({
                type: "DELETE",
                data: dataRequest,
                url: url,
                dataType: "JSON",
            }));
    },
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
    getById: async function (id) {
        let obj = await Promise.resolve(
            $.ajax({
                type: "GET",
                url: `/api/redes/${id}`,
                dataType: "JSON"
            })
        );
        if (obj === undefined || obj === null || !obj) {
            toastr.error(response.mensagem.message);
            throw "Registro não encontrado!";
        } else if (!obj.mensagem.status) {
            let msgs = [];
            let codes = [];

            obj.mensagem.errors.forEach(error => {
                msgs.push(error);
            });

            obj.mensagem.error_codes.forEach(error => {
                codes.push(error);
            });

            throw new Object({
                errors: msgs,
                errorCodes: codes
            });
        }

        return obj.data.rede;
    },
    /**
     * Obtem lista de redes
     *
     * @returns {Redes[]} Lista de redes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-15
     */
    getList: async () => {
        let dataRequest = {};
        let obj = await Promise.resolve(
            $.ajax({
                type: "GET",
                url: "/api/redes/get_redes_list",
                data: dataRequest,
                dataType: "JSON"
            })
        );

        if (obj === undefined || obj === null || !obj) {
            toastr.error(response.mensagem.message);
            throw "Registro não encontrado!";
        } else if (!obj.mensagem.status) {
            let msgs = [];
            let codes = [];

            obj.mensagem.errors.forEach(error => {
                msgs.push(error);
            });

            obj.mensagem.error_codes.forEach(error => {
                codes.push(error);
            });

            throw new Object({
                errors: msgs,
                errorCodes: codes
            });
        }

        return obj.data.redes;
    },
    /**
     * Obtem lista de Unidades da rede
     *
     * @returns {Unidades[]} Lista de Unidades da rede
     *
     * @author Leandro Biciato <leandro@aigen.com.br>
     * @since 1.2.3
     * @date 2020-06-09
     */
    getUnidadesList: async (id) => {
        let dataRequest = { filtros: { redes_id: id }, draw: '1'};
        let obj = await Promise.resolve(
            $.ajax({
                type: "GET",
                url: "/api/clientes",
                data: dataRequest,
                dataType: "JSON"
            })
        );

        if (obj === undefined || obj === null || !obj) {
            toastr.error(response.mensagem.message);
            throw "Registro não encontrado!";
        } else if (!obj.mensagem.status) {
            let msgs = [];
            let codes = [];

            obj.mensagem.errors.forEach(error => {
                msgs.push(error);
            });

            obj.mensagem.error_codes.forEach(error => {
                codes.push(error);
            });

            throw new Object({
                errors: msgs,
                errorCodes: codes
            });
        }

        return obj.data_table_source.data;
    },
    /**
     * Realiza inserção de uma nova rede
     *
     * @param {any} data
     * @returns Rede|generic Object
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-06
     */
    save: function (data) {
        let typeRequest = data.id !== undefined && data.id > 0 ? "PUT" : "POST";
        let id = data.id !== undefined && data.id > 0 ? data.id : "";
        let url = `/api/redes/${id}`;

        return Promise.resolve(
            $.ajax({
                type: typeRequest,
                url: url,
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
