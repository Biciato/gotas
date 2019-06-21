<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RedesUsuariosExcecaoAbastecimento[]|\Cake\Collection\CollectionInterface $redesUsuariosExcecaoAbastecimentos
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$title = "Exceção de Abastecimento Diário para Usuários";
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, array(), array("class" => "active"));
echo $this->Breadcrumbs->render(array('class' => 'breadcrumb'));

$userIsAdmin = $usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType'];
$listUsersPendingApproval = $userIsAdmin;
?>

<?= $this->element("../RedesUsuariosExcecaoAbastecimentos/left_menu") ?>

<div class="redesUsuariosExcecaoAbastecimentos index col-lg-9 col-md-8 columns content">
    <h3><?= $title ?></h3>

    <!-- Filtro -->

    <div class="form-group">

        <div class="panel-group">
            <div class="panel panel-default">
                <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
                    <!-- <h4 class="panel-title"> -->
                    <div>
                        <span class="fa fa-search"></span>
                        Exibir / Ocultar Filtros
                    </div>

                    <!-- </h4> -->
                </div>
                <div id="filter-coupons" class="panel-collapse collapse in">
                    <div class="panel-body">

                        <form action="/redesUsuariosExcecaoAbastecimentos" method="post">


                            <div class="form-group row">

                                <div class="col-lg-4">
                                    <label for="nome">Nome</label>
                                    <input type="text" id="nome" name="nome" placeholder="Nome..." class="form-control" value="<?= $nome ?>">
                                </div>
                                <div class="col-lg-4">
                                    <?= $this->Form->input(
                                        'email',
                                        [
                                            'type' => 'text',
                                            'id' => 'email',
                                            'label' => 'Email',
                                            'class' => 'form-control col-lg-2',
                                            'placeholder' => "E-mail..."
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-lg-4">
                                    <?= $this->Form->input(
                                        'cpf',
                                        [
                                            'type' => 'text',
                                            'id' => 'cpf',
                                            'label' => 'CPF',
                                            'class' => 'form-control col-lg-2',
                                            'placeholder' => "CPF...",

                                        ]
                                    ) ?>
                                </div>

                            </div>

                            <div class="form-group row">
                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="btn btn-primary save-button botao-pesquisar">
                                        <i class="fa fa-search"></i>
                                        Pesquisar
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>


                </div>
            </div>
        </div>

    </div>

    <!-- Tabela de resultado -->

    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('redes_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('adm_rede_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('usuarios_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantidade_dia') ?></th>
                <th scope="col"><?= $this->Paginator->sort('validade') ?></th>
                <th scope="col"><?= $this->Paginator->sort('habilitado') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_insert') ?></th>
                <th scope="col"><?= $this->Paginator->sort('audit_update') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($redesUsuariosExcecaoAbastecimentos as $redesUsuariosExcecaoAbastecimento) : ?>
                <tr>
                    <td><?= $this->Number->format($redesUsuariosExcecaoAbastecimento->id) ?></td>
                    <td><?= $redesUsuariosExcecaoAbastecimento->has('rede') ? $this->Html->link($redesUsuariosExcecaoAbastecimento->rede->nome_rede, ['controller' => 'Redes', 'action' => 'view', $redesUsuariosExcecaoAbastecimento->rede->id]) : '' ?></td>
                    <td><?= $this->Number->format($redesUsuariosExcecaoAbastecimento->adm_rede_id) ?></td>
                    <td><?= $redesUsuariosExcecaoAbastecimento->has('usuario') ? $this->Html->link($redesUsuariosExcecaoAbastecimento->usuario->nome, ['controller' => 'Usuarios', 'action' => 'view', $redesUsuariosExcecaoAbastecimento->usuario->id]) : '' ?></td>
                    <td><?= $this->Number->format($redesUsuariosExcecaoAbastecimento->quantidade_dia) ?></td>
                    <td><?= h($redesUsuariosExcecaoAbastecimento->validade) ?></td>
                    <td><?= h($redesUsuariosExcecaoAbastecimento->habilitado) ?></td>
                    <td><?= h($redesUsuariosExcecaoAbastecimento->audit_insert) ?></td>
                    <td><?= h($redesUsuariosExcecaoAbastecimento->audit_update) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $redesUsuariosExcecaoAbastecimento->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $redesUsuariosExcecaoAbastecimento->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $redesUsuariosExcecaoAbastecimento->id], ['confirm' => __('Are you sure you want to delete # {0}?', $redesUsuariosExcecaoAbastecimento->id)]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>