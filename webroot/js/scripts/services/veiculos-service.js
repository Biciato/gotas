/**
 * Arquivo de services para Veiculos
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-25
 */
var veiculosService = {
    /**
     * Obtem lista de usuários finais, filtrado para inserção de pontos ou obtenção de brindes
     *
     * @param {String} nome Nome do Usuário
     * @param {CPF} cpf CPF
     * @param {Telefone} telefone Telefone
     * @returns \App\Model\Entity\Veiculo[] Veículo
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-25
     */
    getUsuariosByVeiculo: async function (placa = undefined) {
        var url = "/api/veiculos/get_usuarios_by_veiculo";

        if (placa === undefined || placa.length < 7) {
            throw "Necessário informar toda a placa antes de continuar! Formato de Placa: AAA9A99";
        }

        var dataToSend = {
            placa: placa
        };

        let response = await Promise.resolve($.ajax({
            type: "GET",
            url: url,
            data: dataToSend,
            dataType: "JSON"
        }));

        return response.data.veiculo;
    }
};
