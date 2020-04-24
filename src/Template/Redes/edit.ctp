<?php

/**
 * @var \App\View\AppView $this
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Redes', ['controller' => 'redes', 'action' => 'index']);

$this->Breadcrumbs->add('Edição de Rede', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);
?>
<?= $this->element('../Redes/left_menu', ['go_back_url' => ['controller' => 'redes', 'action' => 'index']]) ?>
<div class="redes form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($rede, ['enctype' => 'multipart/form-data']) ?>
    <fieldset>
        <legend><?= __('Editar Rede') ?></legend>
        <?= $this->element('../Redes/redes_form') ?>
    </fieldset>
    
    <?= $this->Form->end() ?>
</div>
