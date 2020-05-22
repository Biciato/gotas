<div class="passwordBox animated fadeInDown">
	<div class="row">

		<div class="col-md-12">
			<div class="ibox-content">

				<h2 class="font-bold"><?php echo __('Esqueci minha senha'); ?></h2>

				<p>
					<?php echo __('Insira seu email e sua nova senha sera enviada para você'); ?>
				</p>

				<div class="row">

					<div class="col-lg-12">
						<form class="m-t" role="form" action="javascript:void(0)" id="form-recuperar-senha">
							<div class="form-group">
								<input type="email" class="form-control" placeholder="<?php echo __('Endereço de email'); ?>" name='email'>
							</div>

							<button type="submit" class="btn btn-primary block full-width m-b" id="btn-recuperar-senha"><?php echo __('Enviar senha nova'); ?></button>
                            <br>
                            <a class="btn btn-white btn-block" href="<?php echo $this->Url->build(['controller' => 'usuarios', 'action' => 'login']) ?>"><?php echo __('Login'); ?></a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-md-6">
			App web GOTAS
		</div>
		<div class="col-md-6 text-right">
			<small>© 2017-<?php echo date('Y'); ?></small>
		</div>
	</div>
</div>
<?php $this->append('title'); ?>
  GOTAS - Recuperar senha
<?php $this->end();
$this->append('script');
  echo $this->Html->script('scripts/usuarios/esqueci_minha_senha');
$this->end(); ?>
