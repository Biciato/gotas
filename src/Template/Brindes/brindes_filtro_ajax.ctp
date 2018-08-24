<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/brindes_filtro_ajax.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;

$usuarioVendaAvulsa = isset($usuarioVendaAvulsa) ? $usuarioVendaAvulsa : false;
?>
    <div class="gifts-query-region">
        <h4>Selecione um brinde</h4>
        <div class="row">
            <div class="col-lg-6">
                <?= $this->Form->text(
                    'restrict_query',
                    array(
                        'id' => 'restrict_query',
                        'value' => true,
                        'style' => 'display: none;'
                    )
                ); ?>
                <?= $this->Form->input(
                    'lista_brindes',
                    array(
                        'type' => 'select',
                        'id' => 'lista_brindes',
                        'class' => 'form-control list-gifts',
                        'label' => 'Brinde',
                        'required' => true
                    )
                ) ?>
                <?= $this->Form->text('brindes_id', ['id' => 'brindes_id', 'style' => 'display: none;']); ?>
                <?= $this->Form->text('preco', ['readonly' => true, 'required' => false, 'label' => false, 'id' => 'preco_banho', 'style' => 'display:none;']) ?>
            </div>

            <div class="col-lg-6" >
                <label for="gift-image">Imagem do Brinde</label>
                <br />
                <?= $this->Html->image(
                    "/",
                    array(
                        "name" => "gift-image",
                        "class" => "gift-image",
                        "label" => "Imagem do Brinde",
                    )
                ) ?>
            </div>
        </div>
        <div class="row">
            <!-- Se for usuário de venda avulsa: -->
            <?php if ($usuarioVendaAvulsa) : ?>
                <div class="col-lg-6">
                    <?= $this->Form->input(
                        'quantidade',
                        array(
                            'type' => 'number',
                            'readonly' => false,
                            'required' => true,
                            'label' => 'Quantidade (Se Não é SMART Shower)',
                            'min' => 1,
                            'id' => 'quantidade',
                            'class' => 'quantidade-brindes',
                            'step' => 1.0,
                            'default' => 0,
                            'min' => 0
                        )
                    ) ?>
                </div>

            <?php else : ?>
                <div class="col-lg-4">
                    <?= $this->Form->input('quantidade', [
                        'type' => 'number',
                        'readonly' => false,
                        'required' => true,
                        'label' => 'Quantidade (Se Não é SMART Shower)',
                        'min' => 1,
                        'id' => 'quantidade',
                        'class' => 'quantidade-brindes',
                        'step' => 1.0,
                        'default' => 0,
                        'min' => 0
                    ]) ?>
                </div>

                <div class="col-lg-2">
                    <?= $this->Form->input(
                        'current_password',
                        [
                            'type' => 'password',
                            'id' => 'current_password',
                            'class' => 'current_password',
                            'label' => 'Senha do usuário'
                        ]
                    ) ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/brindes/brindes_filtro_ajax') ?>
    <?= $this->Html->css('styles/brindes/brindes_filtro_ajax') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/brindes/brindes_filtro_ajax.min') ?>
    <?= $this->Html->css('styles/brindes/brindes_filtro_ajax.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>
