
<?php

/**
 * @category View
 * @package  App\Web\rtibrindes\src\Template\Usuarios
 * @filename rtibrindes/src/Template/UsuariosHasBrindes/relatorio_brindes_usuarios_redes.ctp
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     18/03/2018
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$title = __("Relatório Brindes Adquiridos por Clientes Finais de Cada Rede");

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
        'show_reports_admin_rti' => true
    ]
) ?>

<div class="col-lg-9 col-md-8 ">
    <legend><?= __($title) ?> </legend>

    <?= $this->element('../UsuariosHasBrindes/filtro_relatorio_brindes_usuarios_redes', ['controller' => 'UsuariosHasBrindes', 'action' => 'relatorio_brindes_usuarios_redes']) ?>

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

        <?php if (sizeof($usuarios) > 0) : ?>

            <?php foreach ($usuarios as $key => $usuario) : ?> 


                <?php if (sizeof($usuario['usuarioHasBrindes']) > 0) : ?> 

                <h4><?= __("Brindes do Usuário: {0} ", $usuario->nome) ?> </h4>

                <table class="table table-hover table-striped table-condensed table-responsive">
                    <thead>
                        <tr>
                            <th><?= h(__("Brinde")) ?> </th>
                            <th><?= h(__("Tipo")) ?> </th>
                            <th><?= h(__("Tempo Smart Shower")) ?> </th>
                            <th><?= h(__("Qte.")) ?> </th>
                            <th><?= h(__("Valor Pago")) ?> </th>
                            <th><?= h(__("Data Compra")) ?> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuario['usuarioHasBrindes'] as $key => $usuarioHasBrinde) : ?>

                            <tr>
                                <td><?= $usuarioHasBrinde->clientes_has_brindes_habilitado->brinde->nome ?> </td>
                                <td><?= $this->Gift->getGiftType($usuarioHasBrinde->clientes_has_brindes_habilitado->brinde->equipamento_rti_shower) ?> </td>
                                <td><?=
                                    $usuarioHasBrinde->clientes_has_brindes_habilitado->brinde->equipamento_rti_shower ?
                                        __("{0} minutos", $usuarioHasBrinde->clientes_has_brindes_habilitado->brinde->tempo_uso_brinde)
                                        : null ?> </td>
                                <td><?= h($this->Number->precision($usuarioHasBrinde->quantidade, 2)) ?> </td>
                                <td><?= h($this->Number->precision($usuarioHasBrinde->preco, 2)) ?> </td>
                                <td><?= $usuarioHasBrinde->audit_insert->format('d/m/Y') ?> </td>

                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php endif; ?> 
                

            <?php endforeach; ?> 
            

        <?php else : ?> 

            <h4>Consulta não retornou dados!</h4>

        <?php endif; ?>



</div>

