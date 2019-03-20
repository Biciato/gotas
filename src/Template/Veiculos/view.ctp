<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Veiculo $veiculo
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages']);

// $this->Breadcrumbs->add('Veículos do Usuário', array(), array('class' => 'active'));

if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    // $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'index']);
    $this->Breadcrumbs->add('Veículos', ['controller' => 'veiculos', 'action' => 'index']);

} else if ($usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminDeveloperProfileType']
&& $usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'usuarios_rede', $rede->id]);
}

$this->Breadcrumbs->add("Detalhes do Veículo", array(), array("class" => "active"));

// $this->Breadcrumbs->add('Detalhes de Usuário', array("controller" => "usuarios", "action" => "index"), ['class' =>'active']);
// $this->Breadcrumbs->add('Detalhes de Usuário', array(), ['class' =>'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>

<?= $this->element("../Veiculos/left_menu", array()); ?>

<div class="veiculos view col-lg-9 col-md-8 columns content">
    <h3><?= h($veiculo["placa"]) ?></h3>
    <div class="form-group row">
        <div class="col-lg-3">
            <label for="placa">Placa*</label>
            <input type="text"
                name="placa"
                class="form-control"
                id="placa"
                placeholder="Placa..."
                readonly
                value="<?= $veiculo["placa"]?>"
                required />
        </div>
        <div class="col-lg-3">
            <label for="modelo">Modelo*</label>
            <input type="text"
                name="modelo"
                class="form-control"
                id="modelo"
                placeholder="Modelo..."
                readonly
                value="<?= $veiculo["modelo"]?>"
                required />
        </div>
        <div class="col-lg-3">
            <label for="fabricante">Fabricante*</label>
            <input type="text"
                name="fabricante"
                class="form-control"
                id="fabricante"
                placeholder="Fabricante..."
                readonly
                value="<?= $veiculo["fabricante"]?>"
                required />
        </div>
        <div class="col-lg-3">
            <label for="ano">Ano*</label>
            <input type="text"
                name="ano"
                class="form-control"
                id="ano"
                placeholder="Ano..."
                readonly
                value="<?= $veiculo["ano"]?>"
                required />
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            <label for="ano">Data de Criação*</label>
            <input type="text"
                name="audit_insert"
                class="form-control"
                id="audit_insert"
                placeholder="Data de Criação..."
                readonly
                value="<?= $veiculo["audit_insert"]->format("d/m/Y H:i:s")?>"
                required />
        </div>
        <div class="col-lg-6">
            <label for="ano">Data de Atualização*</label>
            <input type="text"
                name="ano"
                class="form-control"
                id="audit_update"
                placeholder="Data de Atualização..."
                readonly
                value="<?= !empty($veiculo["audit_update"]) ? $veiculo["audit_update"]->format("d/m/Y H:i:s") : null?>"
                required />
        </div>
    </div>

    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <a href="/veiculos" class="btn btn-primary botao-cancelar"> <i class="fa fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</div>
