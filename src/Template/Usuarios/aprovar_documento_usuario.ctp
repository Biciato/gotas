<?php 

/**
 * @description Tela de reativar conta
 * 
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/aprovar_documento_usuario.ctp
 * @date     28/07/2017
 */

?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
	<ul class="nav nav-pills nav-stacked">
		<li class="active"><a><?= __('Ações') ?></a></li>
		<li><?= $this->Html->link(__('Voltar'), ['action' => 'usuarios_aguardando_aprovacao']) ?></li>
	</ul>
</nav>

<div class="usuarios index col-lg-9 col-md-10 columns content">

    <legend><?= h($usuario->nome) ?></legend>
        <?= $this->Form->create($usuario) ?>
        <table class="table table-striped table-hover">

            <tr>
                <th scope="row"><?= __('Nome') ?></th>
                <td><?= h($usuario->nome) ?></td>
            </tr>

            <tr>
                <th scope="row"><?= __('Data de Cadastro') ?></th>
                <td><?= h($usuario->audit_insert->format('d/m/Y H:i:s')) ?></td>
            </tr>

            <tr>
                <th scope="row"><?= __('CPF') ?></th>
                <td><?= h($usuario->cpf) ?></td>
            </tr>

            <tr>
                <th scope="row"><?= __('Documento Estrangeiro') ?></th>
                <td><?= h($usuario->doc_estrangeiro) ?></td>
            </tr>

            <tr>
                <th scope="row"><?= __('Foto capturada') ?></th>
                <td><?= $this->Html->image($usuario->foto_documento, ['width' => 580, 'height' => 430]) ?></td>
            </tr>

            <tr>
                <th>
                </th>
                <td>
            
                    <?= $this->Form->submit(__('Aprovar')) ?>
                    
                </td>
            </tr>
        </table>
<?= $this->Form->end() ?>
</div>