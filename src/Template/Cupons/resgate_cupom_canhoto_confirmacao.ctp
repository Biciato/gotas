<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/resgate_cupom_canhoto_confirmacao_confirmacao.ctp
 * @date     22/04/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>


<div class="container-confirmacao-emissao-canhoto">
    <legend>Confirmação de emissão</legend>

    <h4>O Canhoto foi emitido com sucesso?</h4>

    <div class="form-group row">
        <div class="col-lg-2"></div>
        <div class="col-lg-3">
            <!-- Sim Impressão Brinde Comum  -->

            <?= $this->Html->link(
                __("{0} Sim", $this->Html->tag('i', '', ['class' => 'fa fa-check'])),
                ['controller' => 'cupons', 'action' => 'resgate_cupons'],
                ['class' => 'btn btn-primary btn-block', 'escape' => false]
            ); ?>

        </div>
        <div class="col-lg-2"></div>
        <div class="col-lg-3">
            <!-- Reimprimir Canhoto  -->

            <?= $this->Html->tag('button', __("{0} Não, Reimprimir", $this->Html->tag('i', '', ['class' => 'fa fa-remove'])), [
                'id' => 'reimpressao-canhoto',
                'class' => 'reimpressao-canhoto btn btn-danger btn-block'
            ]) ?>

        </div>
        <div class="col-lg-2"></div>

    </div>
</div>

<?php if (Configure::read('debug')) : ?>
    <?= $this->Html->css('styles/cupons/resgate_cupom_canhoto_confirmacao') ?>
    <?= $this->Html->script('scripts/cupons/resgate_cupom_canhoto_confirmacao') ?>
<?php else : ?> 
    <?= $this->Html->css('styles/cupons/resgate_cupom_canhoto_confirmacao.min') ?>
    <?= $this->Html->script('scripts/cupons/resgate_cupom_canhoto_confirmacao.min') ?>

<?php endif; ?> 