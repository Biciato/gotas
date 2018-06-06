
<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\RedesHasClientes
 * @filename rtibrindes/src/Template/RedesHasClientes/relatorio_unidades_redes.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     02/03/2018
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$title = __("Relatório de Unidades de Redes Cadastradas");

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);
?>

<?= $this->element(
    '../Redes/left_menu',
    [
        'show_reports' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend><?= $title ?></legend>

    <?= $this->element('../Redes/filtro_redes_unidades_relatorio', ['controller' => 'Clientes', 'action' => 'relatorio_unidades_redes']) ?>

    <div class="pull-right">
        <?= $this->Html->tag(
            'button',
            __(
                "{0} Exportar",
                $this->Html->tag('i', '', ['class' => 'fa fa-file-excel-o'])
            ),
            [
                'class' => 'btn btn-primary btn-export-html'
            ]
        ) ?>

        <?= $this->Html->tag(
            'button',
            __(
                "{0} Imprimir",
                $this->Html->tag('i', '', ['class' => 'fa fa-print'])
            ),
            [
                'class' => 'btn btn-primary btn-print-html'
            ]
        ) ?>
    </div>
    <!-- <h4>Lista de Redes</h4> -->
        <!-- <table> -->
    <div class='table-export'>

    <?php foreach ($redes as $key => $rede) : ?>


        <?php if (sizeof($rede['clientes']) > 0) : ?>
            <h4><?= __("Clientes da Rede {0} ", $rede['nome_rede']) ?> </h4>

            <table class="table table-hover table-striped table-condensed table-responsive">
                <thead>
                    <tr>
                            <th><?= h('Nome Fantasia') ?></th>
                            <th><?= h('Razao Social') ?></th>
                            <th><?= h('CNPJ') ?></th>
                            <th><?= h('Endereco') ?></th>
                            <th><?= h('Num.') ?></th>
                            <th><?= h('Compl.') ?></th>
                            <th><?= h('Bairro') ?></th>
                            <th><?= h('Municipio') ?></th>
                            <th><?= h('Estado') ?></th>
                            <th><?= h('CEP') ?></th>
                            <th><?= h('Tel. Fixo') ?></th>
                            <th><?= h('Fax') ?></th>
                            <th><?= h('Celular') ?></th>
                            <th><?= h('Criado em') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rede['clientes'] as $key => $cliente) : ?>

                        <tr>
                            <td><?= h($cliente->nome_fantasia) ?></td>
                            <td><?= h($cliente->razao_social) ?></td>
                            <td><?= h($this->NumberFormat->formatNumberToCNPJ($cliente->cnpj)) ?></td>
                            <td><?= h($cliente->endereco) ?></td>
                            <td><?= h($cliente->endereco_numero) ?></td>
                            <td><?= h($cliente->endereco_complemento) ?></td>
                            <td><?= h($cliente->bairro) ?></td>
                            <td><?= h($cliente->municipio) ?></td>
                            <td><?= h($cliente->estado) ?></td>
                            <td><?= h($cliente->cep) ?></td>
                            <td><?= h($this->Phone->formatPhone($cliente->tel_fixo)) ?></td>
                            <td><?= h($this->Phone->formatPhone($cliente->tel_fax)) ?></td>
                            <td><?= h($this->Phone->formatPhone($cliente->tel_celular)) ?></td>
                            <td><?= h($cliente->audit_insert->format('d/m/Y')) ?></td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
    <?php endforeach; ?>



</div>

