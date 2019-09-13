
<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/PontuacoesComprovantes/historico_pontuacoes.ctp
 * @date     08/01/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] <= (int)Configure::read('profileTypes')['WorkerProfileType']) {
    $this->Breadcrumbs->add('Cupons do Usuário', [], ['class' => 'active']);
} else {
    $this->Breadcrumbs->add('Meu Histórico de Cupons', [], ['class' => 'active']);
}

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../UsuariosHasBrindes/left_menu') ?>

<div class="redes form col-lg-9 col-md-8 columns content">

    <?php if ($usuarioLogado['tipo_perfil'] <= (int)Configure::read('profileTypes')['WorkerProfileType']) : ?>
        <legend>Cupons de Brindes do Usuário</legend>

    <?php else : ?>
        <legend>Meu Histórico de Cupons de Brindes</legend>

    <?php endif; ?>


    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('clientes_has_brindes_habilitados.brindes.nome', ['label' => 'Brinde']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantidade', ['label' => 'Quantidade']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('preco', ['label' => 'Preço (em Gotas)']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('data', ['label' => 'Data']) ?></th>

                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuariosHasBrindes as $key => $usuarioHasBrinde) : ?>
            <tr>
                <td>
                    <?= h($usuarioHasBrinde->brinde->nome) ?>
                </td>
                <td>
                    <?= $usuarioHasBrinde->brinde->quantidade ?>
                </td>
                <td>
                    <?= $usuarioHasBrinde->brinde->preco ?>
                </td>
                <td>
                    <?= $usuarioHasBrinde->data->format('d/m/Y H:i:s') ?>
                </td>
                <td class="actions" style="white-space:nowrap">
                    <?=
                    $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                        ),
                        [
                            'controller' => 'cupons',
                            'action' => 'ver_detalhes',
                            $usuarioHasBrinde->cupons_id
                        ],
                        [
                            'title' => 'Ver detalhes',
                            'class' => 'btn btn-default btn-xs',
                            'escape' => false
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
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
<div/>
