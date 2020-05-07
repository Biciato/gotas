<div class="usuarios view col-lg-12 col-md-12">

    <?php echo $this->element('../Usuarios/tabela_info_usuarios', ['usuario' => $usuario]); ?>

    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <a onclick="history.go(-1); return false;" class="btn btn-primary botao-cancelar"> <i class="fa fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</div>
