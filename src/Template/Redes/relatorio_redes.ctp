
<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\Redes
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     25/02/2018
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Relatório de Redes', [], ['class' => 'active']);

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

<div class="col-lg-9 col-md-8">
    <legend>Relatório Redes Cadastradas</legend>

    <?= $this->element('../Redes/filtro_redes_relatorio', ['controller' => 'Redes', 'action' => 'relatorio_redes']) ?>

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
    <h4>Lista de Redes</h4>

        <table class="table table-hover table-striped table-condensed table-responsive">
            <thead>
                <tr>
                    <th><?= 'Nome da Rede' ?> </th>
                    <th><?= 'Rede Ativa no Sistema' ?> </th>
                    <th><?= 'Permite Consumo de Gotas para Funcionarios' ?> </th>
                    <th><?= 'Data de Cadastro' ?> </th>
                    <!-- <th><?= $this->Paginator->sort('nome_rede', ['label' => 'Nome da Rede']) ?> </th>
                    <th><?= $this->Paginator->sort('ativado', ['label' => 'Rede Ativa no Sistema']) ?> </th>
                    <th><?= $this->Paginator->sort('audit_insert', ['label' => 'Data de Cadastro']) ?> </th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($redes as $key => $rede) : ?>
                    <tr>
                        <td><?= h($rede->nome_rede) ?></td>
                        <td><?= h($this->Boolean->convertBooleanToString($rede->ativado)) ?></td>
                        <td><?= h($rede->audit_insert->format('d/m/Y H:i:s')) ?></td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>



</div>

