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
  if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
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
            <!-- <h4 class="panel-title"> -->
                <div>
                    <span class="fa fa-search"></span>
                        Exibir / Ocultar Filtros
                </div>
          
            <!-- </h4> -->
        </div>
        <div id="filter-coupons" class="panel-collapse collapse">
            <div class="panel-body">
                <?= $this->Form->create('Post', [
                        'url' =>
                        [
                            'controller' => $controller,
                            'action' => $action
                        ]
                    ]
                ) ?>
                <div class="col-lg-3">

                    <?= $this->Form->input('opcoes', ['type' => 'select',
                        'id' => 'opcoes',
                        'label' => 'Pesquisar por',
                        'options' => $list_options,
                        'class' => 'form-control col-lg-2'
                    ]) ?>
                </div>  
        
                <div class="col-lg-7">
                    <?= $this->Form->input(
                        'parametro',
                        [
                            'id' => 'parametro',
                            'label' => 'Parâmetro',
                            'class' => 'form-control col-lg-5'
                        ]
                    ) ?> 
                </div>

                <div class="col-lg-2 vertical-align">

                    <?= $this->Form->button(
                        __("{0} Pesquisar", '<i class="fa       fa-search" aria-hidden="true"></i>'),
                        [
                            'class' => 'btn btn-primary btn-block',
                            'type' => 'submit'
                        ]
                    ) ?>
                                
                <?= $this->Form->end() ?>
                </div>

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
