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
$titleCurrentPage = "Adicionar Estoque";
if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) {

    $titleBrindesIndex = "Configurações IHM";
    $title = sprintf("Informações do IHM %s", $brinde["nome"]);
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
    $this->Breadcrumbs->add('Detalhes da Rede', array('controller' => 'redes', 'action' => 'verDetalhes', $rede["id"]));
    $this->Breadcrumbs->add('Detalhes da Unidade', array("controller" => "clientes", "action" => "verDetalhes", $clientesId), ['class' => 'active']);
    $this->Breadcrumbs->add($titleBrindesIndex, array("controller" => "brindes", "action" => "index", $clientesId), array());
} else if (in_array($usuarioLogado["tipo_perfil"], array(PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL))) {
    $title = "Cadastro de Brindes";
    $this->Breadcrumbs->add("Selecionar Unidade Para Configurar Brindes", array("controller" => "brindes", "action" => "escolherPostoConfigurarBrinde"));
    $this->Breadcrumbs->add($title, array("controller" => "brindes", "action" => "index", $clientesId), ['class' => 'active']);
} else {
    $title = "Cadastro de Brindes";
    $this->Breadcrumbs->add($title, array("controller" => "brindes", "action" => "index", $clientesId), ['class' => 'active']);
}

$this->Breadcrumbs->add("Informações do Brinde", array("controller" => "brindes", "action" => "view", $brinde["id"]), array());
$this->Breadcrumbs->add($titleCurrentPage, [], ['class' => 'active']);

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
    <?= $this->Form->create($brindeEstoque) ?>
    <fieldset>
        <legend><?= $titleCurrentPage ?></legend>
        <?= $this->element('../BrindesEstoque/brindes_estoque_form', ['required_tipo_operacao' => false, 'required_data' => false]) ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
