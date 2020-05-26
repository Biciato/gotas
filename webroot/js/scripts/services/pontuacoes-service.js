/**
 * Arquivo de services para Pontuações
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-26
 */
var pontuacoesService = {
    /**
     * Obtem pontuação
     * Chama serviço rest que retorna a quantidade de pontos que usuário possui em determinada rede
     *
     * @param {Integer} networkId Id de rede
     * @param {Integer} userId Id de Usuário
     * @returns {resumo_gotas} Resumo de Pontuações de Usuário ({ total_gotas_adquiridas: floor, total_gotas_utilizadas: floor, total_gotas_expiradas: floor, saldo: floor})
     * returns {\App\Model\Entity\resumo_gotas} Resumo de Pontuações de Usuário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-26
     */
    getUserPoints: async function (networkId, userId) {
        if (networkId === undefined) {
            throw "Selecione uma Rede antes de continuar!";
        }

        if (userId === undefined) {
            throw "Selecione um Usuário antes de continuar!";
        }

        var dataToSend = {
            redes_id: networkId,
            usuarios_id: userId
        };

        let response = await Promise.resolve($.ajax({
            type: "POST",
            url: "/api/pontuacoes/get_pontuacoes_rede",
            data: dataToSend,
            dataType: "JSON"
        }));

        return response.resumo_gotas;
    }
};
