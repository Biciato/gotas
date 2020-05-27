var login = {
    init: function () {
        var self = this;
        $(document).on('click', '#btn-login', self.login);
        return this;
    },
    login: function (e) {
        e.preventDefault();
        grecaptcha.ready(function() {
            // do request for recaptcha token
            // response is promise with passed token
            grecaptcha.execute('6Ld2BvwUAAAAAAWGBkCdCHfFoBex6MhQhVm4keNF', {action: 'login'}).then(function(token) {
                // add token to form
                $('#login-form').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                $.ajax({
                    url: '/api/usuarios/token',
                    data: $("#login-form").serialize(),
                    method: 'POST',
                    dataType: 'JSON',
                    success: function (resposta) {
                        let credentials = {
                            usuario: resposta.usuario,
                            cliente: resposta.cliente
                        };
                        sessionStorage.setItem("credentials", JSON.stringify(credentials));
                        if (resposta.mensagem.status) {
                            window.location.href = '/pages';
                        }
                    },
                    error: (resp) => toastr.error(resp.responseJSON.mensagem.errors[0])
                })
            })
        })
    }
};

$(document).ready(function () {
    login.init();
})
