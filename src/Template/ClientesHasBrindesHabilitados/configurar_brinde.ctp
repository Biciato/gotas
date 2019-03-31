<?php

/**
 * @var \App\View\AppView $this
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesHabilitados/detalhes_brinde.ctp
 * @date     09/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] <= (int)Configure::read('profileTypes')['AdminRegionalProfileType']) {

    $this->Breadcrumbs->add(
        'Escolher Unidade para Configurar os Brindes',
        [
            'controller' => 'clientes_has_brindes_habilitados',
            'action' => 'escolher_unidade_config_brinde'
        ]
    );
}

$this->Breadcrumbs->add(
    'Configurar um Brinde de Unidade',
    [
        'controller' => 'clientes_has_brindes_habilitados',
        'action' => 'configurar_brindes_unidade', $clientes_id
    ]
);

$this->Breadcrumbs->add('Configurar Brinde', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element(
    '../ClientesHasBrindesHabilitados/left_menu',
    [
        'mode' => 'updateGiftPrice',
        'brinde' => $cliente_has_brinde_habilitado,

    ]
) ?>

<div class="clientesHasBrindeHabilitados view col-lg-9 col-md-8 columns content">
    <legend><?= h(__("Configurar Brinde {0}", $cliente_has_brinde_habilitado->brinde->nome)) ?></legend>

    <table class="table table-striped table-hover">
        <tr>
            <th scope="row"><?= __('Nome') ?></th>
            <td><?= $cliente_has_brinde_habilitado->brinde->nome ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tipo de Emissão') ?></th>
            <?php if (strlen($cliente_has_brinde_habilitado->tipo_codigo_barras) == 0) : ?>
                <td class="text-danger">
                    <strong>
                        <?= __("Tipo de emissão não configurada!") ?>
                    </strong>
                </td>
            <?php else : ?>
                <td>
                    <?= $cliente_has_brinde_habilitado->tipo_codigo_barras ?>
                </td>
            <?php endif; ?>
        </tr>

        <tr>
            <th scope="row">Tipo de Venda</th>
            <td><?= $cliente_has_brinde_habilitado["brinde"]["tipo_venda"]?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Preço Atual (em Gotas)') ?></th>
            <td><?=
                is_null($cliente_has_brinde_habilitado->brinde_habilitado_preco_atual) ? null :
                    $this->Number->precision($cliente_has_brinde_habilitado->brinde_habilitado_preco_atual->preco, 3)
                ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Preço Atual (R$ / Venda Avulsa)') ?></th>
            <td>R$ <?=
                is_null($cliente_has_brinde_habilitado->brinde_habilitado_preco_atual) ? "0,00" :
                    $this->Number->precision($cliente_has_brinde_habilitado->brinde_habilitado_preco_atual->valor_moeda_venda, 2)
                ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Última Alteração De Preço') ?></th>
            <td><?=
                is_null($cliente_has_brinde_habilitado->brinde_habilitado_preco_atual) || is_null($cliente_has_brinde_habilitado->brinde_habilitado_preco_atual->data_preco) ? null :
                    $cliente_has_brinde_habilitado->brinde_habilitado_preco_atual->data_preco->format('d/m/Y H:i:s') ?></td>
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
                <th><?= $this->Paginator->sort('valor_moeda_venda', ['label' => 'Preço (R$ / Venda Avulsa)']) ?></th>
                <th><?= $this->Paginator->sort('data_preco', ['label' => 'Data da Alteração']) ?></th>
                <th><?= $this->Paginator->sort('status_autorizacao', ['label' => 'Estado da Autorização']) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($historico_precos as $key => $value) : ?>
                <tr>
                    <td ><?= $this->Number->precision($value->preco, 3) ?></td>
                    <td ><?= __("R$ {0}", $this->Number->precision($value->valor_moeda_venda, 2)) ?></td>
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
