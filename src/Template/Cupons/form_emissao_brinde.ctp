<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/form_emissao_brinde.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$show_breadcrumbs = isset($show_breadcrumbs) ? $show_breadcrumbs : true;

if ($show_breadcrumbs) {

    $this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
    $this->Breadcrumbs->add('Escolher Brinde', ['controller' => 'cupons', 'action' => 'escolher_brinde']);
    $this->Breadcrumbs->add('Emissão de Cupom Smart Shower', [], ['class' => 'active']);

    echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);
}

$showMenu = isset($showMenu) ? $showMenu : true;

$urlRedirectConfirmacao = empty($urlRedirectConfirmacao) ? array("controller" => "pages", "action" => "display") : $urlRedirectConfirmacao;
$tipoPagamento = !empty($tipoPagamento) ? $tipoPagamento : false;
?>

<?php if ($showMenu) : ?>

    <?= $this->element(
        '../Cupons/left_menu',
        [
            'mode' => 'print',
            'controller' => 'Cupons',
            'action' => 'emissao_brinde'
        ]
    ) ?>

    <div class="col-lg-9 col-md-10 columns">

    <?php else : ?>
        <div class="col-lg-9 col-md-10 columns">
        <?php endif; ?>

        <div class="container-emissao-cupom">
            <legend><?= __("Resgate de Brinde") ?></legend>

            <?= $this->Form->create(); ?>
            <div>
                <?php echo $this->element("../Usuarios/filtro_usuarios_ajax", array("isVendaAvulsa" => 0)) ?>
            </div>

            <?= $this->Form->text('clientes_id', ['id' => 'clientes_id', 'value' => $cliente->id, 'style' => 'display: none;']); ?>

            <input type="hidden" name='cupom-emitido' id="cupom-emitido">

            <?= $this->element('../Brindes/brindes_filtro_ajax', array('mostrarCheckboxDesconto' => $tipoPagamento)) ?>

            <div class="gifts-query-region">

                <div class="col-lg-12 text-right">
                    <button type="button" id="print_gift" class="print-gift-shower
                            btn btn-primary">
                        <i class="fa fa-print"></i>
                        Imprimir
                    </button>

                    <button type="button" class="print-gift-cancel btn btn-default" id="print-gift-cancel">
                        <i class="fa fa-trash"></i>
                        Limpar
                    </button>

                    <?= $this->Html->tag('div', '', ['class' => 'text-danger validation-message', 'id' => 'print-validation']) ?>
                    <?= $this->Html->tag('/div') ?>
                </div>
            </div>

            <?= $this->Form->end(); ?>

            <!-- Confirmação cupom -->
            <?php
            echo $this->element("../Cupons/confirmacao_emissao_cupom");
            ?>

            <!-- Confirmação canhoto -->
            <?php
            echo $this->element("../Cupons/confirmacao_canhoto");
            ?>

            <?php
            echo $this->element("../Cupons/validar_brinde_canhoto_confirmacao");
            ?>
        </div>

        </div>

        <?php
        echo $this->element('../Cupons/impressao_brinde_layout');
        ?>

        <?php
        echo $this->element("../Cupons/impressao_canhoto_layout");
        ?>



        <?= $this->element('../Cupons/validar_brinde_canhoto_impressao') ?>

        <?php
        $extension = Configure::read("debug") ? ""  : ".min";
        ?>
        <script src="/webroot/js/scripts/cupons/imprime_brinde<?= $extension ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
        <link rel="stylesheet" href="/webroot/css/styles/cupons/imprime_brinde<?= $extension ?>.css?<?= SYSTEM_VERSION ?>">
