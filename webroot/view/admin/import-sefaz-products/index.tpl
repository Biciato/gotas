<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Importação de Produtos da SEFAZ</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#/">Início</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Importação de Produtos da SEFAZ</strong>
            </li>
        </ol>
    </div>
</div>

<div class="content import-sefaz-products-index">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">Importação de Produtos da SEFAZ</div>
                <div class="ibox-content">
                    <form id="qrcode-search-form">
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <label for="pesquisa-nome">Informe QR Code para Importação em Massa</label>
                                <input type="text" name="qr_code" id="qr-code" class="form-control"
                                    placeholder="Informe QR Code..." title="Informe QR Code" autofocus>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <div class="col-lg-12 text-right">
                                <button type="button" class="btn btn-primary btn-w-m" id="btn-search">
                                    <span class="fa fa-search"></span>
                                    Pesquisar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="import-sefaz-products-data">

                        <div class="form-group row">

                            <div class="col-lg-6">
                                <label for="redes_nome">Rede Encontrada</label>
                                <input type="text" id="redes-nome" name="redes_nome" class="form-control" readonly
                                    disabled>
                            </div>

                            <div class="col-lg-6">
                                <label for="clientes_nome">Estabelecimento Encontrado</label>
                                <input type="text" id="clientes-nome" name="clientes_nome" class="form-control" readonly
                                    disabled>
                            </div>
                        </div>

                        <h4>Produtos Encontrados</h4>
                        <table class="table table-striped table-bordered table-hover" id="data-table">
                            <thead>
                                <!-- <tr>
                                <th>Nome</th>
                                <th>Quantidade Multiplicador</th>
                                <th>Importar?</th>
                                <th>Ações</th>
                            </tr> -->
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="form-group row ">
                            <div class="col-lg-2 pull-right">
                                <button type="submit" class="btn btn-primary btn-block" id="botao-gravar-gotas">
                                    <span class="fas fa-save"></span>
                                    Gravar
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ajuste de multiplicador -->

<div class="modal" tabindex="-1" role="dialog" id="modal-edit-product">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Produto : <span id='nome-parametro'></span></h5>
            </div>
            <div class="modal-body">
                <p>
                    <label for="multiplicador">Informe o multiplicador para o produto:</label>
                    <input type="text" class="form-control" name="multiplicador-gota" id="multiplicador-gota" />
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirm">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- <link rel="stylesheet" href="/webroot/css/styles/admin/import-sefaz-products.css"> -->
<link rel="stylesheet" href="/webroot/css/styles/gotas/importacao_gotas_sefaz.css">

<script>
    $(function () {
        importSefazProducts.init();
    })
        .ajaxStart(callLoaderAnimation)
        .ajaxStop(closeLoaderAnimation)
        .ajaxError(closeLoaderAnimation);
</script>
