
<div class="form-group">

<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
            <!-- <h4 class="panel-title"> -->
            <div>
                <span class="fa fa-search"></span>
                Exibir / Ocultar Filtros
            </div>

            <!-- </h4> -->
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
                        <button type="submit" class="btn btn-primary save-button botao-pesquisar" id="filtrar_usuarios">
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

</div>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Lista de usuários</h5>
            </div>
            <div class="ibox-content">

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="usuarios-table">
                        <thead>
                            <tr>
                                <th>Tipo de perfil</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>E-mail</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>
    <div id="modal-delete-with-message" class="modal fade modal-delete-with-message" role="dialog">
        <div class="modal-dialog modal-lg">

            <div class="modal-content">
                <div class="modal-header ">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title success-message"><span class="modal-title-content">Atenção</span></h4>
                </div>
                <div class="modal-body">
                    <p class="modal-body-content"></p>
                </div>
                <div class="modal-footer">
                    <!-- Form para alterar a url de destino -->
                    <form method="post" name="post-remove-binding"></form>
                    <div class="btn btn-primary" id="submit_button">Confirmar</div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>

        </div>
    </div>
<script type="text/javascript">
  var s = document.createElement("script");
  s.type = "text/javascript";
  s.src = "/app_gotas/js/scripts/usuarios/usuarios.js";
  var body = document.getElementById('main_body');
  body.prepend(s);
</script>