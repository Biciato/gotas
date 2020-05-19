var importSefazProducts = {

    /**
     * Realiza configuração de eventos dos campos da tela
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-12
     */
    configureEvents: function () {
        var self = this;

        $(document).off("click", "#qrcode-search-form #btn-search")
            .on("click", "#qrcode-search-form #btn-search", self.getQRCodeProducts);


    },
    getQRCodeProducts: async function (event) {
        let self = this;
        let qrCode = $("#qrcode-search-form #qr-code").val();
        console.log(qrCode);

        try {
            let response = await sefazService.getDetailsQRCode(qrCode);

            if (response === undefined || response === null || !response) {
                return false;
            }
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

        /**
         * success: function (response) {
                gerarTabelaGotas(response.data.sefaz.produtos.itens);
                redesSelectedItem = response.data.rede;
                redesNome.val(redesSelectedItem.nome_rede);
                clientesSelectedItem = response.data.cliente;
                clientesNome.val(clientesSelectedItem.nome_fantasia + " / " + clientesSelectedItem.razao_social);
            },
            error: function (response) {
                var mensagem = response.responseJSON.mensagem;
                callModalError(mensagem.message, mensagem.errors);
            }
         */

        return self;
    },
    /**
     * Método 'construtor'
     */
    init: function () {
        let self = this;
        document.title = "GOTAS - Importação de Produtos da SEFAZ";

        self.configureEvents();

        return self;
    }
}
