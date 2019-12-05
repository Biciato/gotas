<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Redes/redes_form.ctp
 * @date     22/11/2017
 */

use Cake\Core\Configure;
?>

<div class="form-group row">
    <div class="col-lg-4">
        <label for="nome_rede">Nome da Rede*</label>
        <input type="text" name="nome_rede" id="nome_rede" class="form-control" value="<?= $rede['nome_rede'] ?>" placeholder="Nome da Rede..." title="Nome da Rede*" autofocus required />
    </div>

    <div class="col-lg-4">
        <label for="quantidade_pontuacoes_usuarios_dia">Máx. Abast. Gotas Diárias p/ Usuário*</label>
        <input type="number" min="1" max="365" placeholder="Máx. Abast. Gotas Diárias p/ Usuário..." class="form-control" name="quantidade_pontuacoes_usuarios_dia" title="Máximo Abastecimento Gotas Diárias para Usuário" required value="<?= $rede['quantidade_pontuacoes_usuarios_dia'] ?>" />
    </div>
    <div class="col-lg-4">
        <label for="quantidade_consumo_usuarios_dia">Máximo de Compras Diárias p/ Usuário*</label>
        <input type="text" min="1" max="365" placeholder="Máximo de Compras Diárias p/ Usuário*" title="Máximo de Compras Diárias para Usuário" class="form-control" name="quantidade_consumo_usuarios_dia" required value="<?= $rede['quantidade_consumo_usuarios_dia'] ?>" />
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-6">
        <label for="custo_referencia_gotas">Custo Referência Gotas (R$)*</label>
        <input type="text" name="custo_referencia_gotas" id="custo_referencia_gotas" placeholder="Custo Referência Gotas (R$)..." title="Custo Referência Gotas (R$)" required="required" value="<?= $rede["custo_referencia_gotas"] ?>" class="form-control" />

    </div>

    <div class="col-lg-6">
        <label for="media_assiduidade_clientes">Média Assid. Clientes (Mês)*</label>
        <input type="number" min="1" max="30" name="media_assiduidade_clientes" required="required" title="Média Assiduidade Clientes (Mês)" id="media_assiduidade_clientes" class="form-control" value="<?= $rede["media_assiduidade_clientes"] ?>" placeholder="Media de Assiduidade Clientes (Mês)" />

    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label for="qte_gotas_minima_bonificacao">Qte. Litros Mínima para Atingir Bonificação</label>
        <input type="number" name="qte_gotas_minima_bonificacao" id="qte_gotas_minima_bonificacao" placeholder="Qte. Litros Mínima para Atingir Bonificação" title="Qte. Litros Mínima para Atingir Bonificação" required="required" value="<?= $rede["qte_gotas_minima_bonificacao"] ?>" class="form-control" />
    </div>

    <div class="col-lg-6">
        <label for="qte_gotas_bonificacao">Qte. Bonificação na Importação da SEFAZ</label>
        <input type="number" name="qte_gotas_bonificacao" required="required" id="qte_gotas_bonificacao" class="form-control" value="<?= $rede["qte_gotas_bonificacao"] ?>" placeholder="Qte. Bonificação na Importação da SEFAZ" title="Qte. Bonificação na Importação da SEFAZ" />
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->input('nome_img', ['type' => 'file', 'label' => 'Logo da Rede']) ?>
    </div>
</div>

<div class="form-group row">
    <div class="img-crop-container">
        <div class="col-lg-6">
            <h5 style="font-weight: bold;">Imagem da Rede para Exibição:</h5>
            <img src="" id="img-crop" class="img-crop" name="img_crop" />
        </div>

        <div class="col-lg-6">
            <h5 style="font-weight: bold;">Preview da Imagem:</h5>
            <div id="img-crop-preview" class="img-crop-preview" name="teste"></div>
        </div>
    </div>
</div>

<h4>Opções Gerais</h4>
<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->control('ativado', ['label' => 'Rede Ativada']); ?>
    </div>
</div>
<h4>Opções de Aplicativo Mobile Personalizado</h4>
<div class="form-group row">
    <div class="col-lg-12">
        <input type="hidden" name='app_personalizado' value='0'>
        <label for="app_personalizado">
            <input type="checkbox" <?= $rede->app_personalizado ? 'checked' : '' ?> id="app_personalizado" name="app_personalizado" class="app_personalizado" value="1">
            Rede com APP Personalizado?
        </label>
    </div>
    <div class="col-lg-12">
        <input type="hidden" name='msg_distancia_compra_brinde' value='0'>
        <label for="msg_distancia_compra_brinde">
            <input type="checkbox" <?= $rede->msg_distancia_compra_brinde ? 'checked' : '' ?> id="msg_distancia_compra_brinde" name="msg_distancia_compra_brinde" class="items_app_personalizado" value="1">
            Exibir Mensagem de Distância ao Comprar
        </label>

    </div>
    <div class="col-lg-12">
        <input type="hidden" name='app_personalizado' value='0'>
        <label for="app_personalizado">
            <input type="checkbox" <?= $rede->app_personalizado ? 'checked' : '' ?> id="app_personalizado" name="app_personalizado" class="app_personalizado" value="1">
            Rede com APP Personalizado?
        </label>
    </div>
</div>
<h4>Opções de Integração entre Sistemas de Postos</h4>
<div class="form-group row">
    <div class="col-lg-12">
        <input type="hidden" name='pontuacao_extra_produto_generico' value='0'>
        <label for="pontuacao_extra_produto_generico">
            <input type="checkbox" <?= $rede->pontuacao_extra_produto_generico ? 'checked' : '' ?> id="pontuacao_extra_produto_generico" name="pontuacao_extra_produto_generico" value="1" title="Adiciona Pontos/Gotas para Produtos que não estão cadastrados no Sistema, ao importar o Cupom Fiscal.">
            Atribuir Pontos Extras para Gotas não Cadastradas?
        </label>

    </div>
</div>

<?php if ($imagemOriginal) : ?>
    <div class="form-group row">
        <div class="col-lg-12">
            <label>Imagem Atual da Rede</label>
            <div><img src="<?= $imagemOriginal ?>" alt="Imagem da Rede" class="imagem-rede" width="400px" height="300px"></div>
        </div>
    </div>
<?php endif; ?>

<div class="hidden">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->Form->input('img-upload', ["type" => "text", "label" => false, "id" => "img-upload", "class" => "img-upload", "readonly" => true]) ?>
        </div>
    </div>
    <div class="row ">
        <div class="col-lg-2">
            <?= $this->Form->input('crop-height', ['type' => 'text', 'label' => 'crop height', 'id' => 'crop-height', 'readonly' => true]); ?>
        </div>
        <div class="col-lg-2">
            <?= $this->Form->input('crop-width', ['type' => 'text', 'label' => 'crop width', 'id' => 'crop-width', 'readonly' => true]); ?>
        </div>
        <div class="col-lg-2">
            <?= $this->Form->input('crop-x1', ['type' => 'text', 'label' => 'crop x', 'id' => 'crop-x1', 'readonly' => true]); ?>
        </div>
        <div class="col-lg-2">
            <?= $this->Form->input('crop-x2', ['type' => 'text', 'label' => 'crop x', 'id' => 'crop-x2', 'readonly' => true]); ?>
        </div>
        <div class="col-lg-2">
            <?= $this->Form->input('crop-y1', ['type' => 'text', 'label' => 'crop y', 'id' => 'crop-y1', 'readonly' => true]); ?>
        </div>
        <div class="col-lg-2">
            <?= $this->Form->input('crop-y2', ['type' => 'text', 'label' => 'crop y', 'id' => 'crop-y2', 'readonly' => true]); ?>
        </div>
    </div>
</div>

<div class="col-lg-12 text-right">
    <button type="submit" class="btn btn-primary  botao-confirmar"><span class="fa fa-save"></span> Salvar</button>
    <a href="/redes/index" class="btn btn-danger botao-cancelar">
        <span class="fa fa-window-close"></span>
        Cancelar
    </a>
</div>

<?php if (Configure::read('debug')) : ?>
    <?= $this->Html->script('scripts/redes/redes_form'); ?>
    <?= $this->Html->css("styles/redes/redes_form"); ?>
<?php else : ?>
    <?= $this->Html->script('scripts/redes/redes_form.min'); ?>
    <?= $this->Html->css("styles/redes/redes_form.min"); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>
<?= $this->fetch('css'); ?>
