<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Redes/escolher_unidade_rede.ctp
 * @date     21/11/2017
 * 
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rede[]|\Cake\Collection\CollectionInterface $redes
 *
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Unidades da Rede', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']

);
?>
<?= $this->element(
    '../Redes/left_menu',
    []
)
?>

<div class="redes index col-lg-9 col-md-10 columns content">
    <legend><?= __('Unidades de Atendimento: {0}', $rede->nome_rede) ?></legend>

    <h4>Selecione uma Unidade de Atendimento para resgatar um brinde</h4>
    
    <?php foreach ($rede->redes_has_clientes as $key => $unidade) : ?>

        <div class="col-lg-4 col-sm-6">
        <a href="/ClientesHasBrindesHabilitados/escolher_brinde_unidade/<?= $unidade->id ?>" class="network_link">

         <div class="network_container_outer" data-value="<?= $unidade->cliente->id ?>" >
                    <div class="network_container_inner">

                        <div>
                            <span class="network_title">
                                <?= $unidade->cliente->nome_fantasia ?>
                            </span>
                            <div class="network_content">
                                <div>Brindes Oferecidos</div>
                                
                                    <?php foreach ($unidade->cliente->clientes_has_brindes_habilitados as $key => $brinde_habilitado) : ?>
                                        <li> <?= $brinde_habilitado->brinde->nome ?></li>
                                    
                                    <?php endforeach; ?> 
                                
                            </div>
                        </div>
                    </div>

                    <!-- <div class="network_info">
                        <?= $this->Html->tag(
                            'div',
                            __(
                                '{0}Ver brindes',
                                $this->Html->tag('i', '', ['class' => 'fa fa-question-circle'])
                            ),
                            [
                                'class' => 'btn btn-default pull-right',
                                'id' => 'submit_button',
                                'escape' => false
                            ]
                        )
                        ?>
                    </div> -->
                </div>
            </div>
        

      

           
        </a>

    <?php endforeach; ?>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->css('styles/pages/escolher_unidade_rede') ?>
<?php else : ?> 
    <?= $this->Html->css('styles/pages/escolher_unidade_rede.min') ?>
<?php endif; ?>

<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>