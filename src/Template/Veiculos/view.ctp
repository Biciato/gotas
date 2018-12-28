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
    <table class="table table-striped table-hover">
        <tr>
            <th scope="row"><?= __('Placa') ?></th>
            <td><?= h($veiculo->placa) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modelo') ?></th>
            <td><?= h($veiculo->modelo) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Fabricante') ?></th>
            <td><?= h($veiculo->fabricante) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($veiculo->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Ano') ?></th>
            <td><?= $this->Number->format($veiculo->ano) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Data de Criação') ?></th>
            <td><?= h($veiculo->audit_insert->format('d/m/Y H:i:s')) ?></td>
        </tr>

    </table>
</div>
