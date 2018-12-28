<?php

/**
* 
*/
/**
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Brindes/brindes_filtro_shower_ajax.ctp
  * @date     18/08/2017
  */

use Cake\Core\Configure;
?>
    <div class="col-lg-12">
        <div class="gifts-query-region">

            <h4>Selecione um brinde</h4>

            <div class="col-lg-2">
                <?= $this->Form->label('Pesquisar por') ?>
            </div>

            <div class="col-lg-8">
                <?= $this->Form->input('parametro_brinde', ['id' => 'parametro_brinde', 'label' => false, 'class' => 'form-control col-lg-5']) ?> 
            </div>

            <div class="col-lg-2">

                <?= $this->Form->button("Pesquisar", ['class' => 'btn btn-primary btn-block botao-pesquisar', 'type' => 'button', 'id'=> 'searchBrinde']) ?>
            </div>

            <span class="text-danger validation-message" id="giftValidationMessage"></span>

        </div>
        

        <div class="gifts-result gifts-result-table" >
            <div class="col-lg-12">
                <table class="table table-striped table-hover" id="gifts-result-table">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Tempo de Banho</th>
                            <th scope="col">Preço em Gotas</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>

        <div class="form gifts-result col-lg-12">
    
            <?= $this->Html->tag('div', ' Pesquisar brinde', ['class' => 'col-lg-2 btn btn-primary btn-xs fa fa-rotate-right', 'type' => 'button', 'id' => 'new-gift-search']) ?>
        
            <h4>Smart Shower selecionado</h4>

            <?= $this->Form->text('brindes_id', ['id' => 'brindes_id', 'style' => 'display: none;']); ?>

            <div class="col-lg-1 col-md-1">
                <?= $this->Form->label('Nome')?>
            </div>
            <div class="col-lg-4 col-md-3">
                <?= $this->Form->input('nome', ['readonly' => true, 'required' => false, 'label' => false, 'id'=> 'brindes_nome']) ?>
            </div>

            <div class="col-lg-2 col-md-2">
            <?= $this->Form->label('Tempo de banho (minutos)') ?>
            </div>
            <div class="col-lg-1 col-md-2">

                <?= $this->Form->input('tempo_uso_brinde', ['readonly' => true, 'required' => false, 'label' => false, 'id' => 'tempo_uso_brinde']) ?>
            </div>

            <div class="col-lg-2 col-md-1">
                <?= $this->Form->label('Preço (Em gotas)') ?>
            </div>
            <div class="col-lg-2 col-md-1">

                <?= $this->Form->input('preco', ['readonly' => true, 'required' => false, 'label' => false, 'id' => 'preco_banho']) ?>
            </div>


        </div>
    </div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/brindes/brindes_filtro_shower_ajax') ?>
    <?= $this->Html->css('styles/brindes/brindes_filtro_shower_ajax') ?>
<?php else: ?> 
    <?= $this->Html->script('scripts/brindes/brindes_filtro_shower_ajax.min') ?>
    <?= $this->Html->css('styles/brindes/brindes_filtro_shower_ajax.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
<?= $this->fetch('css') ?>