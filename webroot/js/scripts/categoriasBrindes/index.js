$(document).ready(function() {
    // #region Fields

    var categoriaBrinde = {};
    
    // #endregion

    /**
     * Obtem os dados de categorias de brindes e alimenta a tabela
     *
     */
    var loadData = function() {
        var dataRequest = {};

        // if (params !== undefined && params.length > 0) {

        // }

        callLoaderAnimation();
        $.ajax({
            type: "GET",
            url: "/api/categorias_brindes/get_categorias_brindes",
            data: dataRequest,
            success: function(success) {},
            error: function(err) {
                closeLoaderAnimation();
                callModalError(err.mensagem.message, err.mensagem.errors);
            },
            complete: function(success) {
                closeLoaderAnimation();

                var table = $("#tabela-dados tbody");
                var dataReceived = success.responseJSON.categorias_brindes;

                dataReceived.forEach(element => {
                    table.append(element.nome);
                });
            }
        });
    };

    var exibirTabela = function() {
        $("#formCadastro").hide(100);
        $("#dados").show(500);
        loadData();
    };

    var limparForm = function(){
        $("#id").val(null);
        $("#nome").val(null);
    }

    var cancelar = function() {
        exibirTabela();
    };

    var novo = function() {
        $("#titulo").text("Nova Categoria");
        $("#dados").hide(200);
        $("#formCadastro").show(500);
        limparForm();
    };

    var editarCadastro = function(e) {};

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

        if (formData.length > 0) {
            $.ajax({
                url: "/api/categorias_brindes/save_categorias_brindes",
                type: "POST",
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

    // Inicializa a tela
    loadData();
});
