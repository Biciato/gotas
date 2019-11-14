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

$maxLength = ($usuario->tipo_perfil == (int) Configure::read('profileTypes')['UserProfileType']) ? 6 : 8;
?>
<?= $this->element('../Usuarios/left_menu', ['controller' => 'pages', 'action' => 'display', 'mode' => 'back']) ?>

<?php $this->assign('title', 'Alterar senha'); ?>
<div class="users form col-lg-9 col-md-10 columns content">
    <?php echo $this->Form->create($usuario); ?>
    <fieldset>
        <legend><?= __('Alterar senha') ?> </legend>

        <!-- Se o usuário à ser alterado é o mesmo logado -->
        <?php if ($id == $usuarioLogado->id) : ?>
        <div class="form-group">
            <label for="senha_antiga">Senha Antiga*</label>
            <?php if ($usuarioLogado->tipo_perfil == PROFILE_TYPE_USER) :?> 
                <input type="password" id="senha_antiga" name="senha_antiga" class="form-control" minlength="6" require />
            <?php else:?> 
                <input type="password" id="senha_antiga" name="senha_antiga" class="form-control" maxlength="<?= $maxLength ?>" require />
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="senha">Nova Senha*</label>
            <?php if ($usuarioLogado->tipo_perfil == PROFILE_TYPE_USER) :?> 
                <input type="password" id="senha" name="senha" class="form-control" minlength="6" require />
            <?php else :?> 
                <input type="password" id="senha" name="senha" class="form-control" maxlength="<?= $maxLength ?>" require />
            <?php endif;?>
        </div>

        <div class="form-group">
            <label for="confirm_senha">Confirmar Nova Senha*</label>
            <?php if ($usuarioLogado->tipo_perfil == PROFILE_TYPE_USER):?>
                <input type="password" id="confirm_senha" name="confirm_senha" class="form-control" minlength="6" require />
            <?php else: ?> 
                <input type="password" id="confirm_senha" name="confirm_senha" class="form-control" maxlength="<?= $maxLength ?>" require />
            <?php endif; ?>
        </div>
        <!-- Se é gerente ou funcionário -->
        <?php elseif (in_array($usuarioLogado->tipo_perfil, [PROFILE_TYPE_MANAGER, PROFILE_TYPE_WORKER])) : ?>
        <div class="form-group">
            <label for="senha_antiga">Senha Antiga*</label>
            <?php if ($usuario->tipo_perfil == PROFILE_TYPE_USER): ?> 
                <input type="password" id="senha_antiga" name="senha_antiga" class="form-control" minlength=6 require />
            <?php else:?> 
                <input type="password" id="senha_antiga" name="senha_antiga" class="form-control" maxlength="<?= $maxLength ?>" require />
            <?php endif;?>
        </div>

        <div class="form-group">
            <label for="senha">Nova Senha*</label>
            <?php if ($usuario->tipo_perfil === PROFILE_TYPE_USER): ?> 
                <input type="password" id="senha" name="senha" class="form-control" minlength="6" require />
            <?php else: ?> 
                <input type="password" id="senha" name="senha" class="form-control" maxlength="<?= $maxLength ?>" require />
            <?php endif;?>
        </div>

        <div class="form-group">
            <label for="confirm_senha">Confirmar Nova Senha*</label>
            <?php if ($usuario->tipo_perfil === PROFILE_TYPE_USER): ?> 
                <input type="password" id="confirm_senha" name="confirm_senha" class="form-control" minlength="6" require />
            <?php else: ?> 
                <input type="password" id="confirm_senha" name="confirm_senha" class="form-control" maxlength="<?= $maxLength ?>" require />
            <?php endif;?>
        </div>
        <!-- Se é outro usuário alterando a senha (Administrador Local ou com mais permissões), não precisa de confirmar -->
        <?php elseif ($usuarioLogado->tipo_perfil <= PROFILE_TYPE_ADMIN_LOCAL) : ?>
        <div class="form-group">
            <label for="senha">Nova Senha*</label>
            <?php if ($usuario->tipo_perfil == PROFILE_TYPE_USER): ?> 
                <input type="password" id="senha" name="senha" class="form-control" minlength="6" require />
            <?php else:?> 
                <input type="password" id="senha" name="senha" class="form-control" maxlength="<?= $maxLength ?>" require />
            <?php endif;?>
        </div>

        <div class="form-group">
            <label for="confirm_senha">Confirmar Nova Senha*</label>
            <?php if ($usuario->tipo_perfil == PROFILE_TYPE_USER): ?> 
                <input type="password" id="confirm_senha" name="confirm_senha" class="form-control" minlength="6" require />
            <?php else:?> 
                <input type="password" id="confirm_senha" name="confirm_senha" class="form-control" maxlength="<?= $maxLength ?>" require />
            <?php endif;?>
        </div>
        <?php endif; ?>

    </fieldset>

    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <button type="submit" class="btn btn-primary botao-confirmar" id="user_submit">
                <i class="fa fa-save"></i>
                Salvar
            </button>
            <a onclick="window.history.go(-1); return false;" class="btn btn-danger botao-cancelar">
                <i class="fa fa-window-close"></i>
                Cancelar
            </a>
        </div>

    </div>
    <?php echo $this->Form->end(); ?>
</div>