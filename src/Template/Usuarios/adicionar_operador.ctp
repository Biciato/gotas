<?php
use Cake\Core\Configure;
?>

<div class="usuarios form col-lg-12 col-md-8 columns content">
    <?php echo $this->element('../Usuarios/usuario_operador_form', ['title' => 'Adicionar', 'mode' => 'add']); ?>
</div>



<?php
$extension = Configure::read("debug") ? ""  : ".min";
$this->append('script');
echo $this->Html->script('scripts/usuarios/add' . $extension . '.js?version=' . SYSTEM_VERSION);
echo $this->Html->script(sprintf("jquery-Mask/jquery.mask.min.js?version=%s",  SYSTEM_VERSION));
$this->end();
$this->append('css');
echo $this->Html->css('styles/usuarios/usuario_form' . $extension . '.css?' . SYSTEM_VERSION);

$this->end();
?>

