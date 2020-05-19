var login = {
    init: function () {
        var self = this;
        $(document).on('click', '#btn-login', self.login);
        return this;
    },
    login: function (e) {
        e.preventDefault();
        $.ajax({
            url: '/api/usuarios/token',
            data: $("#login-form").serialize(),
            method: 'POST',
            dataType: 'JSON',
            success: function (resposta) {
                sessionStorage.setItem("credentials", resposta.usuario);
                if (resposta.mensagem.status) {
                    window.location.href = '/pages';
                } else {
                    toastr.error(resposta.message);
                }
            }
        })
    }
};
$(document).ready(function () {
    login.init();
})
