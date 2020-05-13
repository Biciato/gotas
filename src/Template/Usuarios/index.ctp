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
    <<<<<<< HEAD <?php
                    echo $this->element('modal_delete_with_message');
                    // $this->append('script');
                    // echo $this->Html->css(sprintf("DataTables/datatables.min.css?version=%s", SYSTEM_VERSION));
                    // $this->end();
                    $this->append('script');
                    // echo $this->Html->script(sprintf("DataTables/datatables.min.js?version=%s", SYSTEM_VERSION));
                    // echo $this->Html->script('layout-update/pipeline_wrapper');
                    ?> <script type="text/javascript">
        var usuarios = {
        init: function() {
        var self = this;
        self.initDT();
        $(document).on('click', "#filtrar_usuarios", self.filtrarUsuarios);
        $(document).on('change', '#redes_id', self.buscarUnidades);
        return this;
        },
        initDT: function() {
        if (typeof window["#usuarios-table"] === 'undefined') {
        initPipelinedDT("#usuarios-table",
        [{
        className: "text-center"
        },
        {
        className: "text-center"
        },
        {
        className: "text-center"
        },
        {
        className: "text-center"
        },
        {
        className: "text-center",
        orderable: false
        }
        ],
        '/api/usuarios/carregar-usuarios',
        undefined,
        function(d) {
        var filtros = $("#filtro_usuarios_form").serialize();
        d.filtros = filtros;
        return d;
        },
        [5, 15, 20, 100],
        undefined,
        function(row) {
        var column_key = 4;
        var column = row[column_key];
        //Botão de visualizar
        row[column_key] = '<a href="/usuarios/view/' + column['usuario_id'] + '" class="btn btn-xs btn-default botao-navegacao-tabela" title="Ver detalhes"><i class="fa fa-info-circle"></i> </a> ' +
        //Botão de editar operador
        '<a href="/usuarios/editar-operador/' + column['usuario_id'] + '" class="btn btn-xs btn-primary botao-navegacao-tabela" title="Editar"><i class="fa fa-edit"></i></a>';
        if (column['botoes_extras']) {
        if (column['conta_ativa']) {
        row[column_key] += ' <a href="#" class="btn btn-xs  btn-danger btn-confirm" title="Desativar" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente desabilitar o acesso do usuário ' + row[0] + '?" data-action="' + column['url_desativar'] + '"><i class="fa fa-power-off"></i> </a>';
        } else {
        row[column_key] += ' <a href="#" class="btn btn-xs  btn-primary btn-confirm" title="Ativar" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente habilitar o acesso do usuário ' + row[0] + '?" data-action="' + column['url_ativar'] + '"><i class="fa fa-power-off"></i></a>';
        }
        }
        row[column_key] += ' <a href="#" class="btn btn-xs  btn-danger btn-confirm" title="Remover" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente apagar o registro  ' + row[0] + '?" data-action="' + column['url_deletar'] + '"><i class="fa fa-trash"></i></a>';

        return row;
        });
        }
        },
        filtrarUsuarios: function(e) {
        e.preventDefault();
        if (typeof window['#usuarios-table'] !== 'undefined') {
        window['#usuarios-table'].clearPipeline().draw();
        }
        },
        buscarUnidades: function(e) {
        var val = $(this).val();
        $("#clientes_rede").val("");
        if (val == "") {
        $("#clientes_rede").attr('disabled', 'disabled');
        } else {
        $.ajax({
        url: '/api/clientes/get_clientes_list',
        data: {
        redes_id: val
        },
        method: 'GET',
        dataType: 'JSON',
        async: true,
        success: function(resposta) {
        if (resposta.mensagem.status == true) {
        var markup = "<option value=\"\">&lt;Todos&gt;</option>";
        if (resposta.data.clientes.length > 0) {
        $.each(resposta.data.clientes, function(i, item) {
        markup += "<option value=\"" + item.id + "\">" + item.nome_fantasia_municipio_estado + "</option>";
        });
        $("#clientes_rede").removeAttr('disabled');
        $("#clientes_rede").html(markup);
        } else {
        $("#clientes_rede").attr('disabled', 'disabled');
        }
        } else {
        toastr.resposta(resposta.mensagem.mensagem);
        }
        },
        error: function(xhr, status, error) {
        toastr.error(xhr.responseJSON.mensagem.message);
        $("#clientes_rede").val("");
        $("#clientes_rede").attr("disabled", "disabled");
        }
        });
        }
        }
        };
        $(document).ready(function() {
        usuarios.init();
        })
        </script>
        <?php
        $this->end();
        ?>
        =======
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
        >>>>>>> 181_novo_layout
