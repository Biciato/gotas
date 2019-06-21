<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\PontuacoesComprovante $pontuacoesComprovante
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Pontuacoes Comprovante'), ['action' => 'edit', $pontuacoesComprovante->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Pontuacoes Comprovante'), ['action' => 'delete', $pontuacoesComprovante->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pontuacoesComprovante->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Pontuacoes Comprovantes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pontuacoes Comprovante'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="pontuacoesComprovantes view large-9 medium-8 columns content">
    <h3><?= h($pontuacoesComprovante->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Nome Download') ?></th>
            <td><?= h($pontuacoesComprovante->nome_download) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($pontuacoesComprovante->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Clientes Id') ?></th>
            <td><?= $this->Number->format($pontuacoesComprovante->clientes_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Usuarios Id') ?></th>
            <td><?= $this->Number->format($pontuacoesComprovante->usuarios_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Insert') ?></th>
            <td><?= h($pontuacoesComprovante->audit_insert->format('d/m/Y H:i:s')) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Audit Update') ?></th>
            <td><?= h(isset($pontuacoesComprovante->audit_update) ? $pontuacoesComprovante->audit_update->format('d/m/Y H:i:s') : null) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Conteudo') ?></h4>
        <?= $this->Text->autoParagraph(h($pontuacoesComprovante->conteudo)); ?>
    </div>
</div>
