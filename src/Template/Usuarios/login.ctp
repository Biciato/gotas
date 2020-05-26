<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <div class="logo-element" style="display: block">
            <img alt="image" src="/img/rti-logo.png" style="width: 15rem"/>
        </div>
        <p>
            <?php echo __("Insira seu email e senha para continuar"); ?>
        </p>
        <form class="m-t" role="form" action="javascript:void(0)" id="login-form">
            <div class="form-group">
                <input type="email" class="form-control" placeholder="Email" name="email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Senha" name="senha">
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b" id="btn-login">Login</button>

            <a href="<?php echo $this->Url->build(['controller' => 'usuarios', 'action' => 'esqueciMinhaSenha']) ?>"><small><?php echo __('Esqueceu sua senha?'); ?></small></a>
            <p class="text-muted text-center"><small><?php echo __('Não tem uma conta?') ?></small></p>
            <a class="btn btn-info btn-block" href="<?php echo $this->Url->build(['controller' => 'usuarios', 'action' => 'registrar']) ?>"><?php echo __('Criar uma conta'); ?></a>
        </form>
        <p class="m-t"> <small>APP web GOTAS &copy; <?php echo date('Y'); ?></small> </p>
    </div>
</div>
<?php $this->append('title'); ?>
GOTAS - Faça o login no sistema
<?php $this->end();
$this->append('script');
echo $this->Html->script('https://www.google.com/recaptcha/api.js?render='.$publickeycaptcha);
echo $this->Html->script('scripts/usuarios/login');
$this->end(); ?>
