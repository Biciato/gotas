<div class="ibox" style="width: 50%; margin: auto;">
    <div class="ibox-title">
        Alterar senha
    </div>
        <div class="ibox-content">
    <div class="form-group row">
        <div class="col-3">
            <label for="senha_antiga">
                Senha atual*
            </label>
            <input type="text" name="senha_antiga" id="senha_antiga" class="form-control"/>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-3">
            <label for="nova_senha">
                Senha nova*
            </label>
            <input type="text" name="nova_senha" id="nova_senha" class="form-control"/>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-3">
            <label for="confirm_senha">
                Confirmação da senha nova*
            </label>
            <input type="text" name="confirm_senha" id="confirm_senha" class="form-control"/>
        </div>
    </div>
    <button id="btn-save" class="btn btn-primary" style="float: right">
        <em class="fas fa-save"></em> Salvar
    </button>
    <span style="clear: right; display: block;"></span>
</div>

<script>
    $(document).ready(function () {
        usuariosAlterarSenha.init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
</script>
