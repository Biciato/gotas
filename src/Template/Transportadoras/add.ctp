<?php

/**
 * @var \App\View\AppView $this
 */
?>
<?= $this->element('../Transportadoras/left_menu', ['controller' => 'transportadoras', 'action' => 'index', 'mode' => 'create']) ?>
<div class="transportadoras form col-lg-9 col-md-10 columns content">
    <?= $this->Form->create($transportadora) ?>
    <fieldset>
    
        <?= $this->element('../Transportadoras/transportadoras_form') ?>
        
    </fieldset>
    <div class="col-lg-12">
        <?= $this->Form->button(
            __('{0} Salvar', $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
            [
                'type' => 'button',
                'escape' => false,
            ]
        ) ?>
    </div>
    <?= $this->Form->end() ?>
</div>

<?= $this->Html->tag('i', true, ['id' => 'show_form', ['class' => 'hidden']]) ?>

<?= $this->fetch('script') ?>