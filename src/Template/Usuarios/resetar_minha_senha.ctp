<?php

use Cake\Core\Configure;

/**
 * @description Arquivo para formulário de cadastro de usuários
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Usuarios/resetar_minha_senha.ctp
 * @date        28/08/2017
 *
 */
?>
<?php $this->assign('title', 'Resetar senha'); ?>
<div class="users content container">
    <?php echo $this->Form->create($usuario, ["autocomplete" => "off"]) ?>
    <fieldset>
        <legend><?php echo __('Resetar senha') ?></legend>
        <div class="form-group"
            <label for="senha">Senha*</label>
            <input type="text" name="senha" id="senha" required="true" class="form-control senha password" maxlength="8" autofocus="true" />
            <label for="confirm_senha">Confirmar Senha*</label>
            <input type="text" name="confirm_senha" id="confirm_senha" required="true" class="form-control confirm-senha password" maxlength="8" autofocus="true" />
        </div>


    </fieldset>
    <?php echo $this->Form->button(__('Resetar')); ?>
    <?php echo $this->Form->end(); ?>
</div>

<?php
$extension = Configure::read("debug") ? ""  : ".min";
?>

<link rel="stylesheet" href="<?= "/webroot/css/styles/usuarios/resetar_minha_senha" . $extension . ".css" ?>">

<script>
    $(document).ready(function(){
        $("input").attr("autocomplete", "new-password");
        $('form').trigger("reset");
        // $("input").mask("********");
    });
</script>
