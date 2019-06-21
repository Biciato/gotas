<?php
/**
 * @about: view exclusiva para conteúdo do tipo 'save'
 * @author: Gustavo Souza Gonçalves
 * @file: \src\Template\Element\modal_validate.ctp
 * @date: 02/10/2017
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
  *  echo $this->Html->link(
  *      $this->Html->tag('i', '', array('class' => 'fa fa-trash'))* ,
  *      '#',
  *      array(
  *         'class'=>'btn btn-danger btn-confirm',
  *         'data-toggle'=> 'modal',
  *         'data-target' => '#modal-validate',
  *           'data-action'=> Router::url(
  *              [
  *                  'controller' => 'pontuacoes_comprovantes',
  *                  'action' => 'validate_coupon',$pontuacao->id
  *              ]
  *         'escape' => false),
  *  false);
  */


?>

    <div id="modal-validate" class="modal fade modal-validate" role="dialog">
        <div class="modal-dialog modal-lg">

            <div class="modal-content">
                <div class="modal-header ">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title success-message"><span class="modal-title-content">Atenção</span></h4>
                </div>
                <div class="modal-body">
                    <p class="modal-body-content">Deseja validar o registro?</p>
                </div>
                <div class="modal-footer">
                    <!-- Form para alterar a url de destino -->
                    <form method="post" name="post-validate"></form>
                    
                    <?= $this->Html->tag('div',
                    'Confirmar', 
                    [
                        'class' => 'btn btn-primary', 'id' => 'submit_button'
                    ])?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>