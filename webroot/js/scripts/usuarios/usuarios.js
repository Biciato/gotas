var usuarios = {
    init: function () {
        var self = this;
        self.initDT();
        self.carregarOpcoes();
        // $(document).on("click", "#filtrar_usuarios", self.filtrarUsuarios);
        $(document).on("change", "#redes_id", self.buscarUnidades);
        $(document).on('click', '.visualizar-detalhes-usuario', self.visualizarUsuario);
        return this;
    },
    initDT: function () {
            initPipelinedDT(
                "#usuarios-table",
                [
                    { className: "text-center" },
                    { className: "text-center" },
                    { className: "text-center" },
                    { className: "text-center" },
                    { className: "text-center", orderable: false },
                ],
                "/api/usuarios/carregar-usuarios",
                undefined,
                function (d) {
                    var filtros = $("#filtro_usuarios_form").serialize();
                    d.filtros = filtros;
                    return d;
                },
                [5, 15, 20, 100],
                undefined,
                function (row) {
                    var column_key = 4;
                    var column = row[column_key];
                    //Botão de visualizar
                    row[column_key] =
                        '<a href="javascript:void(0)" data-id="' + column["usuario_id"] + '" class="btn btn-default botao-navegacao-tabela visualizar-detalhes-usuario" title="Ver detalhes"><i class="fa fa-info-circle"></i> </a> ' +
                        //Botão de editar operador
                        '<a href="/usuarios/editar-operador/' +
                        column["usuario_id"] +
                        '" class="btn btn-primary botao-navegacao-tabela" title="Editar"><i class="fa fa-edit"></i></a>';
                    if (column["botoes_extras"]) {
                        if (column["conta_ativa"]) {
                            row[column_key] +=
                                ' <a href="#" class="btn  btn-danger btn-confirm" title="Desativar" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente desabilitar o acesso do usuário ' +
                                row[0] +
                                '?" data-action="' +
                                column["url_desativar"] +
                                '"><i class="fa fa-power-off"></i> </a>';
                        } else {
                            row[column_key] +=
                                ' <a href="#" class="btn  btn-primary btn-confirm" title="Ativar" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente habilitar o acesso do usuário ' +
                                row[0] +
                                '?" data-action="' +
                                column["url_ativar"] +
                                '"><i class="fa fa-power-off"></i></a>';
                        }
                    }
                    row[column_key] +=
                        ' <a href="#" class="btn  btn-danger btn-confirm" title="Remover" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente apagar o registro  ' +
                        row[0] +
                        '?" data-action="' +
                        column["url_deletar"] +
                        '"><i class="fa fa-trash"></i></a>';

                    return row;
                }
            );
    },
    filtrarUsuarios: function (e) {
        e.preventDefault();
        if (typeof window["#usuarios-table"] !== "undefined") {
            window["#usuarios-table"].clearPipeline().draw();
        }
    },
    buscarUnidades: function (e) {
        var val = $(this).val();
        $("#clientes_rede").val("");
        if (val == "") {
            $("#clientes_rede").attr("disabled", "disabled");
        } else {
            $.ajax({
                url: "/api/clientes/get_clientes_list",
                data: { redes_id: val },
                method: "GET",
                dataType: "JSON",
                async: true,
                success: function (resposta) {
                    if (resposta.mensagem.status == true) {
                        var markup = '<option value="">&lt;Todos&gt;</option>';
                        if (resposta.data.clientes.length > 0) {
                            $.each(resposta.data.clientes, function (i, item) {
                                markup +=
                                    '<option value="' +
                                    item.id +
                                    '">' +
                                    item.nome_fantasia_municipio_estado +
                                    "</option>";
                            });
                            $("#clientes_rede").removeAttr("disabled");
                            $("#clientes_rede").html(markup);
                        } else {
                            $("#clientes_rede").attr("disabled", "disabled");
                        }
                    } else {
                        toastr.resposta(resposta.mensagem.mensagem);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error(xhr.responseJSON.mensagem.message);
                    $("#clientes_rede").val("");
                    $("#clientes_rede").attr("disabled", "disabled");
                },
            });
        }
    },
    carregarOpcoes: function () {
        var opcoes_tipos_perfil =
            '<option value="" selected="selected">&lt;Todos&gt </option>';
        var opcoes_redes =
            '<option value="" selected="selected">&lt;Todos&gt </option>';

        $.ajax({
            url: "/api/usuarios/get_profile_types",
            data: {},
            method: "GET",
            success: function (resposta) {
                $.each(resposta.data.filter, function (i, item) {
                    opcoes_tipos_perfil +=
                        '<option value="' + i + '">' + item + "</option>";
                });
                $("#tipo_perfil").html(opcoes_tipos_perfil);
            },
        });
        $.ajax({
            url: "/usuarios/carregar_redes",
            data: {},
            method: "GET",
            success: function (resposta) {
                $.each(resposta.source, function (i, item) {
                    opcoes_redes +=
                        '<option value="' + i + '">' + item + "</option>";
                });
                $("#redes_id").html(opcoes_redes);
            },
        });
    },
    visualizarUsuario: function()
      {
        var id = $(this).data('id');
        $.ajax(
          {
            url: '/api/usuarios/visualizar-usuario',
            data: {id: id},
            dataType: 'JSON',
            method: 'GET',
            success: function(resposta)
              {
                var markup = '<table class="table table-condensed">' +
                '<tbody>' +
                    '<tr>' +
                    '<td><b>Nome</b></td>' +
                    '<td>' + resposta.source.nome + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><b>Email</b></td>' +
                    '<td>' + resposta.source.email + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><b>Telefone</b></td>' +
                    '<td>' + resposta.source.telefone + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><b>Tipo de perfil</b></td>' +
                    '<td>' + resposta.source.tipo_perfil + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><b>Data nascimento</b></td>' +
                    '<td>' + resposta.source.data_nascimento + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><b>Sexo</b></td>' +
                    '<td>' + resposta.source.sexo + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><b>Port. necessidades especiais</b></td>' +
                    '<td>' + resposta.source.necessidades_especiais + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><b>Data criação</b></td>' +
                    '<td>' + resposta.source.data_criacao + '</td>' +
                    '</tr>' +
                    '</tr>' +
                    '<td><b>Última alteração</b></td>' +
                    '<td>' + resposta.source.ultima_atualizacao + '</td>' +
                    '</tr>' +
                    '</tbody>' +
                '</table>';
                $("#modal-visualize-body").html(markup);
                $("#modal-visualize").modal('show');
              }
          }
        );
      }
};
$(document).ready(function () {
    usuarios.init();
});
