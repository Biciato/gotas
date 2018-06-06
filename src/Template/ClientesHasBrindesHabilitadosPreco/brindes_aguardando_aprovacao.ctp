<?php
/**
 * 
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BrindesHabilitadosPreco[]|\Cake\Collection\CollectionInterface $brinde_aguardando_autorizacao
 */
use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Brindes Aguardando Aprovacao de Preço', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>
<?= $this->element('../ClientesHasBrindesHabilitadosPreco/left_menu') ?>
<div class="brindeAguardandoAutorizacao index col-lg-9 col-md-8 columns content">
    <legend><?= __('Brindes Aguardando Aprovacao de Preço') ?></legend>
    <table class="table table-striped table-hover table-responsive table-condensed">
        <thead>
            <tr>
                <th scope="col">Brinde</th>
                <th scope="col">Loja</th>
                <th scope="col">Preço Padrão</th>
                <th scope="col">Preço Atual</th>

                <th scope="col">Data da Alteração do Preço </th>
                <th scope="col" class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" >
                        <span class=" fa fa-book"> Legendas</span>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($brindes_aguardando_autorizacao as $brinde_aguardando_autorizacao) : ?>
            <tr>
                <td><?= h($brinde_aguardando_autorizacao->clientes_has_brindes_habilitado->brinde->nome) ?></td>
                <td><?= h($brinde_aguardando_autorizacao->cliente->nome_fantasia) ?></td>
                <td><?= $this->Number->precision($brinde_aguardando_autorizacao->clientes_has_brindes_habilitado->brinde->preco_padrao, 2) ?></td>
                <td><?= $this->Number->precision($brinde_aguardando_autorizacao->preco, 2) ?></td>
                <td><?= h($brinde_aguardando_autorizacao->data_preco->format('d/m/Y H:i:s')) ?></td>
                <td class="actions">
                <?= $this->Html->link(
                    __(
                        '{0}',
                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                    ),
                    '#',
                    [
                        'class' => 'btn btn-xs btn-primary btn-confirm',
                        'title' => 'Habilitar',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-confirm-with-message',
                        'data-message' => __(Configure::read('messageQuestionAllowGiftPrice'), $brinde_aguardando_autorizacao->clientes_has_brindes_habilitado->brinde->nome),
                        'data-action' => Router::url(
                            [
                                'action' => 'permitir_preco_brinde', $brinde_aguardando_autorizacao->id
                            ]
                        ),
                        'escape' => false
                    ],
                    false
                ); ?>

                <?= $this->Html->link(
                    __(
                        '{0}',
                        $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                    ),
                    '#',
                    [
                        'class' => 'btn btn-xs btn-danger btn-confirm',
                        'title' => 'Habilitar',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-confirm-with-message',
                        'data-message' => __(Configure::read('messageQuestionDenyGiftPrice'), $brinde_aguardando_autorizacao->clientes_has_brindes_habilitado->brinde->nome),
                        'data-action' => Router::url(
                            [
                                'action' => 'negar_preco_brinde', $brinde_aguardando_autorizacao->id
                            ]
                        ),
                        'escape' => false
                    ],
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
</div>
