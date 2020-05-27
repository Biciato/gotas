<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Parâmetros da Rede</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#/">Início</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Parâmetros da Rede</strong>
            </li>
        </ol>
    </div>
</div>

<div class="content network-settings">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">Parâmetros da Rede</div>
                <div class="ibox-content">

                    <form name="network_settings_form" id="network-settings-form">
                        <fieldset>
                            <div class="form-group row">
                                <div class="col-lg-2">
                                    <label for="quantidade-pontuacoes-usuarios-dia">
                                        Máximo de Abastecimento de Pontos Diários para Usuário*
                                    </label>
                                </div>
                                <div class="col-lg-10">
                                    <input type="number" min="1" max="365"
                                        placeholder="Máximo de Abastecimento de Pontos Diários para Usuário..."
                                        class="form-control" name="quantidade_pontuacoes_usuarios_dia"
                                        id="quantidade-pontuacoes-usuarios-dia"
                                        title="Máximo de Abastecimento de Pontos Diários para Usuário*" required
                                        value="" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-lg-2">
                                    <label for="quantidade-consumo-usuarios-dia">
                                        Máximo de Compras Diárias para Usuário*
                                    </label>
                                </div>
                                <div class="col-lg-10">
                                    <input type="text" min="1" max="365"
                                        placeholder="Máximo de Compras Diárias para Usuário*"
                                        title="Máximo de Compras Diárias para Usuário" class="form-control"
                                        name="quantidade_consumo_usuarios_dia" id="quantidade-consumo-usuarios-dia"
                                        required value="" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-lg-2">
                                    <label for="tempo-expiracao-gotas-usuarios">
                                        Tempo Expiração
                                        Pontos Usuarios (Mês)*
                                    </label>
                                </div>
                                <div class="col-lg-10">
                                    <input type="text" min="1" max="365"
                                        placeholder="Tempo Expiração Pontos Usuarios (Mês)..."
                                        title="Tempo Expiração Pontos Usuarios (Mês)" class="form-control"
                                        name="tempo_expiracao_gotas_usuarios" id="tempo-expiracao-gotas-usuarios"
                                        value="" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-lg-2">
                                    <label for="custo-referencia-gotas">Custo Referência Gotas
                                        (R$)*</label>
                                </div>
                                <div class="col-lg-10">
                                    <input type="text" name="custo_referencia_gotas" id="custo-referencia-gotas"
                                        placeholder="Custo Referência Gotas (R$)..." title="Custo Referência Gotas (R$)"
                                        required="required" value="" class="form-control" />

                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-lg-2">
                                    <label for="media-assiduidade-clientes">
                                        Média Assiduidade de Clientes (Mês)*
                                    </label>
                                </div>
                                <div class="col-lg-10">
                                    <input type="number" min="1" max="30" name="media_assiduidade_clientes"
                                        required="required" title="Média Assiduidade Clientes (Mês)"
                                        id="media-assiduidade-clientes" class="form-control" value=""
                                        placeholder="Media de Assiduidade Clientes (Mês)" />

                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-lg-2">
                                    <label for="qte-gotas-minima-bonificacao">
                                        Qte. Litros Mínima para Atingir Bonificação
                                    </label>
                                </div>
                                <div class="col-lg-10">
                                    <input type="number" name="qte_gotas_minima_bonificacao" class="form-control"
                                        id="qte-gotas-minima-bonificacao"
                                        placeholder="Qte. Litros Mínima para Atingir Bonificação"
                                        title="Qte. Litros Mínima para Atingir Bonificação" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-lg-2">
                                    <label for="qte-gotas-bonificacao">
                                        Qte. Bonificação na Importação da SEFAZ
                                    </label>
                                </div>
                                <div class="col-lg-10">
                                    <input type="number" name="qte_gotas_bonificacao" id="qte-gotas-bonificacao"
                                        class="form-control" placeholder="Qte. Bonificação na Importação da SEFAZ"
                                        title="Qte. Bonificação na Importação da SEFAZ" />
                                </div>
                            </div>
                            <div class="form-group row ">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary disabled" id="btn-save">
                                        <span class="fas fa-save"></span>
                                        Gravar
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="/webroot/css/styles/admin/network-settings.css">

    <script>
        $(function () {
            networkSettings.init();
        })
            .ajaxStart(callLoaderAnimation)
            .ajaxStop(closeLoaderAnimation)
            .ajaxError(closeLoaderAnimation);
    </script>