<?php

/**
 * @description Ver detalhes de Usuário (view de administrador)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/administrar_usuario.ctp
 * @date        28/08/2017
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
use App\Custom\RTI\DebugUtil;

?>

<?= $this->element('../Usuarios/left_menu', ['controller' => 'pages', 'action' => 'display', 'mode' => 'view']) ?>
<div class="usuarios index col-lg-9 col-md-10 columns content">

    <legend>
        <?= __("Administrar usuário") ?>
    </legend>

<?= $this->element(
    '../Usuarios/filtro_usuarios',
    array('controller' => 'usuarios', 'action' => 'administrar_usuario'),
    array("perfisUsuariosList" => $perfisUsuariosList)

); ?>

<table class="table table-striped table-hover">

    <thead>
        <th><?= $this->Paginator->sort('ClienteHasUsuario.Cliente.RedesHasCliente.Redes.nome', ['label' => 'Rede']) ?></th>
        <th><?= $this->Paginator->sort('ClienteHasUsuario.Cliente.nome_fantasia', ['label' => 'Loja/Posto']) ?></th>
        <th><?= $this->Paginator->sort('ClienteHasUsuario.tipo_perfil', ['label' => 'Tipo de Perfil']) ?></th>
        <th><?= $this->Paginator->sort('nome') ?></th>
        <th><?= $this->Paginator->sort('cpf', ['label' => 'CPF']) ?></th>
        <th><?= $this->Paginator->sort('sexo') ?></th>
        <th><?= $this->Paginator->sort('data_nasc', ['label' => 'Data de Nascimento']) ?></th>
        <th><?= $this->Paginator->sort('email') ?></th>
        <th class="actions">
            <?= __('Ações') ?>
            <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
        </th>
    </thead>

    <tbody>


        <?php foreach ($usuarios as $key => $usuario) : ?>

            <tr>
                <td><?= h($usuario["cliente_has_usuario"]["cliente"]["redes_has_cliente"]["rede"]["nome_rede"]) ?></td>
                <td><?= h($usuario["cliente_has_usuario"]["cliente"]["nome_fantasia"]) ?></td>
                <td><?= h($this->UserUtil->getProfileType($usuario["cliente_has_usuario"]["tipo_perfil"])) ?></td>
                <td><?= h($usuario->nome) ?></td>
                <td><?= h($this->NumberFormat->formatNumberToCPF($usuario->cpf)) ?></td>
                <td><?= h($this->UserUtil->getGenderType($usuario->sexo)) ?></td>
                <td><?= h(isset($usuario->data_nasc) ? $usuario->data_nasc->format('d/m/Y') : "") ?></td>
                <td><?= h($usuario->email) ?></td>
                <td class="actions" style="white-space:nowrap">
                    <?= $this->Html->link(
                        __(
                            '{0} Gerenciar',
                            $this->Html->tag('i', '', array('class' => 'fa fa-gears'))
                        ),
                        '#',
                        array(
                            'class' => 'btn btn-xs btn-danger btn-confirm',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-confirm-with-message',
                            'data-message' => __("Você está prestes a utilizar o sistema conforme o usuário {0} selecionado. Deseja continuar?", $usuario->nome),
                            'data-action' => Router::url(
                                array(
                                    "controller" => "usuarios",
                                    'action' => 'iniciar_administracao_usuario',
                                    "?" =>
                                        array(
                                        // "clientes_id" => $usuario["cliente_has_usuario"]["cliente"]["id"],
                                        "clientes_id" => $usuario["cliente_has_usuario"]["clientes_id"],
                                        'usuarios_id' => $usuario["id"],
                                    )
                                )
                            ),
                            'escape' => false
                        ),
                        false
                    ); ?>
                </td>
            </tr>

        <?php endforeach; ?>
    </tbody>

</table>

    <div class="paginator">
        <center>
            <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('primeiro')) ?>
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape' => false]) ?>
                <?= $this->Paginator->numbers(['escape' => false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape' => false]) ?>
            <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
