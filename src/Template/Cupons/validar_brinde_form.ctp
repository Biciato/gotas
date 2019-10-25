<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/resgate_cupom_form.ctp
 * @date     30/01/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>


<div class="resgate-cupom-main">

    <legend>Validação de Brinde: </legend>

    <div class="col-lg-9">
        <?= $this->Form->input('pdf417_code', [
            'type' => 'text',
            'class' => 'pdf-417-code',
            'max' => 14,
            'label' => 'Informe Código de Leitura*',
            'placeholder' => 'Informe Código de Leitura...',
            "required" => true,
            'autocomplete' => 'off'
        ]) ?>

        <?= $this->Html->tag(
            'div',
            __(
                "{0} Limpar",
                $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
            ),
            [
                'class' => 'btn btn-primary limpar-pdf-417-code',
                'escape' => false
            ]
        ) ?>

    </div>
</div>

<div class="resgate-cupom-result">
    <div class="container-emissao-resgate-cupom">
        <legend>Confirme os dados para resgate</legend>

        <div class="col-lg-9">
            <h4>Cliente:</h4>
            <div class="col-lg-4">
                <?= $this->Form->input(
                    'nome',
                    [
                        'type' => 'text',
                        'class' => 'nome-cliente-brinde-resgate',
                        'readonly' => true,
                        'label' => 'Nome'
                    ]
                ) ?>
            </div>

            <div class="col-lg-4">
                <?= $this->Form->input(
                    'cpf-cliente-brinde-resgate',
                    [
                        'type' => 'text',
                        'class' => 'cpf-cliente-brinde-resgate',
                        'readonly' => true,
                        'label' => 'CPF'
                    ]
                ) ?>
            </div>

            <div class="col-lg-4">
                <?= $this->Form->input(
                    'data_nasc',
                    [
                        'type' => 'text',
                        'class' => 'data-nasc-cliente-brinde-resgate',
                        'readonly' => true,
                        'label' => 'Data de Nascimento'

                    ]
                ) ?>
            </div>


            <?= $this->Form->input(
                'cupom_resgatar',
                [
                    'type' => 'text',
                    'class' => 'hidden cupom-resgatar',
                    'label' => false
                ]
            ) ?>

            <?= $this->Form->input(
                'unidade_funcionario_id',
                [
                    'type' => 'text',
                    'class' => 'hidden unidade-funcionario-id',
                    'label' => false
                ]
            ) ?>

            <h4>Brindes solicitados</h4>

            <div class="col-lg-12">
                <table class="table table-hover table-condensed table-responsive table-bordered tabela-produtos ">
                    <thead>
                        <tr>
                            <td><?= 'Qtd.' ?></td>
                            <td><?= 'Nome' ?></td>
                            <td><?= 'Valor Pago Gotas' ?></td>
                            <td><?= 'Valor Pago Reais' ?></td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="col-lg-12">
                <?= $this->Html->tag('div', __(
                    "{0} Resgatar",
                    $this->Html->tag('i', '', ['class' => 'fa fa-checkout'])
                ), ['class' => 'btn btn-primary resgatar-cupom', 'escape' => false]) ?>
            </div>

        </div>
    </div>
</div>

<?php if (Configure::read('debug')) : ?>
    <?= $this->Html->css('styles/cupons/validar_brinde_form') ?>
    <?= $this->Html->script('scripts/cupons/validar_brinde_form') ?>
<?php else : ?>
    <?= $this->Html->css('styles/cupons/validar_brinde_form.min') ?>
    <?= $this->Html->script('scripts/cupons/validar_brinde_form.min') ?>
<?php endif; ?>

<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>
