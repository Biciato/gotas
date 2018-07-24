<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\Gotas
 * @filename rtibrindes/src/Template/Gotas/relatorio_consumo_gotas_usuarios.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     14/03/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$title = "Relatório de Consumo de Gotas por Usuários das Redes";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    [
        'class' => 'breadcrumb'
    ]
);


?>

<?= $this->element(
    '../Gotas/left_menu',
    [
        'show_reports_admin_rti' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend><?= $title ?> </legend>

    <?= $this->element('../Gotas/filtro_relatorio_gotas_redes', [
        'controller' => 'Gotas', 'action' => 'relatorio_consumo_gotas_usuarios'
    ]) ?>

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

    <?php $temPontuacao = false; ?>

    <?php foreach ($redes as $key => $rede) : ?>
        <?php if (sizeof($rede['gotas']) > 0) : ?>

        <h3><?= __("Gotas da Rede: {0}", $rede['nome_rede']) ?> </h3>

                <?php foreach ($rede['gotas'] as $key => $gota) : ?>

                <h4><?= __("Usuários que consumiram da Gota {0}", $gota->nome_parametro) ?></h4>

                <?php if (sizeof($gota['usuarios']) > 0) : ?>

                <table class="table table-hover table-striped table-condensed table-responsive">
                    <thead>
                        <tr>
                            <th><?= h(__("Nome")) ?> </th>
                            <th><?= h(__("Data Nasc")) ?> </th>
                            <th><?= h(__("CPF")) ?> </th>
                            <th><?= h(__("Total")) ?> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gota['usuarios'] as $key => $usuario) : ?>

                            <tr>
                                <td><?= $usuario->nome ?></td>
                                <td><?= !is_null($usuario->data_nasc) ? $usuario->data_nasc->format('d/m/Y') : null ?></td>
                                <td><?= h($this->NumberFormat->formatNumberToCPF(!is_null($usuario->cpf) ? $usuario->cpf : null)) ?></td>
                                <td><?= h($gota["quantidade_gotas"]) ?></td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php endif; ?>

                <?php endforeach; ?>


        <?php else : ?>

            <h4>Não há registros à serem exibidos!</h4>

        <?php endif; ?>

    <?php endforeach; ?>

    <h4>Não há registros à serem exibidos!</h4>
</div>

