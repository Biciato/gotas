<?php

use Cake\Core\Configure;

$title = isset($title) ? $title : __('Adicionar Veículo');
?>

<?= $this->Form->create($veiculo) ?>
    <fieldset>
        <legend><?= $title ?></legend>
            <div class="form-group row">
                <div class="col-lg-3">
                    <label for="placa">Placa:*</label>
                    <input type="text" 
                        name="placa" 
                        class="form-control" 
                        id="placa" 
                        value="<?= $veiculo["placa"]?>" 
                        required />
                </div>
                <div class="col-lg-3">
                    <label for="modelo">Modelo:*</label>
                    <input type="text" 
                        name="modelo" 
                        class="form-control" 
                        id="modelo" 
                        value="<?= $veiculo["modelo"]?>" 
                        required />
                </div>
                <div class="col-lg-3">
                    <label for="fabricante">Fabricante:*</label>
                    <input type="text" 
                        name="fabricante" 
                        class="form-control" 
                        id="fabricante" 
                        value="<?= $veiculo["fabricante"]?>" 
                        required />
                </div>
                <div class="col-lg-3">
                    <label for="ano">Ano:*</label>
                    <input type="text" 
                        name="ano" 
                        class="form-control" 
                        id="ano" 
                        value="<?= $veiculo["ano"]?>" 
                        required />
                </div>
            </div>

            <?= $this->Html->tag('span', '', ['class' => 'text-danger validation-message']) ?>
            <div class="col-lg-12 text-right">
                <button type="submit" 
                    class="btn btn-primary save-button botao-confirmar">
                    <i class="fa fa-save"></i>
                    Salvar
                </button>
                
                <a href="/" 
                    class="btn btn-danger botao-cancelar">
                    <i class="fa fa-window-close"></i>
                    Cancelar
                </a>
            </div>

            <!-- <?= $this->Form->control('placa', ['id' => 'placa']); ?>

            <?= $this->Html->tag('div', '', ['class' => 'text-danger validation-message']) ?>
            <?= $this->Form->control('modelo', ['id' => 'modelo', 'class' => 'frozen-input-data']); ?>
            <?= $this->Form->control('fabricante', ['id' => 'fabricante', 'class' => 'frozen-input-data']); ?>
            <?= $this->Form->control('ano', ['id' => 'ano', 'class' => 'frozen-input-data']); ?> -->
    </fieldset>

  
    <?= $this->Form->end() ?>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/veiculos/form_cadastrar_veiculo') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/veiculos/form_cadastrar_veiculo.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
