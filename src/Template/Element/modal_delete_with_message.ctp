<?php
/**
 * @about: view exclusiva para conteúdo do tipo 'save'
 * @author: Gustavo Souza Gonçalves
 * @file: \src\Template\Element\modal_delete_with_message.ctp
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
  * <?= $this->Html->link(__('{0} Remover Atribuição',
  *     $this->Html->tag('i', '', ['class' => 'fa fa-trash']) ),
  *     '#',
  *     [
  *         'class'=>'btn btn-xs btn-danger btn-confirm',
  *         'data-toggle'=> 'modal',
  *         'data-target' => '#modal-delete-with-message',
  *         'data-message' => 'Deseja remover o usuário selecionado como Administrador?',
  *         'data-action'=> Router::url(
  *             [
  *                 'action' => 'desatribuir_administrador',
  *                 '?' =>
  *                 [
  *                     'matriz_id' => isset($cliente->matriz_id) ? $cliente->matriz_id : $cliente->id,
  *                     'cliente_id' => $cliente->id,
  *                     'usuario_id' => $usuario->id
  *                 ]
  *             ]
  *         ),
  *             'escape' => false
  *     ],
  * false
  * ); ?>
  */


?>

    <div id="modal-delete-with-message" class="modal fade modal-delete-with-message" role="dialog">
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
                    <form method="post" name="post-remove-binding"></form>
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
