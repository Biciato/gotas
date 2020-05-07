<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-4">
        <h2>Lista de Redes</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#/">Início</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Redes</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-8">
        <div class="title-action">
            <a href="#/redes/add" class="btn btn-primary" tooltip="Salvar" id="redes-new-btn-show"> <i class="fas fa-plus"></i> Novo</a>
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
                                                <input type="text" name="nome_rede" id="nome-rede" class="form-control" />
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
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/webroot/js/scripts/redes/index.js"></script>

