/**
 * Arquivo de 'controller' para src\Template\Gotas\importacao_gotas_sefaz.ctp
 *
 * @file webroot\js\scripts\gotas\importacao_gotas_sefaz.js
 * @author Gustavo Souza Gonçalves
 * @date 2019-10-10
 */

// class Gota {
//     constructor(id, nomeParametro, multiplicadorGota, importar) {
//         this.id = id;
//         this.nomeParametro = nomeParametro;
//         this.multiplicadorGota = multiplicadorGota;
//         this.importar = importar;
//     }
// }

$
    (function () {
        "use strict";

        // #region Fields

        var botaoCancelar = $("#botao-cancelar");
        var botaoConfirmar = $("#botao-confirmar");
        var botaoGravarGotas = $("#botao-gravar-gotas");
        var botaoPesquisar = $("#botao-pesquisar");
        var data = [];
        var dataSelecionado = {};
        var clientesSelectedItem = {};
        var clientesNome = $("#clientes-nome");
        var qrCode = $("#qr-code");
        var quantidadeMultiplicadorInput = $("#form-edicao #quantidade-multiplicador");
        var redesSelectedItem = {};
        var redesNome = $("#redes-nome");
        var tabelaDados = $("#tabela-dados tbody");
        var tabela = $("#tabela-dados");

        //#endregion

        // #region Functions

        /**
         * Constructor
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::init
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {void}
         */
        function init() {

            redesNome.val(null);
            clientesNome.val(null);
            data = [];
            tabelaDados.empty();
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
            quantidadeMultiplicadorInput.val(null);
            quantidadeMultiplicadorInput.unmask();
            quantidadeMultiplicadorInput.mask("###0.00", {
                reverse: true
            });

            alterarEstadoBotaoGravar();
        }

        /**
         * Cancela edição
         *
         * Cancela edição de gota selecionada
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::cancelarEdicaoGota
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {void}
         */
        function cancelarEdicaoGota() {
            $("#form-edicao").fadeOut(100);
            $("#dados").fadeIn(500);
        }

        /**
         * Confirma edição
         *
         * Confirma edição de gota selecionada
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::cancelarEdicaoGota
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {void}
         */
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

            var gota = new Gota(dataSelecionado.id, nome, multiplicador, null, null);
            gota.importar = dataSelecionado.importar;

            data[index] = gota;
            gerarTabelaGotas(data);
        }

        /**
         * Altera estado
         *
         * Altera estado de um item
         *
         * gotas/importacao_gotas_sefaz::alteraEstadoItem
         *
         * @param {Gota} dataSelecionado Registro Selecionado
         * @param {bool} habilitado Status
         *
         * @returns void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-06
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

        /**
         * Altera estado Botão Gravar
         *
         * Altera estado Botão Gravar conforme estado dos itens do grid
         *
         * gotas/importacao_gotas_sefaz::alteraEstadoItem
         *
         * @returns void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-06
         */
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

        /**
         * Gera Template
         *
         * Gera template de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraDesabilitarButton
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {HTMLButtonElement} Elemento Button
         */
        var geraDesabilitarButton = function (value) {
            var template =
                "<button class='btn btn-danger btn-xs form-btn-disable' id='form-btn-disable' value=" +
                value +
                "> <i class=' fa fa-power-off'></i></button>";

            alterarEstadoBotaoGravar();

            return template;
        };

        /**
         * Gera Funções
         *
         * Gera funções de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraDesabilitarButtonFunctions
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {Function} Elemento Button
         */
        var geraDesabilitarButtonFunctions = function (classString, data) {
            $("." + classString).on("click", function () {
                var id = $(this).val();
                dataSelecionado = data.find(obj => obj.id == id);
                alterarEstadoItem(dataSelecionado, false);
            });
        };

        /**
         * Gera Template
         *
         * Gera template de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraEditarButton
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {HTMLButtonElement} Elemento Button
         */
        var geraEditarButton = function (value) {
            var template =
                "<button class='btn btn-primary btn-xs form-btn-edit' id='form-btn-edit' value=" +
                value +
                "> <i class=' fa fa-edit'></i></button>";

            return template;
        };

        /**
         * Gera Funções
         *
         * Gera funções de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraEditarButtonFunctions
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {Function} Elemento Button
         */
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

        /**
         * Gera Template
         *
         * Gera template de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraHabilitarButton
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {HTMLButtonElement} Elemento Button
         */
        var geraHabilitarButton = function (value) {
            var template =
                "<button class='btn btn-primary btn-xs form-btn-enable' id='form-btn-enable' value=" +
                value +
                "> <i class=' fa fa-power-off'></i></button>";

            alterarEstadoBotaoGravar();
            return template;
        };

        /**
         * Gera Funções
         *
         * Gera funções de element button
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::geraHabilitarButtonFunctions
         *
         * @param {any} value Valor à ser definido
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         *
         * @returns {Function} Elemento Button
         */
        var geraHabilitarButtonFunctions = function (classString, data) {
            $("." + classString).on("click", function () {
                var id = $(this).val();
                dataSelecionado = data.find(obj => obj.id == id);
                alterarEstadoItem(dataSelecionado, true);
            });
        };

        //#endregion

        //#endregion

        // #region REST Services

        /**
         * Obtem dados
         *
         * Obtem dados do parâmetro fornecido
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::loadData
         *
         * @param {string} qrCode Link
         *
         * @returns {void}
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
         */
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
         * Gera Tabela
         *
         * Gera corpo de tabela conforme dados informados
         *
         * webroot\js\scripts\gotas\importacao_gotas_sefaz.js::gerarTabelaGotas
         *
         * @param {Gotas[]} Lista de gotas
         *
         * @returns {HTMLTableElement}
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-18
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
                    null,
                    null
                );

                gota.importar = element.importar;

                data.push(gota);
                index++;
            });

            tabelaDados.empty();
            tabela.pagination({
                pageSize: 10,
                showPrevious: true,
                showNext: true,
                dataSource: function (done) {
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
                    geraDesabilitarButtonFunctions("form-btn-disable", data);
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
                success: function () {
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
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation);
