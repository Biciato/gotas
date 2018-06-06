<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuários/reimpressao_shower_modal.ctp
 * @date     23/08/2017
 */
use Cake\Core\Configure;
?>

<div class="modal fade reemitir-shower-modal" id="reemitir-shower-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                 <h4 class="modal-title" id="myModalLabel">Confirmar informações para re-emissão</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-4">
                
                        <?= $this->Form->input('sexo', [
                            'id' => 'sexo',
                            'empty' => true,
                            'options' =>
                                [
                                '1' => 'Masculino',
                                '0' => 'Feminino'
                            ]
                        ]); ?>
                    </div>

                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'necessidades_especiais',
                            [
                                'label' => 'Portador de Nec. Especiais?',
                                'id' => 'necessidades_especiais',
                                'empty' => true,
                                'options' => [
                                    1 => 'Sim',
                                    0 => 'Não',
                                ]
                            ]
                        ) ?>

                    </div>
                    <div class="col-lg-4">
                        <?= $this->Form->input('current_password', ['type' => 'password', 'id' => 'current_password', 'label' => 'Senha do Usuário']) ?>
                    
                    </div>
                </div>
            </div>
            

            <div class="modal-footer">
            <?=
            $this->Html->tag(
                'div',
                __(
                    '{0} Confirmar',
                    $this->Html->tag('i', '', ['class' => 'fa fa-check'])
                ),
                [
                    'class' => 'btn btn-primary btn-ok modal-confirm', 'escape' => false
                ]
            )
            ?>

            <?=
            $this->Html->tag(
                'div',
                __(
                    '{0} Fechar',
                    $this->Html->tag('i', '', ['class' => 'fa fa-close'])
                ),
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
    <?= $this->Html->script('scripts/cupons/reimpressao_shower_modal') ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/cupons/reimpressao_shower_modal.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>