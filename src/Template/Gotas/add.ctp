<?php
/**
  * @var \App\View\AppView $this
  *
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Gotas/add.ctp
  * @date     06/08/2017
  */
  
?>
<?= $this->element('../Gotas/left_menu', ['mode' => 'edit']) ?>
<div class="gotas form col-lg-9 col-md-8 columns content">
    <?= $this->element('../Gotas/gotas_config_input_form', ['gota' => $gota]) ?>
</div>

