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

<div id="modal-enable" class="modal fade" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Habilitar Registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Deseja habilitar o registro: <span id='nome-registro'></span> ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmar">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
 
    <div id="modal-disable" class="modal fade" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Desabilitar Registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Deseja desabilitar o registro: <span id='nome-registro'></span> ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmar">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

