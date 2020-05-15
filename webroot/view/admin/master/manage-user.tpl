<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Controlar Usuário</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#/">Início</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Controlar Usuário</strong>
            </li>
        </ol>
    </div>
</div>

<div class="content">
    <div class="row manage-user-form">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">Selecione Usuário para Controlar</div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <div class="panel-group">
                            <div class="panel panel-default">
                                <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse"
                                    href="#collapse1" data-target="#filtro-clientes">
                                    <div>
                                        <span class="fa fa-search"></span>
                                        Exibir / Ocultar Filtros
                                    </div>
                                </div>
                                <div id="filtro-clientes" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <form id="form">
                                            <div class="form-group row">
                                                <div class="col-lg-3">
                                                    <label for="nome_fantasia">Nome Fantasia:</label>
                                                    <input type="text" name="nome_fantasia" id="nome-fantasia"
                                                        class="form-control" />
                                                </div>
                                                <div class="col-lg-3">
                                                    <label for="razao_social">Razão Social:</label>
                                                    <input type="text" name="razao_social" id="razao-social"
                                                        class="form-control" />
                                                </div>
                                                <div class="col-lg-3">
                                                    <label for="cnpj">CNPJ:</label>
                                                    <input type="text" name="cnpj" id="cnpj" class="form-control" />
                                                </div>
                                                <div class="col-lg-3">
                                                    <label for="ativado">Ativado:</label>
                                                    <select name="ativado" id="ativado" class="form-control">
                                                        <option value="">Todos</option>
                                                        <option value="1" selected>Sim</option>
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
                    </div>

                    <div class="form-group row">
                        <table class="table table-striped table-bordered table-hover" id="clientes-table">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/webroot/css/styles/admin/master/manage-user.css">

<script>
    $(document)
        .ready(function () {
            manageUsers.init();
        })
        .ajaxStart(callLoaderAnimation)
        .ajaxStop(closeLoaderAnimation)
        .ajaxError(closeLoaderAnimation);
</script>
