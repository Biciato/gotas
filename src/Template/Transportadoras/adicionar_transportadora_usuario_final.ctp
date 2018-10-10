<?php

/**
 * @description View para adicionar transportadoras de um usuário
 * @author      Gustavo Souza Gonçalves
 * @file        Template\Transportadoras\adicionar_transportadora_usuario_final.php
 * @date        19/02/2018
 *
 */


use Cake\Core\Configure;

$userManaged = $this->request->session()->read("User.ToManage");

if (!empty($userManaged)){
    $user_logged = $userManaged;
}

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'index']);

} else if ($user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType']
&& $user_logged['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'usuarios_rede', $rede->id]);
}

$this->Breadcrumbs->add('Detalhes de Usuário', array("controller" => "usuarios", "action" => "view", $usuarios_id), ['class' =>'active']);

$this->Breadcrumbs->add('Transportadoras de  Usuário', array("controller" => "transportadoras", "action" => "transportadorasUsuario", $usuarios_id), array());
$this->Breadcrumbs->add('Dados de Transportadora', array(), array('class' =>'active'));

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->element('../Pages/left_menu', ['item_selected' => 'atualizar_cadastro_cliente', 'mode_selected' => 'atualizar_cadastro_cliente_transportadoras']) ?>


<div class="transportadoras form col-lg-9 col-md-10 columns content">
    <?= $this->Form->create($transportadora) ?>
    <fieldset>

        <?= $this->element('../Transportadoras/transportadoras_form') ?>

    </fieldset>
    <div class="col-lg-12">
        <div class="col-lg-2">
            <?= $this->Form->button(
                __('{0} Salvar', $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
                [
                    'type' => 'submit',
                    'class' => 'btn btn-primary btn-block',
                    'escape' => false,
                ]
            ) ?>
        </div>

        <div class="col-lg-10">
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

<?= $this->Html->tag('i', true, ['id' => 'show_form', ['class' => 'hidden']]) ?>

<?= $this->fetch('script') ?>
