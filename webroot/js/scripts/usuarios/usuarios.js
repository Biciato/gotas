var usuarios = {
    init: function () {
        var self = this;
        self.initDT();
        self.carregarOpcoes();
        $(document).on("click", "#filtrar_usuarios", self.filtrarUsuarios);
        $(document).on("change", "#redes_id", self.buscarUnidades);
        return this;
    },
    initDT: function () {
        if (typeof window["#usuarios-table"] === "undefined") {
            initPipelinedDT(
                "#usuarios-table",
                [
                    { className: "text-center" },
                    { className: "text-center" },
                    { className: "text-center" },
                    { className: "text-center" },
                    { className: "text-center", orderable: false },
                ],
                "/app_gotas/usuarios/carregar-usuarios",
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
                        '<a href="/app_gotas/usuarios/view/' +
                        column["usuario_id"] +
                        '" class="btn btn-xs btn-default botao-navegacao-tabela" title="Ver detalhes"><i class="fa fa-info-circle"></i> </a> ' +
                        //Botão de editar operador
                        '<a href="/app_gotas/usuarios/editar-operador/' +
                        column["usuario_id"] +
                        '" class="btn btn-xs btn-primary botao-navegacao-tabela" title="Editar"><i class="fa fa-edit"></i></a>';
                    if (column["botoes_extras"]) {
                        if (column["conta_ativa"]) {
                            row[column_key] +=
                                ' <a href="#" class="btn btn-xs  btn-danger btn-confirm" title="Desativar" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente desabilitar o acesso do usuário ' +
                                row[0] +
                                '?" data-action="' +
                                column["url_desativar"] +
                                '"><i class="fa fa-power-off"></i> </a>';
                        } else {
                            row[column_key] +=
                                ' <a href="#" class="btn btn-xs  btn-primary btn-confirm" title="Ativar" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente habilitar o acesso do usuário ' +
                                row[0] +
                                '?" data-action="' +
                                column["url_ativar"] +
                                '"><i class="fa fa-power-off"></i></a>';
                        }
                    }
                    row[column_key] +=
                        ' <a href="#" class="btn btn-xs  btn-danger btn-confirm" title="Remover" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente apagar o registro  ' +
                        row[0] +
                        '?" data-action="' +
                        column["url_deletar"] +
                        '"><i class="fa fa-trash"></i></a>';

                    return row;
                }
            );
        }
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
                url: "/app_gotas/api/clientes/get_clientes_list",
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
            url: "/app_gotas/usuarios/carregar_tipos_perfil",
            data: {},
            method: "GET",
            success: function (resposta) {
                $.each(resposta.source, function (i, item) {
                    opcoes_tipos_perfil +=
                        '<option value="' + i + '">' + item + "</option>";
                });
                $("#tipo_perfil").html(opcoes_tipos_perfil);
            },
        });
        $.ajax({
            url: "/app_gotas/usuarios/carregar_redes",
            data: {},
            method: "GET",
            success: function (resposta) {
                $.each(resposta.source, function (i, item) {
                    opcoes_tipos_perfil +=
                        '<option value="' + i + '">' + item + "</option>";
                });
                $("#redes_id").html(opcoes_tipos_perfil);
            },
        });
    },
};
$(document).ready(function () {
    usuarios.init();
});
