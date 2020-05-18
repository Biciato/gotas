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

<div class="content manage-user">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">Selecione Usuário para Controlar</div>
                <div class="ibox-content">
                    <div class="form-group">

                        <div class="panel-group">
                            <div class="panel panel-default">
                                <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse"
                                    href="#collapse1" data-target="#filter-coupons">
                                    <!-- <h4 class="panel-title"> -->
                                    <div>
                                        <span class="fa fa-search"></span>
                                        Exibir / Ocultar Filtros
                                    </div>

                                    <!-- </h4> -->
                                </div>
                                <div id="filter-coupons" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <form action="javascript:void(0)" id="manage-user-form">
                                            <div class="form-group row">
                                                <div class="col-lg-3">
                                                    <div class="form-group select">
                                                        <label for="tipo-perfis-select-list">
                                                            Tipo de Perfil
                                                        </label>
                                                        <select id="tipo-perfis-select-list" name="tipo_perfil"
                                                            class="form-control col-lg-2 select2-list-generic">
                                                            <option value="">&lt;Todos&gt;</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group select"><label for="redes-select-list">
                                                            Rede
                                                        </label>
                                                        <select name="redes_id" id="redes-select-list"
                                                            class="form-control select2-list-generic">
                                                            <option value=""></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group ">
                                                        <label for="nome">
                                                            Nome
                                                        </label>
                                                        <input id="nome" name="nome" class="form-control"
                                                            placeholder="Nome...">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group ">
                                                        <label for="email">
                                                            Email
                                                        </label>
                                                        <input id="email" name="email" class="form-control"
                                                            placeholder="Email...">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-lg-12 text-right">
                                                    <button type="submit" class="btn btn-primary" id="btn-search">
                                                        <em class="fa fa-search"></em>
                                                        Pesquisar
                                                    </button>
                                                </div>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <table class="table table-striped table-bordered table-hover" id="data-table">
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
        $(function () {
            manageUser.init();
        })
            .ajaxStart(callLoaderAnimation)
            .ajaxStop(closeLoaderAnimation)
            .ajaxError(closeLoaderAnimation);
    </script>
