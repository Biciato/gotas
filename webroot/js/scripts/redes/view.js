/**
 * Arquivo de funcionalidades do template src/Templates/Redes/index.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-04-22
 */

var sammy = $.sammy.apps['#content-body'];

var rede = {
    // #region Functions
    init: function () {
        'use strict';
        var self = this;

        console.log(sammy);
        // $(document).on("click", ".redes-index #btn-search", self.getRedeInfo);
        return this;
    },
    /**
     * Atualiza tabela de dados
     */
    getRedeInfo: async function (id) {
        try {
            let response = await this.getRedeInfo(id);

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
    },
    //#region Services

    /**
     * Obtêm redes
     *
     * @param {String} nomeRede Nome da rede
     * @param {Boolean} ativado Rede Ativada
     * @param {Boolean} appPersonalizado Rede com Aplicativos Personalizados
     * @returns JSON data
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-22
     */
    getRedeInfo: function (idRede) {
        var dataRequest = {
            nome_rede: nomeRede,
        };

        if (ativado !== undefined && ativado !== null) {
            dataRequest.ativado = ativado;
        }

        if (appPersonalizado !== undefined && appPersonalizado !== null) {
            dataRequest.app_personalizado = appPersonalizado;
        }

        let url = `/api/redes/${idRede}`;

        return Promise.resolve(
            $.ajax({
                type: "GET",
                url: url,
                data: dataRequest,
                dataType: "JSON"
            }));
    }

    //#endregion
    //#endregion
};

$(document).ready(function () {
        'use strict';

        rede.init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
$(function () {


    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
