var esqueci_minha_senha =
{
init: function()
    {
    var self = this;
    $(document).on('click', "#btn-recuperar-senha", self.recuperarSenha);
    return this;
    },
recuperarSenha: function(e)
    {
    e.preventDefault();
    $.ajax(
        {
        url: '/api/usuarios/esqueci_minha_senha',
        data: $("#form-recuperar-senha").serialize(),
        dataType: 'JSON',
        method: 'POST',
        success: function(resposta)
            {
            if(resposta.message.includes('não encontrado'))
                {
                    toastr.error(resposta.message);

                }
            else
                {
                    toastr.success(resposta.message);
                setTimeout(function()
                    {
                    window.location.href = '/usuarios/login';
                    }, 1000);
                }
            }
        });
    }
};
$(document).ready(function()
{
esqueci_minha_senha.init();
})
