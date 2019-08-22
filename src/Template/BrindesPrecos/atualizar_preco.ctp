<?php

/**
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @file     src\Template\BrindesPrecos\atualizar_preco.ctp
 *
 * @since     2019-04-24
 *
 * Arquivo que exibe formulário para atualizar o preço do brinde
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// Menu de navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$titleBrindesIndex = "";
$title = "";
$titleCurrentPage = "Atualizar Preço";
if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) {

    $titleBrindesIndex = "Configurações IHM";
    $title = sprintf("Informações do IHM %s", $brinde["nome"]);
    $this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
    $this->Breadcrumbs->add('Detalhes da Rede', array('controller' => 'redes', 'action' => 'verDetalhes', $redesId));
    $this->Breadcrumbs->add('Detalhes da Unidade', array("controller" => "clientes", "action" => "verDetalhes", $clientesId), ['class' => 'active']);
    $this->Breadcrumbs->add($titleBrindesIndex, array("controller" => "brindes", "action" => "index", $clientesId), array());
}  else if (in_array($usuarioLogado["tipo_perfil"], array(PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL))) {
    $title = "Cadastro de Brindes";
    $this->Breadcrumbs->add("Selecionar Unidade Para Configurar Brindes", array("controller" => "brindes", "action" => "escolherPostoConfigurarBrinde"));
    $this->Breadcrumbs->add($title, array("controller" => "brindes", "action" => "index", $clientesId), ['class' => 'active']);
} else {
    $title = "Cadastro de Brindes";
    $this->Breadcrumbs->add($title, array("controller" => "brindes", "action" => "index", $clientesId), ['class' => 'active']);
}


$this->Breadcrumbs->add("Informações do Brinde", array("controller" => "brindes", "action" => "view", $brinde["id"]), array());
$this->Breadcrumbs->add($titleCurrentPage, array(), array("class" => "active"));

// 'class' => 'active'

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);


// menu esquerdo
echo $this->element(
    '../BrindesPrecos/left_menu',
    [
        'brindes_id' => $brindesId,
        'go_back_url' => [
            'controller' => 'clientes_has_brindes_habilitados',
            'action' => 'detalhes_brinde', $brindesId
        ]
    ]
) ?>
<div class="clientesHasBrindesHabilitados form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($novoPreco) ?>
    <fieldset>
        <legend><?= $titleCurrentPage ?></legend>
        <?= $this->element('../BrindesPrecos/novo_preco_form') ?>
    </fieldset>
    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <button type="submit" class="btn btn-primary botao-confirmar"><i class="fa fa-save"> </i> Salvar</button>
            <a href="/brindes/view/<?= $brindesId?>"
                class="btn btn-danger botao-cancelar">
                <i class="fa fa-window-close">
                </i>
                Cancelar
            </a>
        </div>
    </div>
</div>


