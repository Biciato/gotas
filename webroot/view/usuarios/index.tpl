<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-4">
        <h2>Lista de Usuários</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#/">Início</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Usuários</strong>
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
    <div class="row usuarios-index">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Usuarios') ?></h5>
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
                                    <form action="javascript:void(0)" id="filtro_usuarios_form">
                                        <div class="form-group row">
                                            <div class="col-lg-3">
                                                <div class="form-group select">
                                                    <label for="tipo_perfil">
                                                        Tipo de Perfil
                                                    </label>
                                                    <select id="tipo_perfil" name="tipo_perfil" class="form-control col-lg-2">
                                                        <option value="">&lt;Todos&gt;</option>

                                                    </select>
                                                </div>

                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group ">
                                                    <label for="nome">
                                                        Nome
                                                    </label>
                                                    <input id="nome" name="nome" class="form-control" placeholder="Nome...">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group ">
                                                    <label for="email">
                                                        Email
                                                    </label>
                                                    <input id="email" name="email" class="form-control" placeholder="Email...">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group ">
                                                    <label for="cpf">
                                                        CPF
                                                    </label>
                                                    <input id="cpf" name="cpf" class="form-control" placeholder="CPF...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">


                                            <div class="col-lg-6">
                                                <div class="form-group ">
                                                    <label for="redes_id">
                                                        Filtrar por rede
                                                    </label>
                                                    <select id="redes_id" name="redes_id" class="form-control col-lg-2">
                                                        <option value="">&lt;Todos&gt;</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group ">
                                                    <label for="redes_id">
                                                        Filtrar por unidade
                                                    </label>
                                                    <select id="clientes_rede" name="clientes_rede" disabled="disabled" class="form-control col-lg-2">
                                                        <option value="">&lt;Todos&gt;</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-12 text-right">
                                                <button type="submit" class="btn btn-success save-button botao-pesquisar" id="filtrar_usuarios">
                                                    <i class="fa fa-search"></i>
                                                    Pesquisar
                                                </button>
                                            </div>
                                        </div>

                                </div>

                                </form>

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

<link rel="stylesheet" href="/webroot/css/styles/usuarios/index.css">
<script>
    $(document).ready(function () {
        usuariosIndex.init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
</script>
