<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rede[]|\Cake\Collection\CollectionInterface $redes
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$extensionDebug = Configure::read("debug") ? "" : ".min";
// $this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

// $this->Breadcrumbs->add('Redes', [], ['class' => 'active']);

// echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);
?>

<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-4">
        <h2>Lista de Redes</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <?= $this->Html->link("Início", ['controller' => "Pages", "action" => "redes"]); ?>
            </li>
            <li class="breadcrumb-item active">
                <strong>Redes</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-8">
        <div class="title-action">
            <a href="#" class="btn btn-primary" tooltip="Salvar" id="redes-new-btn-show"> <i class="fas fa-plus"></i> Novo</a>
        </div>


    </div>
</div>

<div class="content">
    <div class="row redes-index">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Redes') ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="panel-group">
                        <div class="panel panel-default">
                            <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
                                <div>
                                    <span class="fa fa-search"></span>
                                    Exibir / Ocultar Filtros
                                </div>
                            </div>
                            <div id="filter-coupons" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <form id="form">

                                        <div class="form-group row">
                                            <div class="col-lg-4">
                                                <label for="nome_rede">Nome:</label>
                                                <input type="text" name="nome_rede" id="nome-rede" class="form-control">
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="ativado">Ativado:</label>
                                                <select name="ativado" id="ativado" class="form-control">
                                                    <option value="">Todos</option>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="app_personalizado">Aplicativo Personalizado:</label>
                                                <select name="app_personalizado" id="app-personalizado" class="form-control">
                                                    <option value="" selected>Todos</option>
                                                    <option value="1">Sim</option>
                                                    <option value="0">Não</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12 text-right">
                                                <div class="btn btn-primary" id="btn-search">
                                                    <span class="fa fa-search"></span>
                                                    Pesquisar
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover" id="data-table">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Add -->

    <div class="row redes-add-form">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Adicionar Rede</h5>
                </div>
                <div class="ibox-content">
                    <form name="form_redes_add" id="form-redes-add">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#general" data-toggle="tab">Dados Gerais</a>
                            </li>
                            <li>
                                <a href="#redes-image" data-toggle="tab">Imagem</a>
                            </li>
                            <li>
                                <a href="#common-options" data-toggle="tab">Opções</a>
                            </li>
                            <li>
                                <a href="#api-options" data-toggle="tab">Serviços Mobile / API</a>
                            </li>
                        </ul>

                        <div class="tab-content clearfix">
                            <div class="tab-pane active" id="general">
                                <div class="ibox-content">
                                    <div class="form-group row">
                                        <div class="col-lg-3">
                                            <label for="nome_rede">Nome da Rede*</label>
                                            <input type="text" name="nome_rede" id="nome_rede" class="form-control" value="" placeholder="Nome da Rede..." title="Nome da Rede*" autofocus required />
                                        </div>

                                        <div class="col-lg-3">
                                            <label for="quantidade_pontuacoes_usuarios_dia">Máx. Abast. Gotas Diárias p/ Usuário*</label>
                                            <input type="number" min="1" max="365" placeholder="Máx. Abast. Gotas Diárias p/ Usuário..." class="form-control" name="quantidade_pontuacoes_usuarios_dia" title="Máximo Abastecimento Gotas Diárias para Usuário" required value="3" />
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="quantidade_consumo_usuarios_dia">Máximo de Compras Diárias p/ Usuário*</label>
                                            <input type="text" min="1" max="365" placeholder="Máximo de Compras Diárias p/ Usuário*" title="Máximo de Compras Diárias para Usuário" class="form-control" name="quantidade_consumo_usuarios_dia" required value="10" />
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="qte_mesmo_brinde_resgate_dia">Máximo Resgates Mesmo Brinde / Dia*</label>
                                            <input type="text" min="1" max="10" placeholder="Máximo Resgates Mesmo Brinde / Dia*" title="Máximo Resgates Mesmo Brinde por Dia para mesmo Usuário" class="form-control" name="qte_mesmo_brinde_resgate_dia" required value="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-4">
                                            <label for="tempo_expiracao_gotas_usuarios">Tempo Expiracao de Pontos dos Usuários (meses)*</label>
                                            <input type="number" min="1" max="99999" name="tempo_expiracao_gotas_usuarios" id="tempo-expiracao-gotas-usuarios" placeholder="Tempo Expiracao de Pontos dos Usuários..." title="Tempo Expiracao de Pontos dos Usuários (em meses)" required="required" value="6" class="form-control" />
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="custo_referencia_gotas">Custo Referência Gotas (R$)*</label>
                                            <input type="text" name="custo_referencia_gotas" id="custo_referencia_gotas" placeholder="Custo Referência Gotas (R$)..." title="Custo Referência Gotas (R$)" required="required" value="" class="form-control" />
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="media_assiduidade_clientes">Média Assid. Clientes (Mês)*</label>
                                            <input type="number" min="1" max="30" name="media_assiduidade_clientes" required="required" title="Média Assiduidade Clientes (Mês)" id="media_assiduidade_clientes" class="form-control" value="" placeholder="Media de Assiduidade Clientes (Mês)" />

                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-6">
                                            <label for="qte_gotas_minima_bonificacao">Qte. Litros Mínima para Atingir Bonificação</label>
                                            <input type="number" name="qte_gotas_minima_bonificacao" id="qte_gotas_minima_bonificacao" placeholder="Qte. Litros Mínima para Atingir Bonificação" title="Qte. Litros Mínima para Atingir Bonificação" required="required" value="" class="form-control" />
                                        </div>

                                        <div class="col-lg-6">
                                            <label for="qte_gotas_bonificacao">Qte. Bonificação na Importação da SEFAZ</label>
                                            <input type="number" name="qte_gotas_bonificacao" required="required" id="qte_gotas_bonificacao" class="form-control" value="" placeholder="Qte. Bonificação na Importação da SEFAZ" title="Qte. Bonificação na Importação da SEFAZ" />
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane" id="redes-image">
                                <div class="ibox-content">
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
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="common-options">
                                <h5>Opções Gerais</h5>
                                <div class="form-group row">
                                    <input type="hidden" name="checkbox" value="0" />
                                    <div class="col-lg-12">
                                        <label>
                                            <input type="checkbox" name="ativado" id="ativado" /> Rede Ativada
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class=" tab-pane" id="api-options">
                                <h5>Opções de Aplicativo Mobile Personalizado</h5>
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
                                <h5>Opções de Integração entre Sistemas de Postos</h5>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <input type="hidden" name='pontuacao_extra_produto_generico' value='0'>
                                        <label for="pontuacao_extra_produto_generico">
                                            <input type="checkbox" <?= $rede->pontuacao_extra_produto_generico ? 'checked' : '' ?> id="pontuacao_extra_produto_generico" name="pontuacao_extra_produto_generico" value="1" title="Adiciona Pontos/Gotas para Produtos que não estão cadastrados no Sistema, ao importar o Cupom Fiscal.">
                                            Atribuir Pontos Extras para Gotas não Cadastradas?
                                        </label>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="#" class="btn btn-default" tooltip="Cancelar" id="redes-new-btn-cancel"> <i class=" fas fa-times"></i> Cancelar </a>
                            <a href="#" class="btn btn-primary" tooltip="Salvar" id="redes-new-btn-save"> <i class=" fas fa-save"></i> Salvar </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$this->append("script");
echo $this->Html->script(sprintf("scripts/redes/redes%s.js?version=%s", $extensionDebug, SYSTEM_VERSION));
$this->end();
$this->append("css");
echo $this->Html->css(sprintf("styles/redes/redes%s.css?version=%s", $extensionDebug, SYSTEM_VERSION));
$this->end();


?>
