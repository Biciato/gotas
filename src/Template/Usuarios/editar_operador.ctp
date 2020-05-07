<?php
use Cake\Core\Configure;
?>
<div class="usuarios view col-lg-12 col-md-12">
    <?php echo $this->Form->create($usuario); ?>
    <?php echo $this->element('../Usuarios/usuario_operador_form', ['title' => 'Editar', 'mode' => 'edit']) ?>
    <?php echo $this->Form->end() ?>
</div>

<?php
$extension = Configure::read("debug") ? ""  : ".min";
$this->append('css');
echo $this->Html->css('styles/usuarios/usuario_form' . $extension . '.css?' . SYSTEM_VERSION);
$this->end();
$this->append('script');
echo $this->Html->script(sprintf("jquery-Mask/jquery.mask.min.js?version=%s",  SYSTEM_VERSION));
echo $this->Html->script('scripts/usuarios/edit' . $extension . '.js?version=' . SYSTEM_VERSION);
$this->end();
?>
