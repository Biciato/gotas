<?php

/**
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Clientes/filtro_clientes.ctp
  * @date     14/08/2017
  */

  use Cake\Core\Configure;
  use Cake\Routing\Router;

  $list_options = [
        'nome_fantasia' => 'Nome Fantasia',
        'razao_social' => 'Razão Social',
        'cnpj' => 'CNPJ'
  ];
  if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
      $list_options = [
        'nome_fantasia' => 'Nome Fantasia',
        'razao_social' => 'Razão Social',
        'cnpj' => 'CNPJ',
        'nome_rede' => 'Nome da Rede'
      ];

    }
?>

<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
            <div>
                <span class="fa fa-search"></span>
                    Exibir / Ocultar Filtros
            </div>
        </div>
        <div id="filter-coupons" class="panel-collapse collapse in">
            <div class="panel-body">
                <?= $this->Form->create('Post', [
                        'url' =>
                        [
                            'controller' => $controller,
                            'action' => $action,
                            $id
                        ]
                    ]
                ) ?>
                <div class="form-group row">
                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'nome_fantasia',
                            [
                                'id' => 'nome_fantasia',
                                'label' => 'Nome Fantasia',
                                'class' => 'form-control col-lg-5'
                            ]
                        ) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'razao_social',
                            [
                                'id' => 'razao_social',
                                'label' => 'Razão Social',
                                'class' => 'form-control col-lg-5'
                            ]
                        ) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $this->Form->input(
                            'cnpj',
                            [
                                'id' => 'cnpj',
                                'label' => 'CNPJ',
                                'class' => 'form-control col-lg-5',
                                "type" => "text"
                            ]
                        ) ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary btn-block botao-confirmar ">
                            <span class="fa fa-search"></span>
                            Pesquisar
                        </button>
                    </div>
                </div>
                <?= $this->Form->end() ?>

            </div>
        </div>
    </div>

</div>


<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/clientes/filtro_clientes')?>
<?php else : ?>
    <?= $this->Html->script('scripts/clientes/filtro_clientes.min')?>
<?php endif; ?>

<?= $this->fetch('script') ?>
