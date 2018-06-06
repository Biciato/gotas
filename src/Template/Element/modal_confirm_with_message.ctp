<?php
/**
 * @about: view exclusiva para conteúdo do tipo 'save'
 * @author: Gustavo Souza Gonçalves
 * @file: \src\Template\Element\modal_confirm_binding.ctp
 * @date: 28/09/2017
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
  * 1.1 - O botão deve ter a seguinte estrutura similar:
  *  <?= $this->Html->link(
  *      $this->Html->tag('i', '', array('class' => 'fa fa-trash')) ,
  *      '#',
  *      array(
  *         'class'=>'btn btn-danger btn-confirm',
  *         'data-toggle'=> 'modal',
  *         'data-target' => '#modal-confirm-binding',
  *         'data-action'=> Router::url(
  *            ['action'=>'confirm_binding',$id]
  *         ),
  *         'escape' => false),
  *  false); ?>
  */


?>

    <div id="modal-confirm-with-message" class="modal fade modal-confirm-with-message" role="dialog">
        <div class="modal-dialog modal-lg">

            <div class="modal-content">
                <div class="modal-header ">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title success-message"><span class="modal-title-content">Atenção</span></h4>
                </div>
                <div class="modal-body">
                    <p class="modal-body-content"></p>
                </div>
                <div class="modal-footer">
                    <!-- Form para alterar a url de destino -->
                    <form method="post" name="post-confirm-with-message"></form>
                    <?= $this->Html->tag('div',
                        'Confirmar',
                        [
                        'class' => 'btn btn-primary', 'id' => 'submit_button'
                        ]
                    )?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>

        </div>
    </div>
