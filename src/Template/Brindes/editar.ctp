<?php
/**
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Pontuacoes/editar_brinde_rede.ctp
  * @date     18/08/2017
  */

  use Cake\Core\Configure;
  use Cake\Routing\Router;

  $this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

  $this->Breadcrumbs->add('Brindes da Minha Rede', ['controller' => 'brindes', 'action' => 'brindes_minha_rede']);

  $this->Breadcrumbs->add('Edição de Brinde', [], ['class' => 'active']);

  echo $this->Breadcrumbs->render(
      ['class' => 'breadcrumb']
  );

?>

<nav class="col-lg-3 col-md-2 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Menu') ?></a></li>
    </ul>
</nav>
<div class="brindes form col-lg-9 col-md-10 columns content">
    <legend><?= 'Editar Brinde' ?></legend>
    <?= $this->Form->create($brinde) ?>
    <fieldset>
        <?= $this->element('../Brindes/brindes_form', ['brinde' => $brinde, "imagemOriginal" => $brinde["nome_img_completo"]]); ?>
    </fieldset>

    <?= $this->Form->end() ?>
</div>
<?= $this->fetch('script')?>
