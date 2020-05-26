<?php

/**
 * Template para cadastro de propaganda e link de quando for rede e clientes
 *
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/redes/configurar_parametros_rede.ctp
 * @since    2019-07-22
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$title = "Configurar Parâmetros de Rede";
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add($title, array(), array("class" => "active"));

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Redes/left_menu') ?>

<div class="redes form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($rede) ?>
    <fieldset>
        <legend><?= $title ?></legend>
        <div class="form-group row">
            <div class="col-lg-4">
                <label for="quantidade_pontuacoes_usuarios_dia">Máx. Abast. Gotas Diárias p/ Usuário*</label>
                <input type="number" min="1" max="365" placeholder="Máx. Abast. Gotas Diárias p/ Usuário..." class="form-control" name="quantidade_pontuacoes_usuarios_dia" title="Máximo Abastecimento Gotas Diárias para Usuário" required value="<?= $rede->quantidade_pontuacoes_usuarios_dia ?>" />
            </div>
            <div class="col-lg-4">
                <label for="quantidade_consumo_usuarios_dia">Máximo de Compras Diárias p/ Usuário*</label>
                <input type="text" min="1" max="365" placeholder="Máximo de Compras Diárias p/ Usuário*" title="Máximo de Compras Diárias para Usuário" class="form-control" name="quantidade_consumo_usuarios_dia" required value="<?= $rede->quantidade_consumo_usuarios_dia ?>" />
            </div>
            <div class="col-lg-4">
                <label for="tempo_expiracao_gotas_usuarios">Tempo Expiração Pontos Usuarios (Mês)*</label>
                <input type="text" min="1" max="365" placeholder="Tempo Expiração Pontos Usuarios (Mês)..." title="Tempo Expiração Pontos Usuarios (Mês)" class="form-control" name="tempo_expiracao_gotas_usuarios" required value="<?= $rede->tempo_expiracao_gotas_usuarios ?>" />
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-6">
                <label for="custo_referencia_gotas">Custo Referência Gotas (R$)*</label>
                <input type="text" name="custo_referencia_gotas" id="custo_referencia_gotas" placeholder="Custo Referência Gotas (R$)..." title="Custo Referência Gotas (R$)" required="required" value="<?= $rede->custo_referencia_gotas ?>" class="form-control" />

            </div>

            <div class="col-lg-6">
                <label for="media_assiduidade_clientes">Média Assid. Clientes (Mês)*</label>
                <input type="number" min="1" max="30" name="media_assiduidade_clientes" required="required" title="Média Assiduidade Clientes (Mês)" id="media_assiduidade_clientes" class="form-control" value="<?= $rede->media_assiduidade_clientes ?>" placeholder="Media de Assiduidade Clientes (Mês)" />

            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6">
                <label for="qte_gotas_minima_bonificacao">Qte. Litros Mínima para Atingir Bonificação</label>
                <input type="number" name="qte_gotas_minima_bonificacao" id="qte_gotas_minima_bonificacao" placeholder="Qte. Litros Mínima para Atingir Bonificação" title="Qte. Litros Mínima para Atingir Bonificação" required="required" value="<?= $rede->qte_gotas_minima_bonificacao ?>" class="form-control" />
            </div>

            <div class="col-lg-6">
                <label for="qte_gotas_bonificacao">Qte. Bonificação na Importação da SEFAZ</label>
                <input type="number" name="qte_gotas_bonificacao" required="required" id="qte_gotas_bonificacao" class="form-control" value="<?= $rede->qte_gotas_bonificacao ?>" placeholder="Qte. Bonificação na Importação da SEFAZ" title="Qte. Bonificação na Importação da SEFAZ" />
            </div>
        </div>

        <div class="form-group row ">
            <div class="col-lg-12 text-right">
                <button type="submit" class="btn btn-primary botao-confirmar">
                    <span class="fa fa-save"></span>
                    Salvar
                </button>
                <a onclick="history.go(-1); return false;" class="btn btn-danger botao-cancelar">
                    <span class="fa fa-window-close"></span>
                    Cancelar
                </a>
            </div>
        </div>
    </fieldset>

    <?= $this->Form->end() ?>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/redes/configurar_parametros_rede'); ?>
<?php else : ?>
    <?= $this->Html->script('scripts/redes/configurar_parametros_rede.min'); ?>
<?php endif; ?>