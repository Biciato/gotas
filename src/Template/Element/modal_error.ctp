<?php
/**
 * @about: view exclusiva para conteúdo do tipo 'erro'
 * @author: Gustavo Souza Gonçalves
 * @file: \src\Template\Element\modal_error.ctp
 * @date: 12/09/2017
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
  * 1 . Como executar:
  *
  * 1.1 - chame a seguinte função
  * callModalError(param);
  *
  * Onde param é o texto a ser exibido
  *
  */


?>

<div id="modal-error" class="modal fade modal-error" role="dialog">
  <div class="modal-dialog modal-lg">
    
    <div class="modal-content">
      <div class="modal-header ">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title error-message"><span class="modal-title-content">Atenção</span></h4>
      </div>
      <div class="modal-body">
        <p>Houve um erro:</p>
        <span class="modal-body-content"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
      </div>
    </div>

  </div>
</div>
