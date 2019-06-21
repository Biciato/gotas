<?php

/**
 * @about: view exclusiva para conteúdo do tipo 'save'
 * @author: Gustavo Souza Gonçalves
 * @file: \src\Template\Element\modal_save.ctp
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
 * callModalSave();
 */


?>

<div id="modal-save" class="modal fade modal-save" role="dialog">
	<div class="modal-dialog modal-lg">
    
	<div class="modal-content">
		<div class="modal-header ">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title success-message">
				<span class="modal-title-content">Atenção</span>
			</h3>
		</div>
		<div class="modal-body">
			<p class="modal-body-content">Registro gravado com sucesso!</p>
			<div class="modal-body-table-content">
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Fechar</button>
		</div>
	</div>

  </div>
</div>

