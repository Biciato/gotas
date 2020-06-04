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
            <a href="#/usuarios/add" class="btn btn-primary" tooltip="Salvar" id="redes-new-btn-show"> <i class="fas fa-plus"></i> Novo</a>
        </div>
    </div>
</div>

<div class="content">
    <div class="row usuarios-index">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <span class="fa fa-search"></span>
                                    Exibir / Ocultar Filtros
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="ibox-content">
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
                                                <option value="0">0</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
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
                                            <label for="redes_filtro">
                                                Filtrar por rede
                                            </label>
                                            <select id="redes_filtro" name="redes_filtro" class="form-control col-lg-2">
                                                <option value="0">&lt;Todos&gt;</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group ">
                                            <label for="unidades_filtro">
                                                Filtrar por unidade
                                            </label>
                                            <select id="unidades_filtro" name="unidades_filtro" disabled="disabled" class="form-control col-lg-2">
                                                <option value="">&lt;Todos&gt;</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 text-right">
                                        <button type="text" class="btn btn-info" id="filtrar_usuarios">
                                            <i class="fa fa-search"></i>
                                            Pesquisar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-title">
                    Usuários
                </div>
                <div class="ibox-content">
                    <table class="table table-striped table-bordered table-hover" id="data-table">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="modal_info" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="modal" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Detalhes do Usuário</h4>
            </div>
            <div class="modal-body">
                <div id="detalhe-tipo_perfil">
                    <label>Tipo de Perfil: </label><span style="margin-left: 0.5em"></span>
                </div>
                <div id="detalhe-nome">
                    <label>Nome: </label><span style="margin-left: 0.5em"></span>
                </div>
                <div id="detalhe-email">
                    <label>Email: </label><span style="margin-left: 0.5em"></span>
                </div>
                <div id="detalhe-cpf">
                    <label>CPF: </label><span style="margin-left: 0.5em"></span>
                </div>

                <div id="detalhe-data_nascimento">
                    <label>Data de nascimento: </label><span style="margin-left: 0.5em"></span>
                </div>
                <div id="detalhe-sexo">
                    <label>Sexo: </label><span style="margin-left: 0.5em"></span>
                </div>
                <div id="detalhe-telefone">
                    <label>Telefone: </label><span style="margin-left: 0.5em"></span>
                </div>
                <div id="detalhe-necessidades_especiais">
                    <label>Necessidades especiais: </label><span style="margin-left: 0.5em"></span>
                </div>

                <div id="detalhe-data_criacao">
                    <label>Data de criação: </label><span style="margin-left: 0.5em"></span>
                </div>
                <div id="detalhe-ultima_atualizacao">
                    <label>Última atualização: </label><span style="margin-left: 0.5em"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white close-modal" data-dismiss="modal" >Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="modal_confirmar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="modal" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Aviso</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="confirm_remover" />
                <p class="text-center">Tem certeza que deseja remover este usuário ?</p>
            </div>
            <div class="modal-footer">
                <div class="preloader" style="display: none; width: 100%; height: 2.5em; background: url(/img/loading_login.gif); background-size: contain; background-repeat: no-repeat; background-position: center center;"></div>
                <button class="btn btn-default close-modal" data-dismiss="modal" >Cancelar</button>
                <button class="btn btn-danger" id="confirm_remover">Confirmar</button>
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
