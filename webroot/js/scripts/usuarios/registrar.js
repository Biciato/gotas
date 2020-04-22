var registrar = 
{
init: function()
  {
    var self = this;
    $(document).on('click', '#btn-registrar', self.registrar);
    self.mascararCampos(self.inputHandler);
    return this;
  },
mascararCampos: function(inputHandler)
  {
    var telMask = ['(99) 9999-99999', '(99) 99999-9999'];
    var tel = document.querySelector('#input-telefone');
    VMasker(tel).maskPattern(telMask[0]);
    tel.addEventListener('input', inputHandler.bind(undefined, telMask, 14), false);
    VMasker(document.querySelector('#input-cpf')).maskPattern('999.999.999-99');
  },
registrar: function(e)
  {
    e.preventDefault();
    $.ajax(
      {
        url: '/app_gotas/usuarios/registrar',
        data: $("#form-registrar").serialize(),
        dataType: 'JSON',
        method: 'POST',
        success: function(resposta)
          {
            if(resposta.success === true)
              {
                toastr.success('Usuário criado com sucesso!');
                setTimeout(function()
                  {
                    window.location.href = "/app_gotas/usuarios/login";
                  }, 1000);
              }
            else
              {
                toastr.error(resposta.message);
              }
          }
      })
  },
inputHandler: function(masks, max, event) 
  {
    var c = event.target;
    var v = c.value.replace(/\D/g, '');
    var m = c.value.length > max ? 1 : 0;
    VMasker(c).unMask();
    VMasker(c).maskPattern(masks[m]);
    c.value = VMasker.toPattern(v, masks[m]);
  }
};
$(document).ready(function()
{
registrar.init();
});