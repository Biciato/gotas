class GotaSefaz {
    constructor(id, nome, multiplicador, importar) {
        this.id = id;
        this.nome = nome;
        this.multiplicador = multiplicador;
        this.importar = importar;
    }
}

$(function() {
    "use strict";
    // #region Fields

    var botaoCancelar = $("#botao-cancelar");
    var botaoConfirmar = $("#botao-confirmar");
    var botaoGravarGotas = $("#botao-gravar-gotas");
    var botaoPesquisar = $("#botao-pesquisar");

    var dataSelecionado = {};

    var data = [];
    data.push(new GotaSefaz(1, "teste", 1, true));
    data.push(new GotaSefaz(2, "teste", 1, true));
    data.push(new GotaSefaz(3, "teste", 1, true));

    function cancelarEdicaoGota() {
        $("#form-edicao").fadeOut(100);
        $("#dados").fadeIn(500);
    }

    function confirmarEdicaoGota() {
        $("#form-edicao").fadeOut(100);
        $("#dados").fadeIn(500);

        var nome = $("#form-edicao #nome").val();
        var multiplicador = parseFloat(
            $("#form-edicao #quantidade-multiplicador").val()
        );

        var index = data
            .map(function(e) {
                return e.id;
            })
            .indexOf(dataSelecionado.id);

        dataSelecionado.nome = nome;
        dataSelecionado.multiplicador = multiplicador;

        data[index] = dataSelecionado;
        gerarTabelaGotas(data);
    }

    function init() {
        // #region Bindings

        botaoCancelar.on("click", cancelarEdicaoGota);
        botaoConfirmar.on("click", confirmarEdicaoGota);

        botaoPesquisar.on("click", function (e) {
            var qrCode = $("#qr-code").val();
            gerarTabelaGotas(qrCode);
        });

        botaoGravarGotas.on("click", saveData);

        // #endregion

        // Masks

        $("#form-edicao #quantidade-multiplicador").mask("####.###", {
            reverse: true
        });
    }

    /**
     * gotas/importacao_gotas_sefaz::alteraEstado
     *
     * Altera estado de um item
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-06
     *
     * @param {int} id Id do Registro
     *
     * @returns void
     */
    var alterarEstado = function(dataSelecionado, habilitado) {
        // Obtem índice
        var index = data
            .map(function(obj) {
                return obj.id;
            })
            .indexOf(dataSelecionado.id);

        dataSelecionado.importar = habilitado;
        data[index] = dataSelecionado;

        gerarTabelaGotas(data);
    };

    /**
     * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::editar
     *
     * Exibe form de edição
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-06
     *
     * @param {GotaSefaz} gotaSefaz Registro
     *
     * @returns void
     */
    var editar = function(gotaSefaz) {
        $("#dados").fadeOut(100);
        $("#form-edicao").fadeIn(500);
        $("#form-edicao #nome").val(gotaSefaz.nome);

        $("#form-edicao #quantidade-multiplicador").val(
            gotaSefaz.multiplicador
        );

        $("#form-edicao #quantidade-multiplicador").mask("####.###", {
            reverse: true
        });
    };

    var geraEditarButton = function(value, target) {
        var template =
            "<button class='btn btn-primary btn-xs form-btn-edit' id='form-btn-edit' value=" +
            value +
            "> <i class=' fa fa-edit'></i></button>";

        return template;
    };

    var geraEditarButtonFunctions = function(classString, data) {
        var click = function(e) {
            var id = $(this).val();

            var dataSelecionadoTemp = $.grep(data, function(cb) {
                return cb.id == id;
            });

            if (dataSelecionadoTemp.length > 0) {
                dataSelecionado = dataSelecionadoTemp[0];
            }

            editar(dataSelecionado);
        };

        $("." + classString).on("click", click);
    };

    var geraHabilitarButton = function(value, target) {
        var template =
            "<button class='btn btn-primary btn-xs form-btn-enable' id='form-btn-enable' value=" +
            value +
            "> <i class=' fa fa-power-off'></i></button>";
        return template;
    };

    var geraHabilitarButtonFunctions = function(classString, data) {
        $("." + classString).on("click", function() {
            var id = $(this).val();
            dataSelecionado = data.find(obj => obj.id == id);
            alterarEstado(dataSelecionado, true);
        });
    };

    var geraDesabilitarButton = function(value, target) {
        var template =
            "<button class='btn btn-danger btn-xs form-btn-disable' id='form-btn-disable' value=" +
            value +
            "> <i class=' fa fa-power-off'></i></button>";

        return template;
    };

    var generateDisableButtonFunctions = function(classString, data) {
        $("." + classString).on("click", function() {
            var id = $(this).val();
            dataSelecionado = data.find(obj => obj.id == id);
            alterarEstado(dataSelecionado, false);
        });
    };

    function loadData(qrCode) {
        $.ajax({
            type: "GET",
            url: "/api/gotas/get_nf_sefaz_qrcode",
            data: {
                qr_code: qrCode
            },
            dataType: "JSON",
            success: function (response) {

            }
        });
    }

    /**
     * Obtem os dados de categorias de brindes e alimenta a tabela
     *
     */
    function gerarTabelaGotas(data) {
        callLoaderAnimation();
        $("#tabela-dados tbody").empty();
        $("#tabela-dados").pagination({
            pageSize: 10,
            showPrevious: true,
            showNext: true,
            dataSource: function(done) {
                var rows = [];
                var index = 1;
                data.forEach(element => {
                    var gota = new GotaSefaz(
                        index,
                        element.nome,
                        element.multiplicador,
                        element.importar
                    );

                    rows.push(gota);
                    index++;
                });

                done(rows);
                closeLoaderAnimation();
            },
            callback: function(data, pagination) {
                var table = $("#tabela-dados tbody");
                table.empty();

                var rows = [];
                data.forEach(element => {
                    var importar = element.importar ? "Sim" : "Não";
                    var botaoEditar = geraEditarButton(element.id);
                    var botaoTrocaStatus = !element.importar
                        ? geraHabilitarButton(element.id)
                        : geraDesabilitarButton(element.id);

                    var row =
                        "<tr><td>" +
                        element.nome +
                        "</td><td>" +
                        element.multiplicador +
                        "</td><td>" +
                        importar +
                        "</td><td>" +
                        botaoEditar +
                        botaoTrocaStatus +
                        "</td></tr>";
                    rows.push(row);
                });

                table.append(rows);

                geraEditarButtonFunctions("form-btn-edit", data);
                geraHabilitarButtonFunctions("form-btn-enable", data);
                generateDisableButtonFunctions("form-btn-disable", data);
            }
        });
    }

    /**
     * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::saveData
     *
     * Envia as informações para gravação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-06
     *
     * @param {int} id Id do Registro
     *
     * @returns void
     */
    function saveData() {
        var dataSave = data.filter(obj => obj.importar == true);

        callLoaderAnimation();
        $.ajax({
            type: "POST",
            url: "/gotas/set_gotas_importacao_sefaz",
            data: dataSave,
            dataType: "JSON",
            success: function(response) {
                callModalSave();
            },
            error: function(response) {
                var mensagem = response.responseJSON;
                callModalError(mensagem.message, mensagem.errors);
            },
            complete: function(response) {
                closeLoaderAnimation();
            }
        });
    }

    init();
});
