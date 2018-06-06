<?php

/**
 * @about: view exclusiva para conteúdo do tipo 'buscar', que exibirá os resultados da pesquisa
 * @author: Gustavo Souza Gonçalves
 * @file: \src\Template\Element\modal_buscar_results.ctp
 * @date: 24/01/2018
 * 
 * --------------------------------------------------------------------------------
 * Version list:
 * @version: 0.1
 * @notes: Comportamento como diálogo 
 * 
 */

 use Cake\Core\Configure;
   

?>

<div id="modal-show-network" class="modal fade modal_show_network" role="dialog">
    <div class="modal-dialog modal-lg">

        <div class="modal-content">
            
            <div class="modal-header ">
                <img class="network_image_content"/>
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title success-message"><span class="modal-title-content">Atenção</span></h4> -->
            </div>
            <div class="modal-body">
                
                <div class="modal-body-table-content">
                    <img class="modal-body-table-content-image"/>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Form para alterar a url de destino -->
                <form method="post" name="post-confirm" class="post_confirm"></form>
              

                <?= $this->Html->tag('div',
                'Conferir Rede', 
                [
                    'class' => 'btn btn-danger submit_button', 
                    'id' => 'submit_button'
                ])?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>

    </div>
</div>


<?php if (Configure::read('debug')): ?>
    <?= $this->Html->css('styles/pages/modal_buscar_results') ?>
<?php else: ?>
    <?= $this->Html->css('styles/pages/modal_buscar_results.min') ?>
<?php endif;?>

<?= $this->fetch('css') ?>