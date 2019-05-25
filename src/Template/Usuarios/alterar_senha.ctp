<?php

/**
 * @description Arquivo para formulário de cadastro de usuários
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/alterar_senha.ctp
 * @date        28/08/2017
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Dados do Usuário', ['controller' => 'usuarios', 'action' => 'view', $usuario->id]);
$this->Breadcrumbs->add('Alterar Senha', array(), array("class" => "active"));

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);


$max_length = ($usuario->tipo_perfil == (int)Configure::read('profileTypes')['UserProfileType']) ? 6 : 8;
?>
<?= $this->element('../Usuarios/left_menu', ['controller' => 'pages', 'action' => 'display', 'mode' => 'back']) ?>

<?php $this->assign('title', 'Alterar senha'); ?>
<div class="users form col-lg-9 col-md-10 columns content">
    <?php echo $this->Form->create($usuario); ?>
    <fieldset>
        <legend><?= __('Alterar senha') ?> </legend>


            <?= $this->Form->input(
                'senha',
                [
                    "label" => "Nova Senha*",
                    'type' => 'password',
                    'required' => true,
                    'autofocus' => true,
                    'maxLength' => $max_length
                ]
            ); ?>

            <?= $this->Form->input(
                'confirm_senha',
                [
                    "label" => "Confirmar Nova Senha*",
                    'type' => 'password',
                    'required' => true,
                    'maxLength' => $max_length
                ]
            ); ?>


    </fieldset>

        <button type="submit" class="btn btn-primary botao-confirmar" id="user_submit">
            <i class="fa fa-save"></i>
            Salvar
        </button>
        <a onclick="window.history.go(-1); return false;" class="btn btn-danger botao-cancelar">
            <i class="fa fa-window-close"></i>
            Cancelar
        </a>
    <?php echo $this->Form->end(); ?>
</div>
