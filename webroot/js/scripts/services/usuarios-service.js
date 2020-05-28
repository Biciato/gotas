/**
 * Arquivo de services para Usuário
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-19
 */
var usuariosService = {

    /**
     * Finaliza gerenciamento de usuário
     *
     * @returns Promise|false Promise ou status de false da operação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-19
     */
    finishManageUser: async function () {
        return await Promise.resolve(
            $.ajax({
                type: "POST",
                url: "/api/usuarios/finish_manage_user",
                dataType: "JSON"
            })
        );
    },

    /**
     * Obtem lista de usuários finais, filtrado para inserção de pontos ou obtenção de brindes
     *
     * @param {String} nome Nome do Usuário
     * @param {CPF} cpf CPF
     * @param {Telefone} telefone Telefone
     * @returns \App\Model\Entity\Usuario[] lista de Usuários
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-25
     */
    getUsuariosFinais: async function (nome = undefined, cpf = undefined, telefone = undefined) {
        var url = "/api/usuarios/get_usuarios_finais";

        var dataToSend = {};

        if (nome !== undefined) {
            dataToSend.nome = nome.trim();
        } else if (cpf !== undefined) {
            dataToSend.cpf = cpf.trim().replace(/\D/gm, "");
        } else if (telefone !== undefined) {
            dataToSend.telefone = telefone.trim().replace(/\D/gm, "");
        }

        let response = await Promise.resolve($.ajax({
            type: "GET",
            url: url,
            data: dataToSend,
            dataType: "JSON"
        }));

        return response.data.usuarios;
    },
    /**
     * Obtem lista de perfis
     *
     * @returns Promise|false Promise ou status de false da operação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-18
     */
    getPerfisList: async function () {
        let dataRequest = {};
        let obj = await Promise.resolve(
            $.ajax({
                type: "GET",
                url: "/api/usuarios/get_profile_types",
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

        return obj.data;
    },

    /**
     * Realiza troca de sessão do usuário atual com usuário alvo
     *
     * @param {Integer} id Id do usuário
     * @returns Promise|false Promise ou status de false da operação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-19
     */
    startManageUser: async function (id) {
        let dataRequest = {
            usuarios_id: id
        };

        return await Promise.resolve(
            $.ajax({
                type: "POST",
                url: "/api/usuarios/start_manage_user",
                data: dataRequest,
                dataType: "JSON"
            })
        );

    },

};
