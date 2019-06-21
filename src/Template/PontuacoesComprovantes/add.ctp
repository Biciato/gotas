<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Pontuacoes Comprovantes'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="pontuacoesComprovantes form large-9 medium-8 columns content">
    <?= $this->Form->create($pontuacoesComprovante) ?>
    <fieldset>
        <legend><?= __('Add Pontuacoes Comprovante') ?></legend>
        <?php
            echo $this->Form->control('clientes_id');
            echo $this->Form->control('usuarios_id');
            echo $this->Form->control('conteudo');
            echo $this->Form->control('nome_download');
            echo $this->Form->control('audit_insert', ['empty' => true]);
            echo $this->Form->control('audit_update', ['empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('{0} Salvar',
        $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
        [
            'class' => 'btn btn-primary',
            'escape' => false
        ]
    
    ) ?>
    <?= $this->Form->end() ?>
</div>
