<?php
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesEstoque/adicionar_estoque.ctp
 * @date     09/08/2017
 */
// Referências
use Cake\Core\Configure;
use Cake\Routing\Router;

// Menu de navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$titleBrindesIndex = "";
$title = "";
if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) {

    $titleBrindesIndex = "Configurações IHM";
    $title = sprintf("Informações do IHM %s", $brinde["nome"]);
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
    $this->Breadcrumbs->add('Detalhes da Rede', array('controller' => 'redes', 'action' => 'verDetalhes', $redesId));
    $this->Breadcrumbs->add('Detalhes da Unidade', array("controller" => "clientes", "action" => "verDetalhes", $clientesId), ['class' => 'active']);
    $this->Breadcrumbs->add($titleBrindesIndex, array("controller" => "brindes", "action" => "index", $clientesId), array());
} else {
    $titleBrindesIndex = "Cadastro de Brindes";
    $title = sprintf("Informações do Brinde %s", $brinde["nome"]);

    $this->Breadcrumbs->add($titleBrindesIndex, array("controller" => "brindes", "action" => "index", $clientesId), array());
}

$this->Breadcrumbs->add("Informações do Brinde", array("controller" => "brindes", "action" => "view", $brinde["id"]), array());
$this->Breadcrumbs->add(__('Adicionar Estoque de Brinde'), [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>

<?= $this->element(
    '../BrindesEstoque/left_menu',
    [
        'mode' => 'editStock',
    ]
) ?>

<div class="brindesEstoque form col-lg-9 col-md-10 columns content">
    <?= $this->Form->create($brinde_estoque) ?>
    <fieldset>
        <legend><?= __('Adicionar Estoque para Brinde') ?></legend>
        <?= $this->element('../BrindesEstoque/brindes_estoque_form', ['required_tipo_operacao' => false, 'required_data' => false]) ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
