<?php 

use Cake\Core\Configure;

?>
<section class="container">

<?= $this->Form->create('POST', ['url' => ['controller' => 'pages', 'action' => 'busca']]) ?>


<!-- <div class="input-group">
    <input type="text" class="form-control" placeholder="Pesquisa por Estabelecimentos ou Brindes"/>
    <span class="input-group-btn">
        <button class="btn btn-default" type="button">Pesquisar</button>
    </span>
</div> -->

<?php foreach ($redes as $key => $rede) : ?>

<div class="col-lg-4 col-sm-6">

    <div class="network_container_outer" data-value="<?= $rede->id ?>" >
        <div class="network_container_inner">

            <div>
                <img src="<?= $rede->nome_img ?>" class="network_image"/>

                    <span class="network_title">
                        <?= $rede->nome_rede ?>
                    </span>
                </div>
            </div>

            <div class="network_info">
                <span class="network_total_points">
                    Total de pontos na rede: <?= $rede->soma_pontos ?>
                </span>
                    <?= $this->Html->tag(
                        'div',
                        __(
                            '{0}Ver detalhes',
                            $this->Html->tag('i', '', ['class' => 'fa fa-question-circle'])
                        ),
                        [
                            'class' => 'btn btn-default pull-right',
                            'id' => 'submit_button',
                            'escape' => false
                        ]
                    )
                    ?>
            </div>
        </div>
    </div>

<?php endforeach; ?>

</section>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->css('styles/pages/buscar') ?>
    <?= $this->Html->script('scripts/pages/buscar') ?>
<?php else : ?> 
    <?= $this->Html->css('styles/pages/buscar.min') ?>
    <?= $this->Html->script('scripts/pages/buscar.min') ?>
<?php endif; ?>

<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>