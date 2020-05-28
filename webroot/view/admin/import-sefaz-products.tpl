<div id="import-sefaz-products">
    <div class="form-group row border-bottom white-bg page-heading" id="import-sefaz-products-breadcrumb">
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
        <div class="col-lg-4">
            <div class="title-action">
                <div class="btn btn-primary" tooltip="Reiniciar" id="btn-refresh">
                    <em class="fas fa-refresh"></em>
                    Reiniciar
                </div>
            </div>
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
                                    <input type="text" id="clientes-nome" name="clientes_nome" class="form-control"
                                        readonly disabled>
                                </div>
                            </div>

                            <h4>Produtos Encontrados</h4>
                            <table class="table table-striped table-bordered table-hover table-responsive"
                                id="data-table">
                                <thead>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="form-group row ">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary disabled" id="btn-save">
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
</div>

<script>
    $(function () {
        importSefazProducts.init();
    })
        .ajaxStart(callLoaderAnimation)
        .ajaxStop(closeLoaderAnimation)
        .ajaxError(closeLoaderAnimation);
</script>