<?php

/**
 * @description Arquivo para formulário de cadastro de usuários
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/meus_clientes.ctp
 * @date        28/08/2017
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Meus Clientes', array(), ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<nav class="col-lg-3 col-md-2" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
        <li><?= $this->Html->link(__('Novo {0}', ['Usuario']), ['action' => 'registrar']) ?></li>
    </ul>
</nav>
<div class="usuarios index col-lg-9 col-md-10 columns content">

    <legend>
        <?= __("Cadastro de Clientes (Total de {0} clientes)", $totalUsuariosRede) ?>
    </legend>


    <?= $this->element(
        '../Usuarios/filtro_usuarios',
        array(
            'controller' => 'Usuarios',
            'action' => 'meus_clientes',
            'show_filiais' => true,
            "fixarTipoPerfil" => true,
            "tipoPerfilFixo" => Configure::read("profileTypes")["UserProfileType"],
            "unidades_ids" => isset($unidades_ids) ? $unidades_ids : array()
        )
    ) ?>

    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('nome', ['label' => 'Nome']) ?></th>
                <th><?= $this->Paginator->sort('cpf', ['label' => 'CPF']) ?></th>
                <th><?= $this->Paginator->sort('sexo', ['label' => 'Sexo']) ?></th>
                <th><?= $this->Paginator->sort('email', ['label' => 'E-mail']) ?></th>
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes"><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario) : ?>
                <tr>
                    <td><?= h($usuario->nome) ?></td>
                    <td><?= h($this->NumberFormat->formatNumberToCPF($usuario->cpf)) ?></td>
                    <td><?= h($this->UserUtil->getGenderType((string) $usuario["sexo"])) ?></td>
                    <td><?= h($usuario->email) ?></td>
                    <td class="actions" style="white-space:nowrap">
                        <a href="/usuarios/detalhesUsuario/<?= $usuario["id"] ?>" class="btn btn-default btn-xs botao-navegacao-tabela" title="Ver">
                            <i class="fa fa-info-circle">
                            </i>
                        </a>

                        <?php if (($usuarioLogado['tipo_perfil'] >= PROFILE_TYPE_ADMIN_NETWORK && $usuarioLogado["tipo_perfil"] <= PROFILE_TYPE_MANAGER)) : ?>
                            <?=
                                $this->Html->link(
                                    __(
                                        '{0} ',
                                        $this->Html->tag('i', '', ['class' => 'fa fa-edit botao-navegacao-tabela'])
                                    ),
                                    [
                                        'action' => 'editar_usuario', $usuario->id
                                    ],
                                    [
                                        'title' => 'Editar',
                                        'class' => 'btn btn-primary btn-xs botao-navegacao-tabela',
                                        'escape' => false
                                    ]
                                )
                            ?>
                        <?php endif; ?>
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
</div>
