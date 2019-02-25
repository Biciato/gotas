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


$this->Breadcrumbs->add('Início', array('controller' => 'pages', 'action' => 'display'));
$this->Breadcrumbs->add('Adicionar Conta', array(), array('class' => 'active'));

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Usuarios/left_menu', ['controller' => 'pages', 'action' => 'display', 'mode' => 'view']) ?>
<div class="usuarios index col-lg-9 col-md-10 columns content">

    <legend>
        <?= __("Administrar usuário") ?>
    </legend>

<div class="form-group row">

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

                    <form action="/usuarios/administrarUsuario" method="post">
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <!-- <label for="tipo_perfil">Tipo de Perfil</label>
                                <select name="tipo_perfil" id="tipo_perfil" class="form-control">
                                    <option value><?= "Todos" ?> </option>
                                    <?php foreach ($perfisUsuariosList as $key => $value) : ?>
                                        <option value="<?php echo $key ?>" ><?php echo $value ?></option>
                                    <?php endforeach; ?>
                                </select> -->
                                <?= $this->Form->input(
                                    'tipo_perfil',
                                    array(
                                        'type' => 'select',
                                        'id' => 'tipo_perfil',
                                        'label' => 'Tipo de Perfil',
                                        "empty" => "Todos",
                                        'options' => $perfisUsuariosList,
                                        'class' => 'form-control col-lg-2'
                                    )
                                ) ?>

                            </div>
                            <div class="col-lg-5">
                                <label for="nome">Nome</label>
                                <input type="text"
                                    id="nome"
                                    class="form-control"
                                    placeholder="Nome" />
                            </div>
                            <div class="col-lg-4">
                                <label for="email">E-mail</label>
                                <input type="text"
                                    class="form-control"
                                    placeholder="Email" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-12 text-right">

                            <button type="submit"
                                class="btn btn-primary botao-pesquisar">
                                <span class="fa fa-search"></span>
                                Pesquisar
                            </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>

</div>

<table class="table table-striped table-hover">

    <thead>
        <th><?= $this->Paginator->sort('ClienteHasUsuario.Cliente.RedesHasCliente.Redes.nome', ['label' => 'Rede']) ?></th>
        <th><?= $this->Paginator->sort('ClienteHasUsuario.Cliente.nome_fantasia', ['label' => 'Loja/Posto']) ?></th>
        <th><?= $this->Paginator->sort('tipo_perfil', ['label' => 'Tipo de Perfil']) ?></th>
        <th><?= $this->Paginator->sort('nome') ?></th>
        <th><?= $this->Paginator->sort('email') ?></th>
        <th class="actions">
            <?= __('Ações') ?>
            <div class="btn btn-xs btn-default right-align call-modal-how-it-works"
                data-toggle="modal"
                data-target="#modalLegendIconsSave"
                target-id="#legenda-icones-acoes">
                    <span class="fa fa-book">
                    </span>
                    Legendas
            </div>
        </th>
    </thead>

    <tbody>


        <?php foreach ($usuarios as $key => $usuario) : ?>

            <tr>
                <td><?= h($usuario["cliente_has_usuario"]["cliente"]["redes_has_cliente"]["rede"]["nome_rede"]) ?></td>
                <td><?= h($usuario["cliente_has_usuario"]["cliente"]["nome_fantasia"]) ?></td>
                <td><?= h($this->UserUtil->getProfileType($usuario["tipo_perfil"])) ?></td>
                <td><?= h($usuario->nome) ?></td>
                <td><?= h($usuario->email) ?></td>
                <td class="actions" style="white-space:nowrap">
                    <?= $this->Html->link(
                        __('{0} Gerenciar', $this->Html->tag('i', '', array('class' => 'fa fa-gears'))),
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
                                        "clientesId" => $usuario["cliente_has_usuario"]["clientes_id"],
                                        'usuariosId' => $usuario["id"],
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
