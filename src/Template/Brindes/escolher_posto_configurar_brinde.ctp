<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/escolher_posto_configurar_brinde.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
use App\Custom\RTI\NumberUtil;

$title = "Selecionar Unidade Para Configurar Brindes";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../Brindes/left_menu', ['mode' => null]) ?>
<div class="brindes index col-lg-9 col-md-10 columns content">
    <legend><?= $title ?></legend>

    <?= $this->element('../Clientes/filtro_clientes', array('controller' => 'brindes', 'action' => 'escolherPostoConfigurarBrinde', "id" => null)) ?>

    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('nome_fantasia') ?></th>
                <th><?= $this->Paginator->sort('razao_social', array("label" => "Tempo de Uso")) ?></th>
                <th><?= $this->Paginator->sort('CNPJ') ?></th>
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes"><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente) : ?>
                <tr>

                    <td><?= h($cliente["nome_fantasia"]) ?></td>
                    <td><?= h($cliente["razao_social"]) ?></td>
                    <td><?= NumberUtil::formatarCNPJ($cliente["cnpj"]) ?></td>

                    <td class="actions">
                        <a href="<?= sprintf("/brindes/index/%s", $cliente["id"]) ?>" class="btn btn-primary btn-xs botao-navegacao-tabela" title="Configurar">
                            <i class="fa fa-gear"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- <div class="paginator">
        <center>
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('primeiro')) ?>
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape' => false]) ?>
                <?= $this->Paginator->numbers(['escape' => false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape' => false]) ?>
                <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div> -->
</div>
