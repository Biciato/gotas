
<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\Usuarios
 * @filename rtibrindes/src/Template/Usuarios/relatorio_usuarios_cadastrados.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     17/03/2018
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$title = __("Relatório de Usuários Cadastrados");

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);
?>

<?= $this->element(
    '../Usuarios/left_menu',
    [
        'show_reports' => true,
        'show_reports_admin_rti' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend><?= __($title) ?> </legend>

    <?= $this->element('../Usuarios/filtro_relatorio_usuarios_cadastrados', ['controller' => 'Usuarios', 'action' => 'relatorio_usuarios_cadastrados']) ?>

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
    
    <div class='table-export'>
    
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
                        <th><?= h(__("Data Criação")) ?> </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $key => $usuario) : ?>

                        <tr>
                            <td><?= $usuario->nome ?></td>
                            <td><?= h(is_null($usuario->data_nasc) ? null : $usuario->data_nasc->format('d/m/Y')) ?> </td>
                            <td><?= h($this->UserUtil->getGenderType($usuario->sexo)) ?> </td>
                            <td><?= h($this->Boolean->convertBooleanToString($usuario->necessidades_especiais)) ?> </td>
                            <td><?= h($this->NumberFormat->formatNumbertoCPF($usuario->cpf)) ?> </td>
                            <td><?= $usuario->doc_estrangeiro ?> </td>
                            <td><?= h($this->Phone->formatPhone($usuario->telefone)) ?> </td>
                            <td><?= h($this->Boolean->convertBooleanToString($usuario->conta_ativa)) ?> </td>
                            <td><?= h($this->Boolean->convertBooleanToString($usuario->conta_bloqueada)) ?> </td>
                            <td><?= h($usuario->audit_insert->format('d/m/Y')) ?> </td>

                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>




</div>

