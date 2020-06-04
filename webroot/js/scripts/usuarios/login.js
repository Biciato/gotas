var login = {
    init: function () {
        var self = this;
        $(document).on('click', '#btn-login', self.login);
        return this;
    },
    login: function (e) {
        e.preventDefault();
        $(this).html('<div id="preloader" style="width: 100%; height: 2.5em; background: url(/img/loading_login.gif) #1ab394; background-size: contain; background-repeat: no-repeat; background-position: center center;"></div>')
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
                        localStorage.setItem("credentials", JSON.stringify(credentials));
                        if (resposta.mensagem.status) {
                            window.location.href = '/pages';
                        }
                    },
                    error: (resp) => {
                        resp.responseJSON.mensagem.errors.forEach((error) => toastr.error(error));
                        setTimeout(() => window.location.href = '/', 2000)
                    }
                })
            })
        })
    }
};

$(document).ready(function () {
    login.init();
})
