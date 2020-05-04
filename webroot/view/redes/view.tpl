<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-4">
        <h2>Informações da Rede</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#/">Início</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#/redes/index">Redes</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Informações da Rede</strong>
            </li>
        </ol>
    </div>
</div>

<div class="content">
    <div class="row redes-view-form">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Nome da Rede: <span id="nome-rede"></span></h5>
                </div>
                <div class="ibox-content">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#establishments" data-toggle="tab">Estabelecimentos</a>
                        </li>
                        <li>
                            <a href="#general" data-toggle="tab">Dados Gerais</a>
                        </li>
                        <li>
                            <a href="#network-image" data-toggle="tab">Imagem</a>
                        </li>
                        <li>
                            <a href="#common-options" data-toggle="tab">Opções</a>
                        </li>
                        <li>
                            <a href="#api-options" data-toggle="tab">Serviços Mobile / API</a>
                        </li>
                    </ul>

                    <div class="tab-content clearfix">
                        <!-- Estabelecimentos da Rede -->
                        <div class="tab-pane active" id="establishments">
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <h5>Estabelecimentos da Rede</h5>
                                </div>

                                <div class="form-group row">
                                    <div class="panel-group">
                                        <div class="panel panel-default">
                                            <div class="panel-heading panel-heading-sm text-center"
                                                data-toggle="collapse" href="#collapse1" data-target="#filtro-clientes">
                                                <div>
                                                    <span class="fa fa-search"></span>
                                                    Exibir / Ocultar Filtros
                                                </div>
                                            </div>
                                            <div id="filtro-clientes" class="panel-collapse collapse in">
                                                <div class="panel-body">
                                                    <form id="form">
                                                        <div class="form-group row">
                                                            <div class="col-lg-4">
                                                                <label for="nome_fantasia">Nome Fantasia:</label>
                                                                <input type="text" name="nome_fantasia"
                                                                    id="nome-fantasia" class="form-control" />
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <label for="razao_social">Razão Social:</label>
                                                                <input type="text" name="razao_social" id="razao-social"
                                                                    class="form-control" />
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <label for="cnpj">CNPJ:</label>
                                                                <input type="text" name="cnpj" id="cnpj"
                                                                    class="form-control" />
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
                                </div>

                                <div class="form-group row">
                                    <table class="table table-striped table-bordered table-hover" id="clientes-table">
                                        <thead></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Dados Gerais -->
                        <div class="tab-pane" id="general">
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <div class="col-lg-3">
                                        <label for="nome_rede">Nome da Rede*</label>
                                        <input type="text" name="nome_rede" id="nome-rede" class="form-control"
                                            placeholder="Nome da Rede..." title="Nome da Rede*" value="""
                                            autofocus required disabled readonly
                                            />
                                        </div>

                                        <div class=" col-lg-3">
                                        <label for="quantidade_pontuacoes_usuarios_dia">Máx. Abast. Gotas Diárias p/
                                            Usuário*</label>
                                        <input type="number" min="1" max="365"
                                            placeholder="Máx. Abast. Gotas Diárias p/ Usuário..." class="form-control"
                                            name="quantidade_pontuacoes_usuarios_dia"
                                            id="quantidade-pontuacoes-usuarios-dia"
                                            title="Máximo Abastecimento Gotas Diárias para Usuário" required disabled
                                            readonly value="" />
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="quantidade_consumo_usuarios_dia">Máximo de Compras Diárias p/
                                            Usuário*</label>
                                        <input type="text" min="1" max="365"
                                            placeholder="Máximo de Compras Diárias p/ Usuário*"
                                            title="Máximo de Compras Diárias para Usuário" class="form-control"
                                            name="quantidade_consumo_usuarios_dia" id="quantidade-consumo-usuarios-dia"
                                            required disabled readonly value="10" />
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="qte_mesmo_brinde_resgate_dia">
                                            Máximo Resgates Mesmo Brinde / Dia*
                                        </label>
                                        <input type="text" min="1" max="10" name="qte_mesmo_brinde_resgate_dia"
                                            id="qte-mesmo-brinde-resgate-dia"
                                            placeholder="Máximo Resgates Mesmo Brinde / Dia*"
                                            title="Máximo Resgates Mesmo Brinde por Dia para mesmo Usuário"
                                            class="form-control" required disabled readonly value="" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label for="tempo_expiracao_gotas_usuarios">Tempo Expiracao de Pontos dos
                                            Usuários (meses)*</label>
                                        <input type="number" min="1" max="99999" name="tempo_expiracao_gotas_usuarios"
                                            id="tempo-expiracao-gotas-usuarios"
                                            placeholder="Tempo Expiracao de Pontos dos Usuários..."
                                            title="Tempo Expiracao de Pontos dos Usuários (em meses)"
                                            required="required" value="" disabled readonly class="form-control" />
                                    </div>
                                    <div class="col-lg-4">
                                        <label for="custo_referencia_gotas">Custo Referência Gotas
                                            (R$)*</label>
                                        <input type="text" name="custo_referencia_gotas" id="custo-referencia-gotas"
                                            placeholder="Custo Referência Gotas (R$)..."
                                            title="Custo Referência Gotas (R$)" required="required" value="" disabled
                                            readonly class="form-control" />
                                    </div>

                                    <div class="col-lg-4">
                                        <label for="media_assiduidade_clientes">Média Assid. Clientes
                                            (Mês)*</label>
                                        <input type="number" min="1" max="30" name="media_assiduidade_clientes"
                                            required="required" title="Média Assiduidade Clientes (Mês)"
                                            id="media-assiduidade-clientes" class="form-control" value="" disabled
                                            readonly placeholder="Media de Assiduidade Clientes (Mês)" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label for="qte_gotas_minima_bonificacao">Qte. Litros Mínima para Atingir
                                            Bonificação</label>
                                        <input type="number" name="qte_gotas_minima_bonificacao"
                                            id="qte-gotas-minima-bonificacao"
                                            placeholder="Qte. Litros Mínima para Atingir Bonificação"
                                            title="Qte. Litros Mínima para Atingir Bonificação" required="required"
                                            value="" disabled readonly class="form-control" />
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="qte_gotas_bonificacao">Qte. Bonificação na Importação
                                            da SEFAZ</label>
                                        <input type="number" name="qte_gotas_bonificacao" required="required"
                                            id="qte-gotas-bonificacao" class="form-control" value="" disabled readonly
                                            placeholder="Qte. Bonificação na Importação da SEFAZ"
                                            title="Qte. Bonificação na Importação da SEFAZ" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Imagem -->
                        <div class="tab-pane" id="network-image">
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <label>Imagem Atual da Rede</label>
                                            <div>
                                                <img src=""" alt=" Imagem da Rede" id="nome-img" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Opções Gerais -->
                        <div class="tab-pane" id="common-options">
                            <h5>Opções Gerais</h5>
                            <div class="form-group row">
                                <input type="hidden" name="checkbox" value="0" />
                                <div class="col-lg-12">
                                    <label title="Informe se a Rede está ativada ou não">
                                        <input type="checkbox" name="ativado" id="ativado" readonly disabled />
                                        Rede Ativada
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Opções de Aplicativo Mobile Personalizado -->
                        <div class="tab-pane" id="api-options">
                            <h5>
                                Opções de Aplicativo Mobile Personalizado
                            </h5>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <label for="app_personalizado"
                                        title="Marque esta opção se a Rede em questão possui aplicativo personalizado nas Lojas da Play (Google) e App (Apple) Store">
                                        <input type="checkbox" id="app-personalizado" name="app_personalizado"
                                            class="app_personalizado" readonly disabled />
                                        Rede com APP Personalizado?
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <input type="hidden" name="msg_distancia_compra_brinde" value="0" />
                                    <label for="msg_distancia_compra_brinde">
                                        <input type="checkbox" id="msg-distancia-compra-brinde"
                                            name="msg_distancia_compra_brinde" class="items_app_personalizado" readonly
                                            disabled />
                                        Exibir Mensagem de Distância ao
                                        Comprar
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <input type="hidden" name="app_personalizado" value="0" />
                                    <label for="app_personalizado">
                                        <input type="checkbox" id="app-personalizado" name="app_personalizado"
                                            class="app_personalizado" readonly disabled />
                                        Rede com APP Personalizado?
                                    </label>
                                </div>
                            </div>
                            <h5>
                                Opções de Integração entre Sistemas de
                                Postos
                            </h5>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="hidden" name="pontuacao_extra_produto_generico" value="0" />
                                    <label for="pontuacao_extra_produto_generico"
                                        title="Adiciona Pontos/Gotas para Produtos que não estão cadastrados no Sistema, ao importar o Cupom Fiscal.">
                                        <input type="checkbox" id="pontuacao_extra_produto_generico"
                                            name="pontuacao_extra_produto_generico" readonly disabled />
                                        Atribuir Pontos Extras para Gotas não Cadastradas?
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/webroot/css/styles/redes/view.css">

<script>
    $(document)
        .ready(function () {
            let dataStorage = JSON.parse(localStorage.getItem("data"));

            redesView.init(dataStorage.id);
        })
        .ajaxStart(callLoaderAnimation)
        .ajaxStop(closeLoaderAnimation)
        .ajaxError(closeLoaderAnimation);
</script>
