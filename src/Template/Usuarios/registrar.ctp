<div class="middle-box text-center loginscreen   animated fadeInDown">
    <div>
        <div>

            <h1 class="logo-name">GOTAS</h1>

        </div>
        <h3><?php echo __('Registre-se no sistema GOTAS'); ?></h3>
        <form class="m-t" role="form" action="javascript:void(0)" id="form-registrar">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="CPF" name='cpf' id="input-cpf">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Nome" name='nome'>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Telefone" name='telefone' id="input-telefone">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" placeholder="Email" name="email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Senha" name='senha'>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Confirmar senha" name='confirm_senha'>
            </div>
            <input type="hidden" name="tipo_perfil" id="tipo_perfil" value="6">

            <button type="submit" id="btn-registrar" class="btn btn-primary block full-width m-b">Registrar</button>

            <p class="text-muted text-center"><small><?php echo __('Já tem uma conta?'); ?></small></p>
            <a class="btn btn-sm btn-white btn-block" href="<?php echo $this->Url->build(['controller' => 'usuarios', 'action' => 'login']) ?>">Login</a>
        </form>
        <p class="m-t"> <small>APP web GOTAS &copy; <?php echo date('Y'); ?></small> </p>
    </div>
</div>
<?php $this->append('title'); ?> 
  GOTAS - Criar novo usuário
<?php $this->end(); 
$this->append('script'); 
  echo $this->Html->script('vanilla-masker');
  echo $this->Html->script('scripts/usuarios/registrar');
$this->end(); ?>