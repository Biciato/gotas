
<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\RedesHasClientesAdministradores
 * @filename rtibrindes/src/Template/RedesHasClientesAdministradores/relatorio_administradores_redes.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     04/03/2018
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$title = __("Relatório Administradores de Redes Cadastradas");

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
    <legend></legend>

    <?= $this->element('../Redes/filtro_redes_unidades_relatorio', ['controller' => 'RedesHasClientesAdministradores', 'action' => 'relatorio_administradores_redes']) ?>

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


        <?php if (sizeof($rede['administradores']) > 0) : ?>
            <h4><?= __("Administradores de Rede: {0} ", $rede['nome_rede']) ?> </h4>

            <table class="table table-hover table-striped table-condensed table-responsive">
                <thead>
                    <tr>
                        <th><?= h(__("Nome")) ?> </th>
                        <th><?= h(__("Data Nasc.")) ?> </th>
                        <th><?= h(__("Sexo")) ?> </th>
                        <th><?= h(__("Nec. Especiais")) ?> </th>
                        <th><?= h(__("CPF")) ?> </th>
                        <th><?= h(__("Doc. Estrangeiro")) ?> </th>
                        <th><?= h(__("Telefone")) ?> </th>
                        <th><?= h(__("Conta Ativa")) ?> </th>
                        <th><?= h(__("Conta Bloqueada")) ?> </th>
                        <th><?= h(__("Audit Insert")) ?> </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rede['administradores'] as $key => $administrador) : ?>

                        <tr>
                            <td><?= $administrador->nome ?></td>
                            <td><?= h(is_null($administrador->data_nasc) ? null : $administrador->data_nasc->format('d/m/Y')) ?> </td>
                            <td><?= h($this->UserUtil->getGenderType($administrador->sexo)) ?> </td>
                            <td><?= h($this->Boolean->convertBooleanToString($administrador->necessidades_especiais)) ?> </td>
                            <td><?= h($this->NumberFormat->formatNumbertoCPF($administrador->cpf)) ?> </td>
                            <td><?= $administrador->doc_estrangeiro ?> </td>
                            <td><?= h($this->Phone->formatPhone($administrador->telefone)) ?> </td>
                            <td><?= h($this->Boolean->convertBooleanToString($administrador->conta_ativa)) ?> </td>
                            <td><?= h($this->Boolean->convertBooleanToString($administrador->conta_bloqueada)) ?> </td>
                            <td><?= h($administrador->audit_insert->format('d/m/Y')) ?> </td>

                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
    <?php endforeach; ?>



</div>

