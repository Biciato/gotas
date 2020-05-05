<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-4">
        <h2>Adicionar Rede</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#/">Início</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#/redes/index">Redes</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Adicionar Rede</strong>
            </li>
        </ol>
    </div>
</div>

<div class="content">
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
                                            <input type="text" name="nome_rede" id="nome_rede" class="form-control"
                                                placeholder="Nome da Rede..." title="Nome da Rede*" autofocus
                                                required />
                                        </div>

                                        <div class="col-lg-3">
                                            <label for="quantidade_pontuacoes_usuarios_dia">Máx. Abast. Gotas Diárias p/
                                                Usuário*</label>
                                            <input type="number" min="1" max="365"
                                                placeholder="Máx. Abast. Gotas Diárias p/ Usuário..."
                                                class="form-control" name="quantidade_pontuacoes_usuarios_dia"
                                                title="Máximo Abastecimento Gotas Diárias para Usuário" required
                                                value="3" />
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="quantidade_consumo_usuarios_dia">
                                                Máx. Compras Diárias p/ Usuário*
                                            </label>
                                            <input type="text" min="1" max="365"
                                                placeholder="Máximo de Compras Diárias p/ Usuário*"
                                                title="Máximo de Compras Diárias para Usuário" class="form-control"
                                                name="quantidade_consumo_usuarios_dia" required value="10" />
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="qte_mesmo_brinde_resgate_dia">
                                                Máx. Resgates Mesmo Brinde / Dia*
                                            </label>
                                            <input type="text" min="1" max="10"
                                                placeholder="Máximo Resgates Mesmo Brinde / Dia*"
                                                title="Máximo Resgates Mesmo Brinde por Dia para mesmo Usuário"
                                                class="form-control" name="qte_mesmo_brinde_resgate_dia" required
                                                value="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-4">
                                            <label for="tempo_expiracao_gotas_usuarios">Tempo Expiracao de Pontos dos
                                                Usuários (meses)*</label>
                                            <input type="number" min="1" max="99999"
                                                name="tempo_expiracao_gotas_usuarios"
                                                id="tempo-expiracao-gotas-usuarios"
                                                placeholder="Tempo Expiracao de Pontos dos Usuários..."
                                                title="Tempo Expiracao de Pontos dos Usuários (em meses)"
                                                required="required" value="6" class="form-control" />
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="custo_referencia_gotas">Custo Referência Gotas
                                                (R$)*</label>
                                            <input type="text" name="custo_referencia_gotas" id="custo_referencia_gotas"
                                                placeholder="Custo Referência Gotas (R$)..."
                                                title="Custo Referência Gotas (R$)" required="required" value=""
                                                class="form-control" />
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="media_assiduidade_clientes">Média Assid. Clientes
                                                (Mês)*</label>
                                            <input type="number" min="1" max="30" name="media_assiduidade_clientes"
                                                required="required" title="Média Assiduidade Clientes (Mês)"
                                                id="media_assiduidade_clientes" class="form-control" value=""
                                                placeholder="Media de Assiduidade Clientes (Mês)" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-6">
                                            <label for="qte_gotas_minima_bonificacao">Qte. Litros Mínima para Atingir
                                                Bonificação</label>
                                            <input type="number" name="qte_gotas_minima_bonificacao"
                                                id="qte_gotas_minima_bonificacao"
                                                placeholder="Qte. Litros Mínima para Atingir Bonificação"
                                                title="Qte. Litros Mínima para Atingir Bonificação" required="required"
                                                value="" class="form-control" />
                                        </div>

                                        <div class="col-lg-6">
                                            <label for="qte_gotas_bonificacao">Qte. Bonificação na Importação
                                                da SEFAZ</label>
                                            <input type="number" name="qte_gotas_bonificacao" required="required"
                                                id="qte_gotas_bonificacao" class="form-control" value=""
                                                placeholder="Qte. Bonificação na Importação da SEFAZ"
                                                title="Qte. Bonificação na Importação da SEFAZ" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="redes-image">
                                <div class="ibox-content">
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <label for="nome-img">Logo da Rede</label>
                                            <input type="file" name="nome_img" id="nome-img" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="img-crop-container">
                                            <div class="col-lg-12">
                                                <h5 style="font-weight: bold;">
                                                    Imagem da Rede para Exibição:
                                                </h5>
                                                <img src="" id="img-crop" class="img-crop" name="img_crop" />
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div>
                                            <div class="col-lg-12">
                                                <h5 style="font-weight: bold;">
                                                    Preview da Imagem:
                                                </h5>
                                                <div id="img-crop-preview" class="img-crop-preview" name="teste"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="hidden">
                                        <div class="row">
                                            <div>
                                                <div class="col-lg-12">
                                                    <input type="text" class="img-upload" id="img-upload" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-height" id="crop-height" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-width" id="crop-width" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-x1" id="crop-x1" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-x2" id="crop-x2" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-y1" id="crop-y1" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-y2" id="crop-y2" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="common-options">
                                <h5>Opções Gerais</h5>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="ativado">
                                            <input type="checkbox" name="ativado" id="ativado" />
                                            Rede Ativada
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="api-options">
                                <h5>
                                    Opções de Aplicativo Mobile Personalizado
                                </h5>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="app-personalizado">
                                            <input type="checkbox" id="app-personalizado" name="app_personalizado"
                                                class="app_personalizado" value="1">
                                            Rede com APP Personalizado?
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="msg-distancia-compra-brinde">
                                            <input type="checkbox" id="msg-distancia-compra-brinde"
                                                name="msg_distancia_compra_brinde" class="items_app_personalizado"
                                                value="1">
                                            Exibir Mensagem de Distância ao Comprar
                                        </label>
                                    </div>
                                </div>
                                <h5>
                                    Opções de Integração entre Sistemas de Postos
                                </h5>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="pontuacao-extra-produto-generico">
                                            <input type="checkbox" id="pontuacao-extra-produto-generico"
                                                name="pontuacao_extra_produto_generico" value="1" title="Adiciona
                                            Pontos/Gotas para Produtos que não
                                            estão cadastrados no Sistema, ao
                                            importar o Cupom Fiscal.">
                                            Atribuir Pontos Extras para Gotas Não Cadastradas?
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <div href="#" class="btn btn-default" tooltip="Cancelar" id="btn-cancel">
                                <i class="fas fa-times"></i> Cancelar
                            </div>
                            <div class="btn btn-primary" tooltip="Salvar" id="btn-save">
                                <i class="fas fa-save"></i> Salvar
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/webroot/css/styles/redes/add.css">
<script>
    $(document).ready(function () {
        redesAdd.init();
    });
</script>
