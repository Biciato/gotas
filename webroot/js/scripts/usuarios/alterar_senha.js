/**
 * Arquivo de funcionalidades do template src/Templates/Usuarios/alterar_senha.ctp
 *
 * @author Leandro Biciato <leandro@aigen.com.br>
 * @since 1.2.3
 * @date 2020-29-5
 */

 const usuariosAlterarSenha = {
    /**
     * 'Constructor'
     */
    init: function () {
        'use strict';
        var self = this;

        $('#btn-save').click(() => {
            $.ajax({
                url: '/api/usuarios/alterar_senha',
                type: 'POST',
                data: {
                    senha_antiga: $('#senha_antiga').val(),
                    nova_senha: $('#nova_senha').val(),
                    confirm_senha: $('#confirm_senha').val()
                },
                success: () => toastr.success('Senha alterada com sucesso !!!'),
                error: (resp) => {
                    resp.responseJSON.mensagem.errors.forEach(error => toastr.error(error))
                }
            })
        })
    }
 }
