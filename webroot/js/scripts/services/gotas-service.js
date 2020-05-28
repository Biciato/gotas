/**
 * Arquivo de services para Gotas
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-19
 */
var gotasService = {

    /**
     * Cadastra novos Produtos conforme lista obtida da SEFAZ
     *
     * @param {Integer} clientesId Id de Clientes
     * @param {Gotas} gotas Lista de Gotas (id, importar, nome_parametro, multiplicador_gota)
     * @returns Promise|false
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-21
     */
    setGotasCliente: async function (clientesId, gotas) {
        let dataSend = {
            clientes_id: clientesId,
            gotas: gotas
        }

        return Promise.resolve(
            $.ajax({
                type: "POST",
                url: "/api/gotas/set_gotas_clientes",
                data: dataSend,
                dataType: "JSON"
            })
        );
    }
};
