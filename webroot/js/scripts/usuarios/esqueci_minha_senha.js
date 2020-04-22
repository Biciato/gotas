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
        url: '/app_gotas/usuarios/esqueci_minha_senha',
        data: $("#form-recuperar-senha").serialize(),
        dataType: 'JSON',
        method: 'POST',
        success: function(resposta)
            {
            if(resposta.success === true)
                {
                toastr.success('Senha enviada com sucesso!');
                setTimeout(function()
                    {
                    window.location.href = '/app_gotas/usuarios/login';
                    }, 1000);
                }
            else
                {
                toastr.error(resposta.message);
                }
            }
        });
    }
};
$(document).ready(function()
{
esqueci_minha_senha.init();
})