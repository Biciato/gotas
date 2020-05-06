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
                                            <label for="nome-rede">
                                                Nome da Rede*
                                            </label>
                                            <input type="text" name="nome_rede" id="nome-rede" class="form-control"
                                                placeholder="Nome da Rede..." title="Nome da Rede*" />
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="quantidade-pontuacoes-usuarios-dia">
                                                Máx. Abast. Gotas Diárias p/ Usuário*
                                            </label>
                                            <input type="number"
                                                placeholder="Máx. Abast. Gotas Diárias p/ Usuário..."
                                                class="form-control" name="quantidade_pontuacoes_usuarios_dia"
                                                name="quantidade-pontuacoes-usuarios-dia"
                                                title="Máximo Abastecimento Gotas Diárias para Usuário" value="3" />
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="quantidade-consumo-usuarios-dia">
                                                Máx. Compras Diárias p/ Usuário*
                                            </label>
                                            <input type="number" placeholder="Máximo de Compras Diárias p/ Usuário*"
                                                title="Máximo de Compras Diárias para Usuário" class="form-control"
                                                name="quantidade_consumo_usuarios_dia"
                                                id="quantidade-consumo-usuarios-dia" value="10" />
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="qte-mesmo-brinde-resgate-dia">
                                                Máx. Resgates Mesmo Brinde / Dia*
                                            </label>
                                            <input type="number" placeholder="Máximo Resgates Mesmo Brinde / Dia*"
                                                title="Máximo Resgates Mesmo Brinde por Dia para mesmo Usuário"
                                                class="form-control" name="qte_mesmo_brinde_resgate_dia"
                                                id="qte-mesmo-brinde-resgate-dia" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-4">
                                            <label for="tempo-expiracao-gotas-usuarios">
                                                Tempo Expiracao de Pontos dos Usuários (meses)*
                                            </label>
                                            <input type="number" min="1" max="99999" class="form-control"
                                                name="tempo_expiracao_gotas_usuarios"
                                                id="tempo-expiracao-gotas-usuarios"
                                                placeholder="Tempo Expiracao de Pontos dos Usuários..."
                                                title="Tempo Expiracao de Pontos dos Usuários (em meses)" value="6" />
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="custo-referencia-gotas">
                                                Custo Referência Gotas (R$)*
                                            </label>
                                            <input type="text" name="custo_referencia_gotas" id="custo-referencia-gotas"
                                                placeholder="Custo Referência Gotas (R$)..."
                                                title="Custo Referência Gotas (R$)"
                                                class="form-control" />
                                        </div>

                                        <div class="col-lg-4">
                                            <label for="media-assiduidade-clientes">
                                                Média Assid. Clientes (Mês)*
                                            </label>
                                            <input type="number" name="media_assiduidade_clientes"
                                                title="Média Assiduidade Clientes (Mês)"
                                                id="media-assiduidade-clientes" class="form-control"
                                                placeholder="Media de Assiduidade Clientes (Mês)" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-6">
                                            <label for="qte-gotas-minima-bonificacao">
                                                Qte. Litros Mínima para Atingir Bonificação*
                                            </label>
                                            <input type="number" name="qte_gotas_minima_bonificacao"
                                                id="qte-gotas-minima-bonificacao"
                                                placeholder="Qte. Litros Mínima para Atingir Bonificação"
                                                title="Qte. Litros Mínima para Atingir Bonificação"
                                                value="" class="form-control" />
                                        </div>

                                        <div class="col-lg-6">
                                            <label for="qte-gotas-bonificacao">
                                                Qte. Bonificação na Importação da SEFAZ*
                                            </label>
                                            <input type="number" name="qte_gotas_bonificacao"
                                                id="qte-gotas-bonificacao" class="form-control" value=""
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
                                            <label for="nome-img">Logotipo da Rede* (Ícone na lista de Estabelecimentos da API)</label>
                                            <input type="file" name="nome_img" id="nome-img" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="img-crop-container">
                                            <div class="col-lg-12">
                                                <h5 style="font-weight: bold;">
                                                    Imagem da Rede para Exibição:
                                                </h5>
                                                <img src="" id="img-crop" class="img-crop-logo" name="img_crop" title="Ícone na lista de Estabelecimentos da API" />
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div>
                                            <div class="col-lg-12">
                                                <h5 style="font-weight: bold;">
                                                    Preview da Imagem:
                                                </h5>
                                                <div id="img-crop-preview" class="img-crop-logo-preview" name="teste"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="hidden">
                                        <div class="row">
                                            <div>
                                                <div class="col-lg-12">
                                                    <input type="text" class="img-upload" name="img_upload"
                                                        id="img-upload" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-height" name="crop_height"
                                                        id="crop-height" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-width" name="crop_width"
                                                        id="crop-width" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-x1" name="crop_x1" id="crop-x1" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-x2" name="crop_x2" id="crop-x2" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-y1" name="crop_y1" id="crop-y1" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="text" class="crop-y2" name="crop_y2" id="crop-y2" />
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
                                            <input type="checkbox" name="ativado" id="ativado" value="1"
                                                checked="true" />
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
                            <button type="submit" id="btn-save" class="btn btn-primary">
                                <em class="fas fa-save"></em> Salvar
                            </button>

                            <!-- <div class="btn btn-primary" tooltip="Salvar" id="btn-save">
                                <i class="fas fa-save"></i> Salvar
                            </div> -->
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
