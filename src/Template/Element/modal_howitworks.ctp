<?php
/**
 * @about: view exclusiva para conteúdo do tipo 'how it works'
 * @author: Gustavo Souza Gonçalves
 * @file: \src\Template\Element\modal_howitworks.ctp
 * @date: 09/09/2017
 * 
 * --------------------------------------------------------------------------------
 * Version list:
 * @version: 0.1
 * @notes: Comportamento como diálogo 
 * 
 */

 /**
  * Breve explicação de como usar:
  *
  * 1 - Definindo o Modal
  * 1.1 - crie um div pai chamado modal-how-it-works-parent
  * 1.2 - Defina o id com o nome desejado
  * 1.3 - Título: 
  * dentro de modal-how-it-works-parent, coloque um div modal-how-it-works-title.
 
  * 1.4 - Conteúdo:
  * dentro de modal-how-it-works-parent, coloque um div modal-how-it-works-body. 
  * O conteúdo deste div será inserido no corpo do modal
  * 
  * * Aviso:
  * Este modal não aceita comportamento para comandos. Talvez em futuras versões
  * 
  * 2 . Como executar:
  *
  * 2.1 - Crie um botão (div, button) contendo a classe call-modal-how-it-works, e coloque um atributo id em target-id anexe um evento conforme o seguinte:
  * $(".call-modal-how-it-works").on('click', function(){ callHowItWorks(this); });
  *
  * Pronto, a função callHowItWorks definido em \webroot\js\scripts\pages\home.js irá fazer o restante
  */


?>

<div id="modalHowItWorks" class="modal fade modal-how-it-works" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title"><span class="modal-title-content"></span></h4>
      </div>
      <div class="modal-body">
        <p class="modal-body-content"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div>

  </div>
</div>

