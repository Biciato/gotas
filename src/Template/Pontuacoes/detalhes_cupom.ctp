<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/detalhes_cupom.ctp
 * @date     28/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Cupons Emitidos', array("controller" => "pontuacoes", "action" => "cupons_minha_rede"));
$this->Breadcrumbs->add('Detalhes do Cupom Fiscal', array(), ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>
<?= $this->element('../Pontuacoes/left_menu', ['controller' => 'pontuacoes', 'action' => 'cupons_minha_rede', 'id' => $pontuacao->id, 'mode' => 'details']) ?>

<div class="col-lg-9 col-md-10 columns">

    <legend>
        <?= __("Detalhes do Cupom Fiscal") ?>
    </legend>

    <?php if (is_null($pontuacao->nome_img)) : ?>
        <?= $this->element('../Pontuacoes/tabela_descritivo_pontuacoes', ['pontos' => $pontuacao->pontuacoes]) ?>
        <?php else : ?>
        <div class="col-lg-6">
            <legend>Imagem da Captura</legend>
            <?= $this->Html->image($pontuacao->nome_img, ['alt' => 'Comprovante', 'class' => 'image-receipt']) ?>
        </div>
        <div class="col-lg-6">
            <legend>Dados da Captura</legend>

            <?= $this->element('../Pontuacoes/tabela_descritivo_pontuacoes', ['pontos' => $pontuacao->pontuacoes]) ?>
        </div>
    <?php endif; ?>

    <div class="form-group row">
    <div class="col-lg-12">



    <?php if ($pontuacao->requer_auditoria && !$pontuacao->auditado) : ?>
        <h4>Ações</h4>

        <table class="table table-striped table-hover">
            <tr>
                <th>
                    <?= __("Aprovar Auditoria") ?>
                </th>
                <td>
                    <?= $this->Html->link(
                        __(
                            "{0} Aprovar",
                            $this->Html->tag('i', '', ['class' => 'fa fa-check'])
                        ),
                        '#',
                        [
                            'class' => 'btn btn-primary btn-confirm',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-confirm',
                            'data-action' => Router::url(
                                [
                                    'controller' => 'pontuacoes_comprovantes',
                                    'action' => 'aprovar_pontuacao_comprovante', $pontuacao->id
                                ]
                            ), 'escape' => false
                        ]
                    ) ?>
                </td>
            </tr>
            <tr>
                <th>

                    <?= __("Alternar Validade") ?>
                        <td>
                            <?php
                            if ($pontuacao->registro_invalido) {
                                echo $this->Html->link(
                                    __(
                                        "{0} Validar",
                                        $this->Html->tag('i', '', ['class' => 'fa fa-check'])
                                    ),
                                    '#',
                                    [
                                        'class' => 'btn btn-primary btn-confirm',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-validate',
                                        'data-action' => Router::url(
                                            [
                                                'controller' => 'pontuacoes_comprovantes',
                                                'action' => 'validar_pontuacao_comprovante', $pontuacao->id
                                            ]
                                        ), 'escape' => false
                                    ],
                                    false
                                );
                            } else {
                                echo $this->Html->link(
                                    __(
                                        "{0} Invalidar",
                                        $this->Html->tag('i', '', ['class' => 'fa fa-close'])
                                    ),
                                    '#',
                                    [
                                        'class' => 'btn btn-danger btn-confirm',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-invalidate',
                                        'data-action' => Router::url(
                                            [
                                                'controller' => 'pontuacoes_comprovantes',
                                                'action' => 'invalidar_pontuacao_comprovante', $pontuacao->id
                                            ]
                                        ), 'escape' => false
                                    ],
                                    false
                                );
                            }
                            ?>
                        </td>

                </th>
            </tr>
        </table>
    <?php endif; ?>

    <h4>Dados do Cupom</h4>
    <table class="table table-striped table-hover">
        <tr>
            <th>
                <?= __('Cliente') ?>
            </th>
            <td>
                <?= h($pontuacao->usuario->nome) ?>
                <?= $this->Html->link(__("Alterar"), ['action' => 'alterar_cliente_pontuacao', $pontuacao->id], ['class' => 'btn btn-primary btn-xs fa fa-refresh']) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Funcionário do atendimento') ?>
            </th>
            <td>
                <?= h($pontuacao->funcionario->nome) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Total de Gotas') ?>
            </th>
            <td>
                <?= h($this->Number->precision($pontuacao->soma_pontuacoes, 0)) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Chave da NFE') ?>
            </th>
            <td>
                <?= h($pontuacao->chave_nfe) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Requer Auditoria') ?>
            </th>
            <td>
                <?= h($this->Boolean->convertBooleanToString($pontuacao->requer_auditoria)) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Registro Auditado') ?>
            </th>
            <td>
                <?= h($this->Boolean->convertBooleanToString($pontuacao->auditado)); ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Data Impressão ') ?>
            </th>
            <td>
                <?= h($pontuacao->data->format('d/m/Y H:i:s')) ?>
            </td>

        </tr>
    </table>

        </div>
    </div>
</div>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->css('styles/pontuacoes/detalhes_cupom'); ?>
<?php else : ?>
    <?= $this->Html->css('styles/pontuacoes/detalhes_cupom.min'); ?>
<?php endif; ?>

<?= $this->fetch('css'); ?>
