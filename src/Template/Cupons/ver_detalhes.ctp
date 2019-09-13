
<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/ver_detalhes.ctp
 * @date     08/01/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] <= (int)Configure::read('profileTypes')['WorkerProfileType']) {
    $this->Breadcrumbs->add('Cupons de Brinde do Usuário', ['controller' => 'usuarios_has_brindes', 'action' => 'historico_brindes', $usuario->id]);
} else {
    $this->Breadcrumbs->add('Meu Histórico de Cupons de Brinde', ['controller' => 'usuarios_has_brindes', 'action' => 'historico_brindes']);
}

$this->Breadcrumbs->add('Detalhes do Cupom', [], ['class' => 'active']);
echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../UsuariosHasBrindes/left_menu') ?>

<div class="redes form col-lg-9 col-md-8 columns content">

    <legend>Detalhes do Brinde</legend>


    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="row"><?= __('Brinde') ?></th>
                <td>
                    <?= h($cupom->brinde->nome) ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?= __('Quantidade') ?></th>
                <td>
                    <?= $cupom->quantidade ?>
                </td>
            <tr>
            </tr>
                <th scope="row"><?= __("Valor Pago em Gotas") ?></th>
                <td>
                    <?= $this->Number->precision($cupom->valor_pago_gotas, 2) ?>
                </td>
            <tr>
            </tr>
                <th scope="row"><?= __("Valor Pago em Reais") ?></th>
                <td>
                    <?= $this->Number->precision($cupom->valor_pago_reais, 2) ?>
                </td>
            <tr>
            </tr>
                <th scope="row"><?= __('Data') ?></th>
                <td>
                    <?= $cupom->data->format('d/m/Y H:i:s') ?>
                </td>
            </tr>

            <!-- <tr>
                <th scope="row"><?= __("Cupom Resgatado?") ?></th>
                <td><?= $this->Boolean->convertBooleanToString($cupom->resgatado) ?></td>
            </tr> -->

            <!-- <?php if (!$cupom->restatado) : ?>
                <tr>
                    <th scope="row"><?= __("Reimprimir?") ?></th>
                    <td>
                        <?= $this->Html->link(
                            __(
                                "{0} Ir para Tela de Impressão",
                                $this->Html->tag('i', '', ['class' => 'fa fa-print'])
                            ),
                            [
                                'controller' => 'Cupons',
                                'action' => 'reimprime_brinde_comum', $cupom->cupom_emitido
                            ],
                            [
                                'class' => 'btn btn-default',
                                'title' => 'Novo',
                                'escape' => false
                            ]
                        )
                        ?>
                    </td>
                </tr>
            <?php endif; ?> -->


        </thead>
        <tbody>


            </tr>

        </tbody>
    </table>
<div/>
