<div id="correction-user-points">
    <div class="form-group row border-bottom white-bg page-heading" id="correction-user-points-breadcrumb">
        <div class="col-lg-8">
            <h2>Correção de Pontos de Usuário</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#/">Início</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Correção de Pontos de Usuário</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-4">
            <div class="title-action">
                <div class="btn btn-primary" tooltip="Reiniciar" id="btn-refresh">
                    <em class="fas fa-refresh"></em>
                    Reiniciar
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">Correção de Pontos de Usuário</div>
                    <div class="ibox-content">
                        <form id="correction-user-points-search-form">
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label for="redes">Rede:</label>
                                    <select name="redes_list" id="redes-list" class="form-control"
                                        placeholder="Redes..." title="Redes"></select>
                                </div>
                                <div class="col-lg-2">
                                    <label for="usuario-options-search">Pesquisar Por:</label>
                                    <select id="usuario-options-search" name="usuario_options_search"
                                        class="form-control" autofocus>
                                        <option value="nome">Nome</option>
                                        <option value="cpf">CPF</option>
                                        <option value="telefone" selected>Telefone</option>
                                        <option value="placa">Placa</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label for="usuario-cpf">Dados de Pesquisa do Usuário:</label>
                                    <input type="text" name="usuario-parameter-search" id="usuario-parameter-search"
                                        class="form-control" placeholder="" title="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <div class="pull-right">
                                        <div class="btn btn-primary" title="Pesquisar"
                                            id="usuario-parameter-button-search"><i class="fas fa-search-plus"></i>
                                            Pesquisar </div>
                                    </div>
                                </div>
                            </div>

                            <div id="veiculo-region">
                                <h4>Dados do Veículo:</h4>

                                <div class="form-group row">
                                    <div class="col-lg-3">
                                        <label for="veiculo-placa">Placa:</label>
                                        <input type="text" name="veiculo-placa" id="veiculo-placa" class="form-control"
                                            disabled placeholder="Placa do Veículo..." title="Placa do Veículo">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="veiculo-modelo">Modelo:</label>
                                        <input type="text" name="veiculo-modelo" id="veiculo-modelo"
                                            class="form-control" disabled placeholder="Modelo do Veículo..."
                                            title="Modelo do Veículo">

                                    </div>
                                    <div class="col-lg-3">
                                        <label for="veiculo-fabricante">Fabricante:</label>
                                        <input type="text" name="veiculo-fabricante" id="veiculo-fabricante"
                                            class="form-control" disabled placeholder="Fabricante do Veículo..."
                                            title="Fabricante do Veículo">

                                    </div>
                                    <div class="col-lg-3">
                                        <label for="veiculo-ano">Ano:</label>
                                        <input type="text" name="veiculo-ano" id="veiculo-ano" class="form-control"
                                            disabled placeholder="Ano do Veículo..." title="Ano do Veículo">

                                    </div>
                                </div>
                            </div>

                            <!-- Div para lista de seleção de usuários -->
                            <div id="usuarios-region">
                                <h4>Usuários Encontrados:</h4>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <table class="table table-striped table-bordered table-hover table-responsive"
                                            id="data-table">
                                            <thead>
                                                <!-- <tr>
                                                    <th></th>
                                                    <th>Nome</th>
                                                    <th>Telefone</th>
                                                    <th>Data Nasc.</th>
                                                    <th>Ações</th>
                                                </tr> -->
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label for="usuario-nome">Nome:</label>
                                    <input type="text" name="usuario-nome" id="usuario-nome" class="form-control"
                                        placeholder="Nome do Usuário..." title="Nome do Usuário" disabled>
                                </div>
                                <div class="col-lg-4">
                                    <label for="usuario-saldo">Saldo de Gotas:</label>
                                    <input type="text" name="usuario-saldo" id="usuario-saldo" class="form-control"
                                        placeholder="Saldo de Pontos do Usuário.." title="Saldo de Pontos do Usuário"
                                        readonly disabled>
                                </div>
                                <div class="col-lg-4">
                                    <label for="quantidade_multiplicador">Quantidade de Pontos à ser
                                        Ajustado</label>
                                    <input type="text" class="form-control" name="quantidade_multiplicador"
                                        id="quantidade-multiplicador">
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-primary" id="botao-gravar-gotas">
                                    <em class="fas fa-save"></em> Gravar
                                </button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/webroot/css/styles/pontuacoes_comprovantes/correction-user-points.css">

<script>
    $(function () {
        importSefazProducts.init();
    })
        .ajaxStart(callLoaderAnimation)
        .ajaxStop(closeLoaderAnimation)
        .ajaxError(closeLoaderAnimation);
</script>
