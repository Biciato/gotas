<?php 

/**
 * @description Tela de reativar conta
 * 
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/reativar_conta.ctp
 * @date     28/07/2017
 */

 use Cake\Core\Configure;

?>

<div class="users form container ">

<?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Por favor informe os dados abaixo para reativar a conta') ?></legend>
        <?= $this->Form->control('data_nasc_display', ['label'=>'Data de Nascimento.', 'empty' => true, 'data-format' => 'dd/MM/yyyy', 'type' => 'text', 'id'=> 'data_nasc_display', 'placeholder' => 'Informe sua data de nascimento']);?>

        <?= $this->Form->hidden('data_nasc', ['id' => 'data_nasc'])?>
        <?= $this->Form->input('placa', ['label' => 'Placa de Veículo', 'placeholder' => 'Informe um veículo cadastrado anteriormente']) ?>


    </fieldset>

<?= $this->Form->button(__('Reativar')); ?>

<?= $this->Form->end() ?>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/usuarios/reativar_conta');?>
<?php else: ?> 
    <?= $this->Html->script('scripts/usuarios/reativar_conta.min');?>
<?php endif; ?>

<?= $this->fetch('script');