<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RedesUsuariosExcecaoAbastecimento $redesUsuariosExcecaoAbastecimento
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Redes Usuarios Excecao Abastecimento'), ['action' => 'edit', $redesUsuariosExcecaoAbastecimento->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Redes Usuarios Excecao Abastecimento'), ['action' => 'delete', $redesUsuariosExcecaoAbastecimento->id], ['confirm' => __('Are you sure you want to delete # {0}?', $redesUsuariosExcecaoAbastecimento->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Redes Usuarios Excecao Abastecimentos'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Redes Usuarios Excecao Abastecimento'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Redes'), ['controller' => 'Redes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Rede'), ['controller' => 'Redes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="redesUsuariosExcecaoAbastecimentos view large-9 medium-8 columns content">
    <h3><?= h($redesUsuariosExcecaoAbastecimento->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Rede') ?></th>
            <td><?= $redesUsuariosExcecaoAbastecimento->has('rede') ? $this->Html->link($redesUsuariosExcecaoAbastecimento->rede->nome_rede, ['controller' => 'Redes', 'action' => 'view', $redesUsuariosExcecaoAbastecimento->rede->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Usuario') ?></th>
            <td><?= $redesUsuariosExcecaoAbastecimento->has('usuario') ? $this->Html->link($redesUsuariosExcecaoAbastecimento->usuario->nome, ['controller' => 'Usuarios', 'action' => 'view', $redesUsuariosExcecaoAbastecimento->usuario->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($redesUsuariosExcecaoAbastecimento->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Adm Rede Id') ?></th>
            <td><?= $this->Number->format($redesUsuariosExcecaoAbastecimento->adm_rede_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Quantidade Dia') ?></th>
            <td><?= $this->Number->format($redesUsuariosExcecaoAbastecimento->quantidade_dia) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Validade') ?></th>
            <td><?= h($redesUsuariosExcecaoAbastecimento->validade) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($redesUsuariosExcecaoAbastecimento->audit_insert) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h($redesUsuariosExcecaoAbastecimento->audit_update) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Habilitado') ?></th>
            <td><?= $redesUsuariosExcecaoAbastecimento->habilitado ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
