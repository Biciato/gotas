class Gota {
    constructor(id, nomeParametro, multiplicadorGota, importar) {
        this.id = id;
        this.nomeParametro = nomeParametro;
        this.multiplicadorGota = multiplicadorGota;
        this.importar = importar;
    }
}

$
    (function () {
        "use strict";
        // #region Fields

        var botaoCancelar = $("#botao-cancelar");
        var botaoConfirmar = $("#botao-confirmar");
        var botaoGravarGotas = $("#botao-gravar-gotas");
        var botaoPesquisar = $("#botao-pesquisar");
        var redesSelectedItem = {};
        var clientesSelectedItem = {};
        var redesNome = $("#redes-nome");
        var clientesNome = $("#clientes-nome");
        var qrCode = $("#qr-code");
        var quantidadeMultiplicadorInput = $("#form-edicao #quantidade-multiplicador");
        var tabelaDados = $("#tabela-dados tbody");
        var tabela = $("#tabela-dados");

        var dataSelecionado = {};

        var data = [];

        function init() {
            // #region Bindings

            redesNome.val(null);
            clientesNome.val(null);
            data = [];
            tabelaDados.empty();
            alterarEstadoBotaoGravar();

            botaoCancelar.on("click", cancelarEdicaoGota);
            botaoConfirmar.on("click", confirmarEdicaoGota);
            qrCode.val(null);
            qrCode.unbind("keyup");
            qrCode.on("keyup", function (evt) {
                if (evt.keyCode == 13) {
                    loadData(qrCode.val());
                }
            });

            botaoPesquisar.unbind("click");
            botaoPesquisar.on("click", function (e) {
                loadData(qrCode.val());
            });

            botaoGravarGotas.unbind("click");
            botaoGravarGotas.on("click", saveData);

            // #endregion

            // Masks

            quantidadeMultiplicadorInput.val(null);
            quantidadeMultiplicadorInput.unmask();
            quantidadeMultiplicadorInput.mask("###0.00", {
                reverse: true
            });
        }

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
                .map(function (e) {
                    return e.id;
                })
                .indexOf(dataSelecionado.id);

            var gota = new Gota(dataSelecionado.id, nome, multiplicador, dataSelecionado.importar);

            data[index] = gota;
            console.log(data);
            gerarTabelaGotas(data);
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
        var alterarEstadoItem = function (dataSelecionado, habilitado) {
            // Obtem índice
            var index = data
                .map(function (obj) {
                    return obj.id;
                })
                .indexOf(dataSelecionado.id);

            dataSelecionado.importar = habilitado;
            data[index] = dataSelecionado;

            gerarTabelaGotas(data);
        };

        function alterarEstadoBotaoGravar() {
            var itens = data.filter(x => x.importar == true);

            if (itens.length == 0) {
                botaoGravarGotas.prop("disabled", true);
                botaoGravarGotas.prop("title", "Para gravar, é necessário que um dos itens esteja com a opção 'Importar' habilitada!");
            } else {
                botaoGravarGotas.prop("disabled", false);
                botaoGravarGotas.prop("title", "");
            }
        }

        /**
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::editar
         *
         * Exibe form de edição
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-06
         *
         * @param {Gota} gotaSefaz Registro
         *
         * @returns void
         */
        var editar = function (gotaSefaz) {
            $("#dados").fadeOut(100);
            $("#form-edicao").fadeIn(500);
            $("#form-edicao #nome").val(gotaSefaz.nomeParametro);

            $("#form-edicao #quantidade-multiplicador").val(
                gotaSefaz.multiplicadorGota
            );

            $("#form-edicao #quantidade-multiplicador").mask("####.###", {
                reverse: true
            });
        };

        //#region HTML Helpers
        var geraEditarButton = function (value, target) {
            var template =
                "<button class='btn btn-primary btn-xs form-btn-edit' id='form-btn-edit' value=" +
                value +
                "> <i class=' fa fa-edit'></i></button>";

            return template;
        };

        var geraEditarButtonFunctions = function (classString, data) {
            var click = function (e) {
                var id = $(this).val();

                var dataSelecionadoTemp = $.grep(data, function (cb) {
                    return cb.id == id;
                });

                if (dataSelecionadoTemp.length > 0) {
                    dataSelecionado = dataSelecionadoTemp[0];
                }

                editar(dataSelecionado);
            };

            $("." + classString).on("click", click);
        };

        var geraHabilitarButton = function (value, target) {
            var template =
                "<button class='btn btn-primary btn-xs form-btn-enable' id='form-btn-enable' value=" +
                value +
                "> <i class=' fa fa-power-off'></i></button>";

            alterarEstadoBotaoGravar();
            return template;
        };

        var geraHabilitarButtonFunctions = function (classString, data) {
            $("." + classString).on("click", function () {
                var id = $(this).val();
                dataSelecionado = data.find(obj => obj.id == id);
                alterarEstadoItem(dataSelecionado, true);
            });
        };

        var geraDesabilitarButton = function (value, target) {
            var template =
                "<button class='btn btn-danger btn-xs form-btn-disable' id='form-btn-disable' value=" +
                value +
                "> <i class=' fa fa-power-off'></i></button>";

            alterarEstadoBotaoGravar();

            return template;
        };

        var generateDisableButtonFunctions = function (classString, data) {
            $("." + classString).on("click", function () {
                var id = $(this).val();
                dataSelecionado = data.find(obj => obj.id == id);
                alterarEstadoItem(dataSelecionado, false);
            });
        };

        function obterQRCode() {
            botaoPesquisar.on("click", function (e) {
                var qrCode = $("#qr-code").val();
                loadData(qrCode);
            });
        }

        //#endregion

        // #region REST Services

        function loadData(qrCode) {
            $.ajax({
                type: "GET",
                url: "/api/sefaz/get_nf_sefaz_qr_code",
                data: {
                    qr_code: qrCode
                },
                dataType: "JSON",
                success: function (response) {
                    gerarTabelaGotas(response.data.sefaz.produtos.itens);
                    redesSelectedItem = response.data.rede;
                    redesNome.val(redesSelectedItem.nome_rede);
                    clientesSelectedItem = response.data.cliente;
                    clientesNome.val(clientesSelectedItem.nome_fantasia + " / " + clientesSelectedItem.razao_social);
                },
                error: function (response) {
                    var mensagem = response.responseJSON.mensagem;
                    callModalError(mensagem.message, mensagem.errors);
                }
            });
        }

        /**
         * Obtem os dados de categorias de brindes e alimenta a tabela
         *
         */
        function gerarTabelaGotas(dadosGotas) {

            data = [];
            var index = 1;
            dadosGotas.forEach(element => {
                // nomes em snake case vem do banco, então todo local que chamar o gerarTabelaGotas, deve vir em snake-case
                var gota = new Gota(
                    index,
                    element.nomeParametro,
                    element.multiplicadorGota,
                    element.importar
                );

                data.push(gota);
                index++;
            });

            tabelaDados.empty();
            tabela.pagination({
                pageSize: 10,
                showPrevious: true,
                showNext: true,
                dataSource: function (done) {
                    // this.data = [];
                    // var index = 1;
                    // dadosGotas.forEach(element => {
                    //     var gota = new Gota(
                    //         index,
                    //         element.nome_parametro,
                    //         element.multiplicador_gota,
                    //         element.importar
                    //     );

                    //     this.data.push(gota);
                    //     index++;
                    // });

                    done(data);
                },
                callback: function (data, pagination) {
                    var rows = [];
                    data.forEach(element => {
                        var importar = element.importar ? "Sim" : "Não";
                        var botaoEditar = geraEditarButton(element.id);
                        var botaoTrocaStatus = !element.importar ?
                            geraHabilitarButton(element.id) :
                            geraDesabilitarButton(element.id);

                        var row =
                            "<tr><td>" +
                            element.nomeParametro +
                            "</td><td>" +
                            element.multiplicadorGota +
                            "</td><td>" +
                            importar +
                            "</td><td>" +
                            botaoEditar +
                            botaoTrocaStatus +
                            "</td></tr>";
                        rows.push(row);
                    });

                    tabelaDados.append(rows);

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
            var gotas = data.filter(obj => obj.importar == true);
            var gotasSave = [];
            var clientesId = clientesSelectedItem.id;

            gotas.forEach(element => {
                var gota = {
                    id: element.id,
                    nome_parametro: element.nomeParametro,
                    multiplicador_gota: element.multiplicadorGota
                };

                gotasSave.push(gota);
            });

            var dataSend = {
                clientes_id: clientesId,
                gotas: gotasSave
            };

            $.ajax({
                type: "POST",
                url: "/api/gotas/set_gotas_clientes",
                data: dataSend,
                dataType: "JSON",
                success: function (response) {
                    callModalSave();
                    init();
                },
                error: function (response) {
                    var mensagem = response.responseJSON.mensagem;
                    callModalError(mensagem.message, mensagem.errors);
                }
            });
        }

        //#endregion

        init();
    })
    .ajaxStart(function () {
        callLoaderAnimation();
    }).ajaxStop(closeLoaderAnimation);
