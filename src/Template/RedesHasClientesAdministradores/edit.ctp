<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $redesHasClientesAdministradore->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $redesHasClientesAdministradore->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Redes Has Clientes Administradores'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="redesHasClientesAdministradores form large-9 medium-8 columns content">
    <?= $this->Form->create($redesHasClientesAdministradore) ?>
    <fieldset>
        <legend><?= __('Edit Redes Has Clientes Administradore') ?></legend>
        <?php
            echo $this->Form->control('redes_has_clientes_id');
            echo $this->Form->control('usuarios_id');
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
