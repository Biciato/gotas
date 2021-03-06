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
    <div class="col-lg-12 text-right">
        <button type="submit"
            class="btn btn-primary save-button botao-confirmar">
            <i class="fa fa-save"></i>
            Salvar
        </button>

        <a href="/transportadoras/index"
            class="btn btn-danger botao-cancelar">
            <i class="fa fa-window-close"></i>
            Cancelar
        </a>
    </div>
    <?= $this->Form->end() ?>
</div>

<?= $this->Html->tag('i', true, ['id' => 'show_form', ['class' => 'hidden']]) ?>

<?= $this->fetch('script') ?>
