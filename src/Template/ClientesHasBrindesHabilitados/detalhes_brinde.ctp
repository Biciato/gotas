<?php

/**
 * @var \App\View\AppView $this
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesHabilitados/detalhes_brinde.ctp
 * @date     09/08/2017
 */


$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Brindes Habilitados de Unidades', ['controller' => 'clientes_has_brindes_habilitados', 'action' => 'meus_brindes_ativados']);

$this->Breadcrumbs->add('Brindes Habilitados da Loja', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element(
    '../ClientesHasBrindesHabilitados/left_menu',
    [
        'mode' => 'updateGiftPrice',
        'brinde' => $cliente_has_brinde_habilitado,
        'go_back_url' => [
            'controller' => 'clientes_has_brindes_habilitados',
            'action' => 'meus_brindes_ativados'
        ]
    ]
) ?>

<div class="clientesHasBrindeHabilitados view col-lg-9 col-md-8 columns content">
    <legend><?= h($cliente_has_brinde_habilitado->brinde->nome) ?></legend>
    <table class="table table-striped table-hover">
        <tr>
            <th scope="row"><?= __('Nome') ?></th>
            <td><?= $cliente_has_brinde_habilitado->brinde->nome ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Preço Atual (em Gotas)') ?></th>
            <td><?= $this->Number->precision($cliente_has_brinde_habilitado->brinde_habilitado_preco_atual->preco, 2) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Última Alteração De Preço') ?></th>
            <td><?= $cliente_has_brinde_habilitado->brinde_habilitado_preco_atual->data_preco->format('d/m/Y H:i:s') ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Fornecimento Ilimitado?') ?></th>
            <td><?= $this->Boolean->convertBooleanToString($cliente_has_brinde_habilitado->brinde->ilimitado) ?></td>
        </tr>

        <?php if (!($cliente_has_brinde_habilitado->brinde->ilimitado)) : ?>
        <tr>
            <th scope="row"><?= __('Estoque Atual') ?></th>
            <td><?= $cliente_has_brinde_habilitado->estoque[0] ?></td>
        </tr>

        <?php endif; ?>
        
    </table>

    <h4><legend>Histórico de preços</legend></h4>
    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('preco', ['label' => 'Preço (em Gotas)']) ?></th>
                <th><?= $this->Paginator->sort('data_preco', ['label' => 'Data da Alteração']) ?></th>
                <th><?= $this->Paginator->sort('status_autorizacao', ['label' => 'Estado da Autorização']) ?></th>
            </tr>
        </thead>    
        <tbody>
            <?php foreach ($historico_precos as $key => $value) : ?>
                <tr>
                    <td ><?= $this->Number->precision($value->preco, 2) ?></td>
                    <td><?= $value->data_preco->format('d/m/Y H:i:s') ?></td>
                    <td ><?= $this->Gift->formatGift($value->status_autorizacao) ?></td>
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
