
<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\Veiculos
 * @filename rtibrindes/src/Template/UsuariosHasVeiculos/relatorio_veiculos_usuarios_detalhado.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     20/03/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$title = __("Relatório Detalhado de Usuários Vinculados à Veiculos Cadastrados");

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add(
    __("Relatório Veiculos Cadastrados de Clientes das Redes"),
    [
        'controller' => 'Veiculos',
        'action' => 'relatorio_veiculos_usuarios_redes'
    ]
);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);
?>

<?= $this->element(
    '../Veiculos/left_menu',
    [
        'show_reports' => true,
        'show_reports_admin_rti' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend><?= $title ?></legend>

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

    <h4><?= __("Usuários da Veiculo: {0} ", $veiculo->nome_fantasia) ?> </h4>



        <table class="table table-hover table-striped table-condensed table-responsive">
            <thead>
                <tr>
                <th><?= h(__("Nome")) ?> </th>
                <th><?= h(__("Tipo Perfil")) ?> </th>
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
                <?php foreach ($veiculoHasUsuarios as $key => $veiculoHasUsuario) : ?>

                    <tr>
                        <td><?= $veiculoHasUsuario->usuario->nome ?> </td>
                        <td><?= h($this->UserUtil->getProfileType($veiculoHasUsuario->usuario->tipo_perfil)) ?></td>
                        <td><?= h(is_null($veiculoHasUsuario->usuario->data_nasc) ? null : $veiculoHasUsuario->usuario->data_nasc->format('d/m/Y')) ?> </td>
                        <td><?= h($this->UserUtil->getGenderType($veiculoHasUsuario->usuario->sexo)) ?> </td>
                        <td><?= h($this->Boolean->convertBooleanToString($veiculoHasUsuario->usuario->necessidades_especiais)) ?> </td>
                        <td><?= h($this->NumberFormat->formatNumbertoCPF($veiculoHasUsuario->usuario->cpf)) ?> </td>
                        <td><?= $veiculoHasUsuario->usuario->doc_estrangeiro ?> </td>
                        <td><?= h($this->Phone->formatPhone($veiculoHasUsuario->usuario->telefone)) ?> </td>
                        <td><?= h($this->Boolean->convertBooleanToString($veiculoHasUsuario->usuario->conta_ativa)) ?> </td>
                        <td><?= h($this->Boolean->convertBooleanToString($veiculoHasUsuario->usuario->conta_bloqueada)) ?> </td>
                        <td><?= h($veiculoHasUsuario->usuario->audit_insert->format('d/m/Y')) ?> </td>
                        
                        
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>

</div>

