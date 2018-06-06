<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\PontuacoesPendente $pontuacoesPendente
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Pontuacoes Pendente'), ['action' => 'edit', $pontuacoesPendente->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Pontuacoes Pendente'), ['action' => 'delete', $pontuacoesPendente->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pontuacoesPendente->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Pontuacoes Pendentes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pontuacoes Pendente'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Clientes'), ['controller' => 'Clientes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Cliente'), ['controller' => 'Clientes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="pontuacoesPendentes view large-9 medium-8 columns content">
    <h3><?= h($pontuacoesPendente->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Cliente') ?></th>
            <td><?= $pontuacoesPendente->has('cliente') ? $this->Html->link($pontuacoesPendente->cliente->id, ['controller' => 'Clientes', 'action' => 'view', $pontuacoesPendente->cliente->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Usuario') ?></th>
            <td><?= $pontuacoesPendente->has('usuario') ? $this->Html->link($pontuacoesPendente->usuario->id, ['controller' => 'Usuarios', 'action' => 'view', $pontuacoesPendente->usuario->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($pontuacoesPendente->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Usuarios Id') ?></th>
            <td><?= $this->Number->format($pontuacoesPendente->usuarios_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Data') ?></th>
            <td><?= h($pontuacoesPendente->data) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($pontuacoesPendente->audit_insert->format('d/m/Y H:i:s')) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h(isset($pontuacoesPendente->audit_update) ? $pontuacoesPendente->audit_update->format('d/m/Y H:i:s') : null) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Conteudo') ?></h4>
        <?= $this->Text->autoParagraph(h($pontuacoesPendente->conteudo)); ?>
    </div>
</div>
