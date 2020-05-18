var usuariosService = {


    getPerfisList: async () => {
        let dataRequest = {};
        let obj = await Promise.resolve(
            $.ajax({
                type: "GET",
                url: "/api/usuarios/carregar_tipos_perfil",
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

        return obj.source;
    },

};
