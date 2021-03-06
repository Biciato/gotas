<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/historico_brindes.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Histórico de Brindes', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../Cupons/left_menu', ['mode' => 'report', 'controller' => 'pages', 'action' => 'display']) ?>

<div class="col-lg-9 col-md-10 columns">
    <legend> <?= __("Histórico de Brindes") ?></legend>

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading  panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
                <div>
                    <span class="fa fa-search"></span>
                    Exibir / Ocultar Filtros
                </div>

            </div>
            <div id="filter-coupons" class="panel-collapse collapse in">
                <div class="panel-body">
                    <?= $this->Form->create('POST', array('url' => array('controller' => 'Cupons', 'action' => 'historico_brindes'))) ?>

                    <div class="form-group row">
                        <div class="col-lg-4">

                            <?= $this->Form->input(
                                'unidadeSelecionado',
                                [
                                    'type' => 'select',
                                    'id' => 'unidadeSelecionado',
                                    'label' => "Ponto de Atendimento",
                                    'empty' => "<Todos>",
                                    'options' => $unidadesAtendimento
                                ]
                            ) ?>
                        </div>

                        <div class="col-lg-4">
                            <?= $this->Form->input(
                                "brindeSelecionado",
                                array(
                                    "type" => "select",
                                    "id" => "brindeSelecionado",
                                    "label" => "Brindes",
                                    'empty' => "<Todos>",
                                    "value" => $brindeSelecionado,
                                    "options" => $brindes
                                )
                            );
                            ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $this->Form->input(
                                "nomeUsuarios",
                                array(
                                    "type" => "text",
                                    "id" => "nomeUsuarios",
                                    "label" => "Clientes",
                                    "placeholder" => "Informe o nome..."
                                )
                            ); ?>
                        </div>

                        <div class="col-lg-2">
                            <?= $this->Form->input("valorMinimo", array("id" => "valorMinimo", "type" => "text", "class" => "valores", "label" => "Valor Mínimo")) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $this->Form->input("valorMaximo", array("id" => "valorMaximo", "type" => "text", "class" => "valores", "label" => "Valor Máximo")) ?>
                        </div>

                        <div class="hidden">
                            <!-- <div > -->
                            <?= $this->Form->input("altdataInicio", array("id" => "altdataInicio")); ?>
                            <?= $this->Form->input("altdataFim", array("id" => "altdataFim")); ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $this->Form->input(
                                'dataInicio',
                                [
                                    'type' => 'text',
                                    'id' => 'dataInicio',
                                    'label' => 'Data de Início',
                                    // 'format' => 'd/m/Y',
                                    'format' => 'Y-m-d',
                                    'default' => $dataInicio,
                                    'class' => 'datepicker-input',
                                    'div' =>
                                    [
                                        'class' => 'form-inline',
                                    ],
                                ]
                            ) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $this->Form->input(
                                'dataFim',
                                [
                                    'type' => 'text',
                                    'id' => 'dataFim',
                                    'label' => 'Data de Fim',
                                    'format' => 'd/m/Y',
                                    'default' => $dataFim,
                                    'class' => 'datepicker-input',
                                    'div' =>
                                    [
                                        'class' => 'form-inline',
                                    ],
                                ]
                            ) ?>
                        </div>

                        <div class="col-lg-2 vertical-align">

                            <?= $this->Form->button(
                                "Pesquisar",
                                [
                                    'class' => 'btn btn-primary btn-block',
                                    'id' => 'search_button'
                                ]
                            ) ?>

                        </div>

                    </div>
                    <?= $this->Form->end(); ?>

                </div>

            </div>
        </div>
    </div>
    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th>
                    <?= $this->Paginator->sort('usuario.nome', ['label' => 'Cliente']) ?></th>
                <th>
                    <?= $this->Paginator->sort('brinde.nome', ['label' => 'Brinde']) ?></th>
                <th>
                    <?= $this->Paginator->sort('tipo_banho', ['label' => 'Tipo de Banho']) ?></th>
                <th>
                    <?= $this->Paginator->sort('valor_pago_gotas', ['label' => 'Valor Gotas']) ?></th>
                <th>
                    <?= $this->Paginator->sort('valor_pago_reais', ['label' => 'Valor Reais']) ?></th>
                <th>
                    <?= $this->Paginator->sort('data', ['label' => 'Data Impressão ']) ?></th>

                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes">
                        <span class=" fa fa-book"> Legendas</span>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cupons as $cupom) : ?>
                <tr>

                    <td>
                        <?= h($cupom->usuario->nome) ?>
                    </td>
                    <td>
                        <?= h($cupom->brinde->nome) ?>
                    </td>
                    <td>
                        <?= h($this->Tickets->getTicketShowerType($cupom->tipo_banho)) ?>
                    </td>
                    <td>
                        <?= h($this->Number->precision($cupom["valor_pago_gotas"], 2)); ?>
                    </td>
                    <td>
                        <?= h($this->Number->precision($cupom["valor_pago_reais"], 2)); ?>
                    </td>
                    <td>
                        <?= h($cupom->data->format('d/m/Y H:i:s')) ?>
                    </td>

                    <td class="actions" style="white-space:nowrap">
                        <?= $this->Html->link(
                            __(
                                '{0}',
                                $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                            ),
                            [
                                'action' => 'detalhes_ticket', $cupom->id
                            ],
                            [
                                'title' => 'Ver detalhes',
                                'class' => 'btn btn-default btn-xs',
                                'escape' => false
                            ]
                        ) ?>

                        <?php
                        // TODO: Deverá ser ajustado devido alteração do Tipo de brindes
                        $this->Html->tag(
                            'div',
                            __(
                                "{0}",
                                $this->Html->tag('i', '', ['class' => 'fa fa-print'])
                            ),
                            [
                                'title' => 'Reemitir',
                                'data-toggle' => 'modal',
                                'data-target' => '#reemitir-shower-modal',
                                'type' => 'button', 'class' => 'btn btn-primary btn-xs print-ticket',
                                'value' => __(
                                    "id={0},brindes_id={1},clientes_id={2},usuarios_id={3},data={4}",
                                    $cupom->id,
                                    $cupom->brindes_id,
                                    $cupom->clientes_id,
                                    $cupom->usuarios_id,
                                    $cupom->data->format('Y-m-d H:i:s')
                                )
                            ]
                        ) ?>


                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="paginator">
        <center>
            <ul class="pagination">
                <?= $this->Paginator->first('                        << ' . __('primeiro')) ?>
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape' => false]) ?>
                <?= $this->Paginator->numbers(['escape' => false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape' => false]) ?>
                <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
        </center>
    </div>


</div>

<?= $this->Html->tag('div', '', ['class' => 'temporary-info', 'style' => 'display: none;']) ?>

<?= $this->element('../Cupons/reimpressao_shower_modal') ?>
<?= $this->element('../Cupons/impressao_shower_layout') ?>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/cupons/historico_brindes') ?>
    <?= $this->Html->script('scripts/cupons/reimpressao_shower_modal') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/cupons/historico_brindes.min') ?>
    <?= $this->Html->script('scripts/cupons/reimpressao_shower_modal.min') ?>
<?php endif; ?>


<?= $this->fetch('script') ?>
