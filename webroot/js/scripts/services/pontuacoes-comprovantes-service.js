/**
 * Arquivo de services para Pontuações Comprovantes
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-26
 */
var pontuacoesComprovantesService = {
    /**
     * Define pontuação
     * Define quantidade de pontos à adicionar/remover de usuário
     *
     * @param {Integer} networkId Id de Rede
     * @param {Integer} userId Id de Usuário
     * @param {Float} points Pontos
     * @returns Promise|false
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-26
     */
    setPointsUserManual: async function (networkId, userId, points) {
        let response = await Promise.resolve($.ajax({
            type: "POST",
            url: "/api/pontuacoes_comprovantes/set_gotas_manual_usuario",
            data: {
                redes_id: networkId,
                usuarios_id: userId,
                quantidade_gotas: points
            },
            dataType: "JSON"
        }));

        return response;
    }
};
