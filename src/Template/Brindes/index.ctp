<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src\Template\Brindes\index.ctp
 *
 * @since     2019-04-20
 *
 * Arquivo que exibe brindes do cliente
 */
use Cake\Core\Configure;
use Cake\Routing\Router;
use App\Custom\RTI\DateTimeUtil;
use Cake\I18n\Number;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$title = "";
if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) {

    $title = "Configurações IHM";
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
    $this->Breadcrumbs->add('Detalhes da Rede', array('controller' => 'redes', 'action' => 'verDetalhes', $redesId));
    $this->Breadcrumbs->add('Detalhes da Unidade', array("controller" => "clientes", "action" => "verDetalhes", $clientesId), ['class' => 'active']);
    $this->Breadcrumbs->add($title, [], ['class' => 'active']);
} else {
    $title = "Cadastro de Brindes";

    $this->Breadcrumbs->add($title, [], ['class' => 'active']);

}

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>

<div class="col-lg-12">


</div>


<div class="row">
    <?= $this->element("../Brindes/left_menu", array("mode" => "add")) ?>
    <div class="brindes index col-lg-9 columns content">
        <h3><?php echo $title ?></h3>

        <?php echo $this->element(
            "../Brindes/brindes_filtro",
            array(
                "controller" => "brindes",
                "action" => "index",
                "id" => $clientesId,
                "tipoPerfil" => $usuarioLogado["tipo_perfil"],
                "dataPost" => $dataPost
            )
        )
        ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('ilimitado') ?></th>
                    <th><?= $this->Paginator->sort('habilitado') ?></th>
                    <th><?= $this->Paginator->sort('tipo_equipamento', array("label" => "Equipamento")) ?></th>
                    <th><?= $this->Paginator->sort('tipo_codigo_barras', array("label" => "Código de Barras")) ?></th>
                    <th><?= $this->Paginator->sort('preco_padrao', array("label" => "Preço Gotas")) ?></th>
                    <th><?= $this->Paginator->sort('valor_moeda_venda_padrao', array("label" => "Preço Reais")) ?></th>
                    <th><?= $this->Paginator->sort('audit_insert', array("Label" => "Data Criação")) ?></th>

                    <th class="actions">
                        <?= __('Ações') ?>
                        <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes"><span class=" fa fa-book"> Legendas</span></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($brindes as $brinde) : ?>
                    <tr>
                        <td><?php echo h($brinde->nome) ?></td>
                        <td><?php echo $this->Boolean->convertBooleanToString($brinde["ilimitado"]) ?></td>
                        <td><?php echo $this->Boolean->convertBooleanToString($brinde["habilitado"]) ?></td>
                        <td><?php echo $brinde["tipo_equipamento"] ?></td>
                        <td><?php echo $brinde["tipo_codigo_barras"] ?></td>
                        <td><?php echo Number::precision($brinde->preco_padrao, 2) ?></td>
                        <td><?php echo Number::currency($brinde->valor_moeda_venda_padrao) ?></td>
                        <td><?php echo DateTimeUtil::convertDateTimeToLocal($brinde["audit_insert"]) ?></td>

                        <td class="actions" style="white-space:nowrap">
                            <a href="<?php echo sprintf('/brindes/view/%s', $brinde['id']) ?>" class="btn btn-default btn-xs"><i class="fa fa-info-circle" title="Ver detalhes"></i></a>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $brinde->id], ['class' => 'btn btn-primary btn-xs']) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $brinde->id], ['confirm' => __('Are you sure you want to delete # {0}?', $brinde->id), 'class' => 'btn btn-danger btn-xs']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="paginator">
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
        </div>
    </div>
