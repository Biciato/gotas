<?php
/**
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Usuários/senha_modal.ctp
  * @date     23/08/2017
  */
  use Cake\Core\Configure;
?>

<div class="modal fade senha-modal" id="senha-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                 <h4 class="modal-title" id="myModalLabel">Confirmar senha do usuário para emissão</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->input('current_password', ['label' => 'Senha do usuário', 'id' => 'current_password', 'type' => 'password', 'class' => 'form-control col-lg-1 pull-right']) ?>
            </div>

            <div class="modal-footer">
            <?= 
                $this->Html->tag(
                    'div', 
                    __('{0} Confirmar', 
                    $this->Html->tag('i', '', ['class' => 'fa fa-check'])), 
                    [
                        'class' => 'btn btn-primary btn-ok modal-confirm', 'escape' => false
                    ]
                ) 
            ?>

            <?= 
                $this->Html->tag(
                    'div',
                    __('{0} Fechar',
                    $this->Html->tag('i', '', ['class' => 'fa fa-close'])),
                    [
                        'class' => 'btn btn-danger', 
                        'data-dismiss' => 'modal',
                        'escape' => false
                    ]
                )
            ?>
            
            </div>
         </div>
     </div>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/usuarios/senha_modal') ?>
<?php else: ?> 
    <?= $this->Html->script('scripts/usuarios/senha_modal.min') ?>
<?php endif; ?>

<?= $this->fetch('script')?>