/**
 * Arquivo de services para SEFAZ
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-19
 */
var sefazService = {
    /**
     * Obtem detalhes do QR Code Informado via SEFAZ
     * @param {String} qrCode QR Code Sefaz
     * @returns {SEFAZ} Object
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-19
     */
    getDetailsQRCode: function (qrCode) {
        return Promise.resolve($.ajax({
            type: "GET",
            url: "/api/sefaz/get_nf_sefaz_qr_code",
            data: {
                qr_code: qrCode
            },
            dataType: "JSON"

        }));
    }
};
