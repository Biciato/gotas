<?php

/**
 * @description View para adicionar transportadoras de um usuário
 * @author      Gustavo Souza Gonçalves
 * @file        Template\Transportadoras\adicionar_transportadora_usuario_final.php
 * @date        19/02/2018
 *
 */


?>
<?= $this->element('../Pages/left_menu', ['item_selected' => 'atualizar_cadastro_cliente', 'mode_selected' => 'atualizar_cadastro_cliente_transportadoras']) ?>


<div class="transportadoras form col-lg-9 col-md-10 columns content">
    <?= $this->Form->create($transportadora) ?>
    <fieldset>
    
        <?= $this->element('../Transportadoras/transportadoras_form') ?>
        
    </fieldset>
    <div class="col-lg-12">
        <div class="col-lg-2">
            <?= $this->Form->button(
                __('{0} Salvar', $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
                [
                    'type' => 'submit',
                    'class' => 'btn btn-primary btn-block',
                    'escape' => false,
                ]
            ) ?>
        </div>

        <div class="col-lg-10">
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

<?= $this->Html->tag('i', true, ['id' => 'show_form', ['class' => 'hidden']]) ?>

<?= $this->fetch('script') ?>