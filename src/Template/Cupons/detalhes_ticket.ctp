<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/detalhes_ticket.ctp
 * @date     09/08/2017
 */

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Histórico de Brindes', ['controller' => 'cupons', 'action' => 'historico_brindes']);

$this->Breadcrumbs->add(__("Histórico de Brinde {0}", $cupom->brinde->nome), [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>



<?= $this->element('../Cupons/left_menu', ['mode' => 'report', 'controller' => 'cupons', 'action' => 'historico_brindes']) ?>
<div class="col-lg-9 col-md-10 columns">
    <h3><?= h($cupom->brinde->nome) ?></h3>
    <table class="table table-striped table-hover">
        <tr>
            <th scope="row"><?= __('Cliente:') ?></th>
            <td><?= h($cupom->usuario->nome) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Brinde') ?></th>
            <td><?= h($cupom->brinde->nome) ?></td>
        </tr>
        <?php if (!empty($cupom->brinde->codigo_primario) && ($cupom->brinde->codigo_primario <= 4)) : ?>
            <tr>
                <th scope="row"><?= __('Tipo de Smart Shower') ?></th>
                <td><?= h($this->Tickets->getTicketShowerType($cupom->tipo_banho)) ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <th scope="row"><?= __('Tempo ') ?></th>
            <td><?= $this->Number->format($cupom->codigo_secundario) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Senha') ?></th>
            <td><?= $this->Number->format($cupom->senha) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Data de Emissão:') ?></th>
            <td><?= h($cupom->data->format('d/m/Y H:i:s')) ?></td>
        </tr>

    </table>
</div>
