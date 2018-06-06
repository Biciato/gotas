<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\RedesHasClientesAdministradore $redesHasClientesAdministradore
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Redes Has Clientes Administradore'), ['action' => 'edit', $redesHasClientesAdministradore->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Redes Has Clientes Administradore'), ['action' => 'delete', $redesHasClientesAdministradore->id], ['confirm' => __('Are you sure you want to delete # {0}?', $redesHasClientesAdministradore->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Redes Has Clientes Administradores'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Redes Has Clientes Administradore'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="redesHasClientesAdministradores view large-9 medium-8 columns content">
    <h3><?= h($redesHasClientesAdministradore->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($redesHasClientesAdministradore->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Redes Has Clientes Id') ?></th>
            <td><?= $this->Number->format($redesHasClientesAdministradore->redes_has_clientes_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Usuarios Id') ?></th>
            <td><?= $this->Number->format($redesHasClientesAdministradore->usuarios_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($redesHasClientesAdministradore->audit_insert) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h($redesHasClientesAdministradore->audit_update) ?></td>
        </tr>
    </table>
</div>
