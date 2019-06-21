<?php
/**
 * @about: view exclusiva para conteúdo do tipo 'confirm'
 * @author: Gustavo Souza Gonçalves
 * @file: \src\Template\Element\modal_confirm_entire_network.ctp
 * @date: 21/10/2017
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
  *  <?= $this->Html->link(__("{0} Definir Gotas da Matriz para Filiais",
  *      $this->Html->tag('i', '', ['class' => 'fa fa-warning'])),
  *      '#',
  *      [
  *          'class'=>"list-group-item-danger",
  *          'data-toggle'=> 'modal',
  *          'data-target' => '#modal-confirm-entire-network',
  *          'data-action'=> Router::url(
  *              [
  *                  'controller' => 'controller',
  *                  'action'=>'action',$id
  *              ]
  *          ), 'escape' => false
  *      ], false
  *  ) ?>
  */


?>

    <div id="modal-confirm-entire-network" class="modal fade modal-confirm-entire-network" role="dialog">
        <div class="modal-dialog modal-lg">

            <div class="modal-content">
                <div class="modal-header ">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title success-message"><span class="modal-title-content">Atenção</span></h4>
                </div>
                <div class="modal-body">
                    <p class="modal-body-content">
                    Prosseguir com a operação implicará na remoção das gotas configuradas em suas filiais. Deseja confirmar a ação sobre o registro?</p>
                </div>
                <div class="modal-footer">
                    <!-- Form para alterar a url de destino -->
                    <form method="post" name="post-confirm"></form>
                    
                    <?= $this->Html->tag('div',
                    'Confirmar', 
                    [
                        'class' => 'btn btn-danger', 'id' => 'submit_button'
                    ])?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>

        </div>
    </div>
