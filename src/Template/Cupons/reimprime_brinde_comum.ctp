<?php 
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/imprimir_brinde_comum.ctp
 * @date     28/01/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($user_logged['tipo_perfil'] <= (int)Configure::read('profileTypes')['WorkerProfileType']) {
    $this->Breadcrumbs->add('Cupons de Brinde do Usuário', ['controller' => 'usuarios_has_brindes', 'action' => 'historico_brindes', $usuario->id]);
} else {
    $this->Breadcrumbs->add('Meu Histórico de Cupons de Brinde', ['controller' => 'usuarios_has_brindes', 'action' => 'historico_brindes']);
}

$this->Breadcrumbs->add('Detalhes do Cupom', ['controller' => 'Cupons', 'action' => 'ver_detalhes', $cupom_id]);

$this->Breadcrumbs->add('Reimpressão do Cupom', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?> 

<?= $this->element('../ClientesHasBrindesHabilitados/left_menu', []) ?> 

<div class="redes index col-lg-9 col-md-10 columns content">
    <legend>Reimpressão do Cupom para Resgatar os Brindes</legend>

    <div class="col-lg-4">
        <?= $this->element('../Cupons/impressao_cupom_layout') ?>
    
        <?= $this->Html->tag(
            'div',
            __(
                "{0} Imprimir",
                $this->Html->tag('i', '', ['class' => 'fa fa-print'])
            ),
            ['class' => 'btn btn-primary print-button']
        ) ?>
    </div>
</div>

<?php if (Configure::read('debug')) : ?> 
    <?= $this->Html->script('scripts/cupons/imprimir_brinde_comum') ?> 

<?php else : ?> 
    <?= $this->Html->script('scripts/cupons/imprimir_brinde_comum.min') ?> 

<?php endif; ?>

<?= $this->fetch('script') ?>