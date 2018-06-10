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
    <?= $this->Form->create($brinde) ?>
    <fieldset>
        <legend><?= 'Editar Brinde' ?></legend>
        <?= $this->element('../Brindes/brindes_form', ['brinde' => $brinde, "imagemOriginal" => $imagemOriginal]); ?>
    </fieldset>
    <?= $this->Form->button(__('{0} Salvar',
        $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
        [
            'class' => 'btn btn-primary',
            'escape' => false
        ]

    ) ?>
    <?= $this->Form->end() ?>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/brindes/brindes_form') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/brindes/brindes_form.min') ?>
<?php endif; ?>

<?= $this->fetch('script')?>
