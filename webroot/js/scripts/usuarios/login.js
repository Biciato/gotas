var login = {
    init: function () {
        var self = this;
        $(document).on('click', '#btn-login', self.login);
        return this;
    },
    login: function (e) {
        e.preventDefault();
        $.ajax({
            url: '/api/usuarios/login',
            data: $("#login-form").serialize(),
            method: 'POST',
            dataType: 'JSON',
            success: function (resposta) {
                if (resposta.success === true) {
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
