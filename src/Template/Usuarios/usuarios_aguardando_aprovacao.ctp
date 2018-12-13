<?php 

/**
 * @description Tela de reativar conta
 * 
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Usuarios/usuarios_aguardando_aprovacao.ctp
 * @date     28/07/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'index']);
}

$this->Breadcrumbs->add('Usuários Aguardando Aprovação', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Usuarios/left_menu', ['controller' => 'usuarios', 'action' => 'index', 'mode' => 'view']) ?>
<div class="usuarios index col-lg-9 col-md-10 columns content">
    <legend><?= __("Usuários Aguardando Aprovacao") ?></legend>
    
    <div class="form-group">

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading panel-heading-sm text-center"
                data-toggle="collapse"
                href="#collapse1"
                data-target="#filter-coupons">
                <!-- <h4 class="panel-title"> -->
                    <div>
                        <span class="fa fa-search"></span>
                            Exibir / Ocultar Filtros
                    </div>

                <!-- </h4> -->
            </div>
            <div id="filter-coupons" class="panel-collapse collapse in">
                <div class="panel-body">

                    <?=
                    $this->Form->create('Post', array('url' => array('controller' => "usuarios", 'action' => "usuariosAguardandoAprovacao")))
                    ?>

                        <div class="form-group row">
                            
                            <div class="hidden">
                                <?= $this->Form->input(
                                    'tipo_perfil',
                                    [
                                        'type' => 'select',
                                        'id' => 'tipo_perfil',
                                        'label' => 'Tipo de Perfil',
                                        "empty" => "Todos",
                                        'options' => Configure::read("profileTypesTranslatedAdminNetwork"),
                                        "value" => Configure::read("profileTypes")["UserProfileType"],
                                        "disabled" => true,
                                        'class' => 'form-control col-lg-2'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-4">
                                <?= $this->Form->input(
                                    'nome',
                                    [
                                        'type' => 'text',
                                        'id' => 'nome',
                                        'label' => 'Nome',
                                        'class' => 'form-control'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= $this->Form->input(
                                    'email',
                                    [
                                        'type' => 'text',
                                        'id' => 'email',
                                        'label' => 'Email',
                                        'class' => 'form-control'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-2">
                                <?= $this->Form->input(
                                    'cpf',
                                    [
                                        'type' => 'text',
                                        'id' => 'cpf',
                                        'label' => 'CPF',
                                        'class' => 'form-control'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= $this->Form->input(
                                    'doc_estrangeiro',
                                    [
                                        'type' => 'text',
                                        'id' => 'doc_estrangeiro',
                                        'label' => 'Doc Estrangeiro',
                                        'class' => 'form-control'
                                    ]
                                ) ?>
                            </div>

                            <div class="col-lg-7">

                                <?php

                                if (isset($unidades_ids) && sizeof($unidades_ids) > 0) {

                                    echo $this->Form->input(
                                        'filtrar_unidade',
                                        [
                                            'type' => 'select',
                                            'id' => 'filtrar_unidade',
                                            'label' => " Filtrar por unidade ? ",
                                            'empty' => '<Todas>',
                                            'options' => $unidades_ids
                                        ]
                                    );
                                }
                                ?>
                            </div>

                            <div class="col-lg-12 text-right">
                                <button type="submit" 
                                    class="btn btn-primary save-button botao-pesquisar">
                                    <i class="fa fa-search"></i>
                                    Pesquisar
                                </button>
                            </div>

                    </div>

                <?= $this->Form->end() ?>

            </div>
        </div>
    </div>

</div>

    <table class=" table table - striped table - hover ">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('tipo_perfil', ['label' => 'Tipo de Perfil']) ?></th>
                <th><?= $this->Paginator->sort('nome', ['label' => 'Nome']) ?></th>
                <th><?= $this->Paginator->sort('cpf', ['label' => 'CPF']) ?></th>
                <th><?= $this->Paginator->sort('doc_estrangeiro', ['label' => 'Documento Estrangeiro']) ?></th>
                <th><?= $this->Paginator->sort('sexo', ['label' => 'Sexo']) ?></th>
                <th><?= $this->Paginator->sort('data_nasc', ['label' => 'Data de Nascimento']) ?></th>
                <th><?= $this->Paginator->sort('email', ['label' => 'E-mail']) ?></th>
                <th class=" actions ">
                    <?= __('Ações') ?>
                    <div class=" btn btn - xs btn - default right - align call - modal - how - it - works " data-toggle=" modal " data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                            </th>
                        </tr>
                    </thead>
                <tbody>
                <?php foreach ($usuarios as $usuario) : ?>
                <tr>
                    <td><?= h($this->UserUtil->getProfileType($usuario->tipo_perfil)) ?></td>
                    <td><?= h($usuario->nome) ?></td>
                    <td><?= h($this->NumberFormat->formatNumberToCPF($usuario->cpf)) ?></td>
                    <td><?= h($usuario->doc_estrangeiro) ?></td>
                    <td><?= h($this->UserUtil->getGenderType($usuario->sexo)) ?></td>
                    <td><?= h(isset($usuario->data_nasc) ? $usuario->data_nasc->format('d/m/Y') : "") ?></td>
                    <td><?= h($usuario->email) ?></td>
                    <td class="actions" style="white-space:nowrap">
                        <?= $this->Html->link(__('Ver Documento'), ['action' => 'aprovar_documento_usuario', $usuario->id], ['class' => 'btn btn-default btn-xs']) ?>
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