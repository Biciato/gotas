<?php
use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado["tipo_perfil"] == (int) Configure::read("profileTypes")["AdminDeveloperProfileType"])
{
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);

    $this->Breadcrumbs->add('Detalhes da Rede', ['controller' => 'redes', 'action' => 'ver_detalhes', $redes_id]);
}
else if ($usuarioLogado["tipo_perfil"] == (int) Configure::read("profileTypes")["AdminNetworkProfileType"])
{
    $this->Breadcrumbs->add('Usuarios Rede', ['controller' => 'Usuarios', 'action' => 'usuarios_rede']);
}

$this->Breadcrumbs->add('Administradores Regionais e Comuns', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

$user_is_admin = $usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']
    || Configure::read('profileTypes')['AdminNetworkProfileType']
    || Configure::read('profileTypes')['AdminRegionalProfileType'];

?>

<?= $this->element('../Usuarios/left_menu', ['add_user' => true, 'mode' => 'addUser', 'controller' => 'pages', 'action' => 'display']) ?>
    <div class="usuarios index col-lg-9 col-md-10 columns content">
        <legend>
            <?= __("Administradores Regionais / Comuns") ?>
        </legend>

        <?php if ($user_is_admin) : ?>
            <?= $this->element('../Usuarios/filtro_usuarios_redes', ['controller' => 'usuarios', 'action' => 'administradores_regionais_comuns', 'id' => $redes_id, 'show_filiais' => false, 'filter_redes' => true, 'unidades_ids' => $unidades_ids]) ?>
        <?php else : ?>
            <?= $this->element('../Usuarios/filtro_usuarios', ['controller' => 'usuarios', 'action' => 'administradores_regionais_comuns', 'id' => $redes_id, 'show_filiais' => false, 'filter_redes' => true]) ?>
        <?php endif; ?>
            <table class="table table-striped table-hover table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>
                            <?= $this->Paginator->sort('tipo_perfil', ['label' => 'Tipo de Perfil']) ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('nome') ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('cpf', ['label' => 'CPF']) ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('Count', ['label' => 'Qte. Unidades Administrando']) ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('doc_estrangeiro', ['label' => 'Doc. Estrangeiro']) ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('sexo') ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('data_nasc', ['label' => 'Data de Nascimento']) ?>
                        </th>
                        <th>
                            <?= $this->Paginator->sort('email', ['label' => 'E-mail']) ?>
                        </th>
                        <th class="actions">
                            <?= __('Ações') ?>
                            <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario) : ?>

                    <?php
                    if (isset($usuario['usuario'])) {
                        $usuario = $usuario['usuario'];
                    }
                    ?>
                    <tr>
                        <td>
                            <?= h($this->UserUtil->getProfileType($usuario->tipo_perfil)) ?>
                        </td>
                        <td>
                            <?= h($usuario->nome) ?>
                        </td>
                        <td>
                            <?= h($this->NumberFormat->formatNumberToCpf($usuario->cpf)) ?>
                        </td>
                        <td>
                            <?php
                            echo count($usuario->clientes_has_usuarios)

                            ?>
                        </td>
                        <td>
                            <?= h($usuario->doc_estrangeiro) ?>
                        </td>
                        <td>
                            <?= h($this->UserUtil->getGenderType($usuario->sexo)) ?>
                        </td>
                        <td>
                            <?= h($this->DateUtil->dateToFormat($usuario->data_nasc, 'd/m/Y')) ?>
                        </td>
                        <td>
                            <?= h($usuario->email) ?>
                        </td>
                        <td class="actions" style="white-space:nowrap">

                            <?=
                            $this->Html->link(
                                __(
                                    '{0}',
                                    $this->Html->tag('i', '', ['class' => 'fa fa-gear'])
                                ),
                                [
                                    'controller' => 'clientes_has_usuarios',
                                    'action' => 'editar_administracao',
                                    $usuario->id
                                ],
                                [
                                    'class' => 'btn btn-xs btn-primary ',
                                    'escape' => false,
                                    'title' => 'Editar Permissão de Administrador Regional'
                                ]
                            )
                            ?>


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
            <p>
                <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?>
            </p>
        </center>
    </div>
</div>
