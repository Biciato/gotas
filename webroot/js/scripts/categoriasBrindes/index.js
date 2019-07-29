"use strict";
$(function() {
    // #region Fields

    var categoriasBrindes = [];
    var categoriaBrindeSelected = {};

    // #endregion

    // #region Functions

    var filterData = function() {
        var nome = $("#pesquisa-nome").val();

        loadData(nome);
    };

    $("#pesquisa-nome").on("keyup", function(e) {
        if (e.which == 13) {
            filterData();
        }
    });
    $(".botao-pesquisar").on("click", filterData);

    var generateEditButton = function(value, target) {
        var template =
            "<button class='btn btn-primary btn-xs form-btn-edit' id='form-btn-edit' value=" +
            value +
            "> <i class=' fa fa-edit'></i></button>";

        return template;
    };

    var generateEditButtonFunctions = function(classString) {
        var click = function(e) {
            var id = $(this).val();

            var categoriaBrinde = $.grep(categoriasBrindes, function(cb) {
                return cb.id == id;
            });

            if (categoriaBrinde.length > 0) {
                categoriaBrindeSelected = categoriaBrinde[0];
            }

            editar(categoriaBrindeSelected.id);
        };

        $("." + classString).on("click", click);
    };

    var generateEnableButton = function(value, target) {
        var template =
            "<button class='btn btn-primary btn-xs form-btn-enable' id='form-btn-enable' value=" +
            value +
            "> <i class=' fa fa-power-off'></i></button>";
        return template;
    };

    var generateEnableButtonFunctions = function(classString) {
        var click = function(e) {
            var id = $(this).val();

            categoriaBrinde = $.grep(categoriasBrindes, function(cb) {
                return cb.id == id;
            });

            if (categoriaBrinde.length > 0) {
                categoriaBrindeSelected = categoriaBrinde[0];
            }

            abreModalHabilitar(categoriaBrindeSelected.id);
        };

        $("." + classString).on("click", click);
    };

    var generateDisableButton = function(value, target) {
        var template =
            "<button class='btn btn-danger btn-xs form-btn-disable' id='form-btn-disable' value=" +
            value +
            "> <i class=' fa fa-power-off'></i></button>";

        return template;
    };

    var generateDisableButtonFunctions = function(classString) {
        var click = function(e) {
            var id = $(this).val();

            var categoriaBrinde = $.grep(categoriasBrindes, function(cb) {
                return cb.id == id;
            });

            if (categoriaBrinde.length > 0) {
                categoriaBrindeSelected = categoriaBrinde[0];
            }

            abreModalDesabilitar(categoriaBrindeSelected.id);
        };

        $("." + classString).on("click", click);
    };

    var generateDeleteButton = function(value, target) {
        var template =
            "<button class='btn btn-danger btn-xs form-btn-delete' id='form-btn-delete' value=" +
            value +
            "> <i class=' fa fa-trash'></i></button>";

        return template;
    };

    var generateDeleteButtonFunctions = function(classString) {
        var click = function(e) {
            var id = $(this).val();

            var categoriaBrinde = $.grep(categoriasBrindes, function(cb) {
                return cb.id == id;
            });

            if (categoriaBrinde.length > 0) {
                categoriaBrindeSelected = categoriaBrinde[0];
            }

            abreModalRemover(categoriaBrindeSelected.id);
        };

        $("." + classString).on("click", click);
    };

    /**
     * Obtem os dados de categorias de brindes e alimenta a tabela
     *
     */
    var loadData = function(nome) {
        var dataRequest = { nome: nome };

        callLoaderAnimation();


        // if ($(".paginationjs").length > 0) {
        //     $("#tabela-dados").pagination("destroy");
        // }
        $("#tabela-dados").pagination({
            pageSize: 1,
            showPrevious: true,
            showNext: true,
            dataSource: function(done) {
                $.ajax({
                    type: "GET",
                    url: "/api/categorias_brindes/get_categorias_brindes",
                    data: dataRequest,
                    success: function(success) {},
                    error: function(err) {
                        closeLoaderAnimation();
                        callModalError(
                            err.mensagem.message,
                            err.mensagem.errors
                        );
                    },
                    complete: function(success) {
                        closeLoaderAnimation();

                        var dataReceived =
                            success.responseJSON.categorias_brindes;

                        var rows = [];
                        dataReceived.forEach(element => {
                            var obj = {};
                            obj.id = element.id;
                            obj.nome = element.nome;
                            obj.habilitado = element.habilitado;
                            obj.dataCriado = element.data;
                            rows.push(obj);
                        });

                        categoriasBrindes = dataReceived;

                        done(categoriasBrindes);
                    }
                });
            },
            callback: function(data, pagination) {
                var table = $("#tabela-dados tbody");
                table.empty();

                var rows = [];
                data.forEach(element => {
                    var habilitado = element.habilitado ? "Sim" : "Não";
                    var botaoEditar = generateEditButton(element.id);
                    var botaoRemover = generateDeleteButton(element.id);
                    var botaoTrocaStatus = !element.habilitado
                        ? generateEnableButton(element.id)
                        : generateDisableButton(element.id);
                    var dataCriado = moment(element.data).format(
                        "DD/MM/YYYY HH:mm:ss"
                    );
                    var row =
                        "<tr><td>" +
                        element.nome +
                        "</td><td>" +
                        habilitado +
                        "</td><td>" +
                        dataCriado +
                        "</td><td>" +
                        botaoEditar +
                        botaoTrocaStatus +
                        botaoRemover +
                        "</td></tr>";
                    rows.push(row);
                });

                table.append(rows);

                generateEditButtonFunctions("form-btn-edit");
                generateEnableButtonFunctions("form-btn-enable");
                generateDisableButtonFunctions("form-btn-disable");
                generateDeleteButtonFunctions("form-btn-delete");
            }
        });
    };

    var exibirTabela = function() {
        $("#formCadastro").hide(100);
        $("#dados").show(500);
        loadData();
    };

    var limparForm = function() {
        $("#id").val(null);
        $("#nome").val(null);
    };

    var cancelar = function() {
        exibirTabela();
    };

    var novo = function() {
        $("#titulo").text("Nova Categoria");
        $("#dados").hide(200);
        $("#formCadastro").show(500);
        limparForm();
    };

    var editar = function(val) {
        // esconde tabela de dados e exibe form

        $("#dados").fadeOut(100);
        $("#formCadastro").fadeIn(500);

        callLoaderAnimation();

        $.ajax({
            url: "/api/categorias_brindes/get_categoria_brinde",
            type: "GET",
            data: { id: val },
            success: function(resultSuccess) {},
            error: function(resultError) {
                closeLoaderAnimation();

                callModalError(resultError.mensagem.message);
            },
            complete: function(resultComplete) {
                closeLoaderAnimation();
                var categoriaBrinde =
                    resultComplete.responseJSON.categoria_brinde;
                $("#titulo").text("Editar Categoria " + categoriaBrinde.nome);
                $("#id").val(categoriaBrinde.id);
                $("#nome").val(categoriaBrinde.nome);
            }
        });
    };

    var abreModalHabilitar = function(val) {
        var modal = "#modal-enable";
        $(modal).modal();
        $(modal + " #nome-registro").text(categoriaBrindeSelected.nome);
        $(modal + " #confirmar").on("click", function() {
            alteraEstado(val, 1);
            $(modal).modal("hide");
            loadData();
        });
    };

    var abreModalDesabilitar = function(val) {
        var modal = "#modal-disable";
        $(modal).modal();
        $(modal + " #nome-registro").text(categoriaBrindeSelected.nome);
        $(modal + " #confirmar").on("click", function() {
            alteraEstado(val, 0);
            $(modal).modal("hide");
            loadData();
        });
    };

    var abreModalRemover = function(val) {
        $("#modal-remover").modal();
        $("#modal-remover #nome-registro").text(categoriaBrindeSelected.nome);
        $("#modal-remover #confirmar").on("click", function() {
            remover(val);
            $("#modal-remover").modal("hide");
            loadData();
        });
    };

    /**
     * categoriasBrindes/index::alteraEstado
     *
     * Desabilita um registro
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-28
     *
     * @param {int} id Id do Registro
     *
     * @returns void
     */
    var alteraEstado = function(id, habilitado) {
        $.ajax({
            url: "/api/categorias_brindes/update_status_categorias_brindes",
            type: "PUT",
            data: {
                id: id,
                habilitado: habilitado
            },
            success: function(res) {},
            error: function(res) {
                closeLoaderAnimation();
                callModalError(
                    res.responseJSON.mensagem.message,
                    res.responseJSON.mensagem.errors
                );
            },
            complete: function(res) {
                closeLoaderAnimation();
                // callModalGeneric(res.responseJSON.mensagem.message);
                loadData();
            }
        });
    };

    var remover = function(val) {
        data = { id: val };

        $.ajax({
            url: "/api/categorias_brindes/delete_categorias_brindes",
            type: "DELETE",
            data: data,
            success: function(result) {},
            error: function(result) {
                closeLoaderAnimation();
                callModalError(result.mensagem.message, result.mensagem.errors);
            },
            complete: function(result) {
                closeLoaderAnimation();
                callModalGeneric(result.responseJSON.mensagem.message);
            }
        });
    };

    var gravar = function() {
        validacaoGenericaForm();

        var formData = {};
        var dataSerialized = $(this.form).serializeArray();

        dataSerialized.forEach(function(item) {
            if (item.value !== undefined && item.value.toString().length > 0) {
                var key = item.name;
                var value = item.value;
                formData[key] = value;
            }
        });

        if (formData !== undefined) {
            var type = "POST";
            var url = "/api/categorias_brindes/save_categorias_brindes";

            if ($("#id").val() > 0) {
                type = "PUT";
                url = "/api/categorias_brindes/update_categorias_brindes";
            }

            $.ajax({
                url: url,
                type: type,
                data: formData,
                success: function(resultSuccess) {},
                error: function(resultError) {
                    closeLoaderAnimation();
                    callModalError(resultError.mensagem.message);
                },
                complete: function(resultComplete) {
                    closeLoaderAnimation();
                    callModalSave();
                    exibirTabela();
                }
            });
        }
    };

    $("#novo").on("click", novo);
    $(".botao-cancelar").on("click", cancelar);
    $(".botao-confirmar").on("click", gravar);

    // #endregion

    // Inicializa a tela
    loadData();
});
