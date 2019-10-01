/**
 * Arquivo de funções para src\Template\Usuarios\relatorio_usuarios_cadastrados_funcionarios.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-09-25
 */

$(function() {
    'use strict';
    // #region Properties

    var form = {};
    var clientesSelectListBox = $("#clientes-list");
    var clientesList = [];
    var funcionariosSelectListBox = $("#funcionarios-list");
    var funcionariosList = [];

    var tabela = $("#tabela-dados");
    var conteudoTabela = $("#tabela-dados tbody");
    var tipoRelatorio = $("#tipo-relatorio");
    var pesquisarBtn = $("#btn-pesquisar");
    var imprimirBtn = $("#btn-imprimir");
    // @todo analisar se vai exportar
    var exportarBtn = "";

    var dataAtual = moment().format("DD/MM/YYYY");

    var dataInicio = $("#data-inicio").datepicker({
        minView: 2,
        maxView: 2,
        clearBtn: true,
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        forceParse: false,
        language: "pt-BR",
        format: "dd/mm/yyyy",
        initialDate: new Date()
    });
    var dataFim = $("#data-fim").datepicker({
        minView: 2,
        maxView: 2,
        clearBtn: true,
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        forceParse: false,
        language: "pt-BR",
        format: "dd/mm/yyyy",
        initialDate: new Date()
    });

    // #endregion

    // #region Functions

    function init() {
        funcionariosList = [];
        var option = document.createElement("option");
        option.value = undefined;
        option.textContent = "Selecione um Estabelecimento para continuar...";
        option.title = "Selecione um Estabelecimento para continuar...";

        funcionariosList.push(option);
        funcionariosSelectListBox.empty();
        funcionariosSelectListBox.append(funcionariosList);

        // Inicializa campos date
        dataInicio.datepicker().datepicker("setDate", dataAtual);
        dataFim.datepicker().datepicker("setDate", dataAtual);

        // Dispara todos os eventos que precisam de inicializar
        // dataInicioOnChange();
        // dataFimOnChange();
        tipoRelatorioOnChange();
        getClientesList();


        // Desabilita botão de imprimir até que usuário faça alguma consulta
        imprimirBtn.addClass("disabled");
        imprimirBtn.addClass("readonly");
        imprimirBtn.unbind("click");
    }

    /**
     * relatorio_gotas.js::brindesSelectListBoxOnChange()
     *
     * Comportamento ao trocar o brinde selecionado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-11
     *
     * @return void
     */
    function funcionariosSelectListBoxOnChange() {
        var funcionario = parseInt(funcionariosSelectListBox.val());

        funcionario = isNaN(funcionario) ? undefined : funcionario;
        form.funcionariosId = funcionario;
    }

    /**
     * relatorio_gotas.js::clientesSelectListBoxOnChange()
     *
     * Comportamento ao trocar o cliente selecionado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-11
     *
     * @return void
     */
    function clientesSelectListBoxOnChange() {
        var clienteSelected = clientesSelectListBox.val();

        // Se não tiver seleção, será feito via backend.
        clienteSelected = parseInt(clienteSelected);

        form.clientesId = isNaN(clienteSelected) ? 0 : clienteSelected;

        // Obtem Brindes
        getFuncionariosList(form.clientesId);
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::dataInicioOnChange
     *
     * Comportamento ao atualizar campo de data
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-12
     */
    function dataInicioOnChange() {
        var date = this.value;

        if (date !== undefined) {
            if (date.length == 8 && date.indexOf("/") == -1) {
                date = moment(date, "DDMMYYYY").format("DD/MM/YYYY");
                this.value = date;
            }

            date = moment(this.value, "DD/MM/YYYY").format("YYYY-MM-DD");
            form.dataInicio = date;
        }
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::dataFimOnChange
     *
     * Comportamento ao atualizar campo de data
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-12
     */
    function dataFimOnChange() {
        var date = this.value;

        if (date !== undefined) {
            if (date.length == 8 && date.indexOf("/") == -1) {
                date = moment(date, "DDMMYYYY").format("DD/MM/YYYY");
                this.value = date;
            }

            date = moment(this.value, "DD/MM/YYYY").format("YYYY-MM-DD");
            form.dataFim = date;
        }
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::imprimirRelatorio
     *
     * Imprime relatório
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-12
     */
    function imprimirRelatorio() {
        setTimeout(tabela.printThis({
            importCss: false
        }), 100);
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::tipoRelatorioOnChange
     *
     * Comportamento ao trocar o tipo de relatório
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-12
     */
    function tipoRelatorioOnChange() {
        form.tipoRelatorio = tipoRelatorio.val();
    }

    // #endregion

    // #region Get / Set REST Services

        /**
     * webroot\js\scripts\gotas\relatorio_gotas.js::getClientesList
     *
     * Obtem lista de clientes disponível para seleção
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-06
     *
     * return SelectListBox
     */
    function getClientesList() {
        callLoaderAnimation();
        $.ajax({
            type: "GET",
            url: "/api/clientes/get_clientes_list",
            data: {},
            dataType: "JSON",
            success: function(res) {
                if (res.clientes.length > 0) {
                    clientesList = [];
                    clientesSelectListBox.empty();

                    var option = document.createElement("option");
                    option.value = undefined;
                    option.textContent = "Todos";

                    clientesList.push(option);

                    res.clientes.forEach(cliente => {
                        var cliente = {
                            id: cliente.id,
                            value: cliente.nome_fantasia
                        };

                        var option = document.createElement("option");
                        option.value = cliente.id;
                        option.textContent = cliente.value;

                        clientesList.push(option);
                    });

                    clientesSelectListBox.append(clientesList);
                    var clienteSelected = $("#cliente-selected").val();

                    if (clienteSelected !== undefined && clienteSelected > 0) {
                        clientesSelectListBox.val(clienteSelected);
                    }

                    // Option vazio e mais um Estabelecimento? Desabilita pois só tem uma seleção possível
                    if (clientesList.length == 2) {
                        $(clientesSelectListBox).attr("disabled", true);
                    }
                }

                closeLoaderAnimation();
            },
            error: function(response) {
                var data = response.responseJSON;
                callModalError(data.mensagem.message, data.mensagem.error);
            },
            complete: function(response) {
                closeLoaderAnimation();
                clientesSelectListBoxOnChange();
                funcionariosSelectListBoxOnChange();
            }
        });
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::getFuncionariosList
     *
     * Obtem lista de Funcionários do posto(s) selecionado(s)
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-17
     *
     * @returns SelectListBox Lista de Seleção
     */
    function getFuncionariosList(clientesId) {
        callLoaderAnimation();
        $.ajax({
            type: "GET",
            url: "/api/usuarios/get_funcionarios_list",
            data: {
                clientes_id: clientesId,
                tipo_perfil: [5, 998]
            },
            dataType: "JSON",
            success: function(response) {

                if (response.data !== undefined) {
                    funcionariosSelectListBox.empty();
                    funcionariosList = [];

                    var data = response.data.usuarios;
                    var collection = [];
                    var options = [];
                    var option = document.createElement("option");
                    option.title = "Selecionar Funcionário para filtro específico";
                    option.textContent = "Todos";
                    options.push(option);

                    data.forEach(dataItem => {
                        var option = document.createElement("option");
                        var item = {
                            id: dataItem.usuario.id,
                            nome: dataItem.usuario.nome
                        };

                        option.value = item.id;
                        option.textContent = item.nome;
                        collection.push(item);
                        options.push(option);
                    });

                    funcionariosSelectListBox.append(options);
                    funcionariosList = collection;
                }
            },
            error: function(response) {
                var data = response.responseJSON;
                callModalError(data.mensagem.message, data.mensagem.error);
            },
            complete: function(response) {
                closeLoaderAnimation();
            }
        });
    }

    /**
     * webroot\js\scripts\pontuacoes\relatorio_gotas.js::getDataPontuacoesEntradaSaida
     *
     * Obtem os dados de relatório do servidor
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-12
     *
     * @param {int} clientesId Id do Cliente
     * @param {int} brindesId id do Brinde
     * @param {datetime} dataInicio Data Inicio
     * @param {datetime} dataFim DataFim
     * @param {string} tipoRelatorio Analítico / Sintético
     *
     * @returns HtmlTable
     */
    function getUsuariosCadastrados(clientesId, funcionariosId, dataInicio, dataFim, tipoRelatorio) {
        // Validação
        var dataInicioEnvio = moment(dataInicio);
        var dataFimEnvio = moment(dataFim);

        if (!dataInicioEnvio.isValid()) {
            dataInicioEnvio = undefined;
        } else {
            dataInicioEnvio = dataInicio;
        }

        if (!dataFimEnvio.isValid()) {
            dataFimEnvio = undefined;
        } else {
            dataFimEnvio = dataFim;
        }

        var data = {
            clientes_id: clientesId,
            funcionarios_id: funcionariosId,
            data_inicio: dataInicioEnvio,
            data_fim: dataFimEnvio,
            tipo_relatorio: tipoRelatorio
        };

        callLoaderAnimation();
        $.ajax({
            type: "GET",
            url: "/api/usuarios/get_usuarios_fidelizados_rede",
            data: data,
            dataType: "JSON",
            success: function(response) {
                // closeLoaderAnimation();

                imprimirBtn.removeClass("disabled");
                imprimirBtn.removeClass("readonly");
                imprimirBtn.on("click", imprimirRelatorio);

                var data = response.data;

                if (data.clientes_has_usuarios_total > 0) {
                    conteudoTabela.empty();

                    $(tabela).hide();
                    $(tabela).fadeIn(500);
                }

                var rows = [];

                if (form.tipoRelatorio == "Analítico") {
                    // data.pontuacoes.forEach(element => {
                    //     // Dados do Estabelecimento
                    //     var rowCliente = document.createElement("tr");

                    //     var labelCliente = document.createElement("strong");
                    //     labelCliente.textContent = "Estabelecimento: ";
                    //     var cellLabelCliente = document.createElement("td");
                    //     cellLabelCliente.classList.add("font-weight-bold");
                    //     cellLabelCliente.append(labelCliente);

                    //     var cellInfoCliente = document.createElement("td");
                    //     var infoCliente = document.createElement("strong");
                    //     infoCliente.textContent = element.cliente.nome_fantasia + " / " + element.cliente.razao_social;
                    //     cellInfoCliente.colSpan = 6;
                    //     cellInfoCliente.classList.add("text-right");

                    //     cellInfoCliente.append(infoCliente);

                    //     rowCliente.append(cellLabelCliente);
                    //     rowCliente.append(cellInfoCliente);

                    //     rows.push(rowCliente);

                    //     // Fim dados Estabelecimento

                    //     // Linhas periodos

                    //     var rowsPeriodos = [];
                    //     var pontuacoesLength = element.pontuacoes_entradas.length;
                    //     var pontuacoesEntradas = element.pontuacoes_entradas;
                    //     var pontuacoesSaidas = element.pontuacoes_saidas;

                    //     for (var pontuacoesIndex = 0; pontuacoesIndex < pontuacoesLength; pontuacoesIndex++) {
                    //         var pontuacoesEntradaPeriodoList = pontuacoesEntradas[pontuacoesIndex];
                    //         var pontuacoesSaidaPeriodoList = pontuacoesSaidas[pontuacoesIndex];

                    //         var pontuacoesDataLength = pontuacoesEntradaPeriodoList.data.length;
                    //         var pontuacoesEntradaDataList = pontuacoesEntradaPeriodoList.data;
                    //         var pontuacoesSaidaDataList = pontuacoesSaidaPeriodoList.data;

                    //         var mesAtual = '';
                    //         var ultimaData = '';

                    //         for (var indexData = 0; indexData < pontuacoesDataLength; indexData++) {
                    //             var entrada = pontuacoesEntradaDataList[indexData];
                    //             var saida = pontuacoesSaidaDataList[indexData];
                    //             var periodoAtual = moment(entrada.periodo, "YYYY-MM-DD").format("DD/MM/YYYY");

                    //             // O header deve ser construído se a data muda
                    //             if (ultimaData !== periodoAtual) {
                    //                 ultimaData = periodoAtual;
                    //                 // Linha que indica o cabeçalho dos períodos
                    //                 var rowPeriodo = document.createElement("tr");

                    //                 var cellPeriodoLabel = document.createElement("td");
                    //                 var labelPeriodo = document.createElement("strong");
                    //                 labelPeriodo.textContent = "Data";
                    //                 cellPeriodoLabel.append(labelPeriodo);

                    //                 var cellPeriodoTextoLabel = document.createElement("td");
                    //                 var labelPeriodoValue = document.createElement("strong");

                    //                 mesAtual = moment(entrada.periodo, "YYYY-MM-DD").format("MM/YYYY");
                    //                 labelPeriodoValue.textContent = periodoAtual;
                    //                 cellPeriodoTextoLabel.append(labelPeriodoValue);
                    //                 cellPeriodoTextoLabel.colSpan = 6;
                    //                 cellPeriodoTextoLabel.classList.add("text-right");

                    //                 rowPeriodo.append(cellPeriodoLabel);
                    //                 rowPeriodo.append(cellPeriodoTextoLabel);

                    //                 rowsPeriodos.push(rowPeriodo);

                    //                 // linha que indica o cabeçalho das colunas quem compõem o conjunto dos períodos
                    //                 var headerDadosPeriodoRow = document.createElement("tr");

                    //                 var cellLabelGota = document.createElement("td");
                    //                 var textlabelGota = document.createElement("strong");
                    //                 textlabelGota.textContent = "Gota:";
                    //                 cellLabelGota.append(textlabelGota);

                    //                 var cellLabelUsuarioEntrada = document.createElement("td");
                    //                 var textUsuarioEntrada = document.createElement("strong");
                    //                 textUsuarioEntrada.textContent = "Usuário:";
                    //                 cellLabelUsuarioEntrada.append(textUsuarioEntrada);

                    //                 var cellLabelGotasEntrada = document.createElement("td");
                    //                 var textEntradaGotas = document.createElement("strong");
                    //                 textEntradaGotas.textContent = "Gotas";
                    //                 cellLabelGotasEntrada.append(textEntradaGotas);

                    //                 var cellLabelUsuarioSaida = document.createElement("td");
                    //                 var textUsuarioSaida = document.createElement("strong");
                    //                 textUsuarioSaida.textContent = "Usuário:";
                    //                 cellLabelUsuarioSaida.append(textUsuarioSaida);

                    //                 var cellLabelBrindesSaida = document.createElement("td");
                    //                 var textBrindesSaida = document.createElement("strong");
                    //                 textBrindesSaida.textContent = "Brindes";
                    //                 cellLabelBrindesSaida.append(textBrindesSaida);

                    //                 var cellLabelGotasSaida = document.createElement("td");
                    //                 var textSaidaGotas = document.createElement("strong");
                    //                 textSaidaGotas.textContent = "Gotas";
                    //                 cellLabelGotasSaida.append(textSaidaGotas);

                    //                 headerDadosPeriodoRow.append(document.createElement("td"));
                    //                 headerDadosPeriodoRow.append(cellLabelGota);
                    //                 headerDadosPeriodoRow.append(cellLabelUsuarioEntrada);
                    //                 headerDadosPeriodoRow.append(cellLabelGotasEntrada);
                    //                 headerDadosPeriodoRow.append(cellLabelUsuarioSaida);
                    //                 headerDadosPeriodoRow.append(cellLabelBrindesSaida);
                    //                 headerDadosPeriodoRow.append(cellLabelGotasSaida);

                    //                 rowsPeriodos.push(headerDadosPeriodoRow);
                    //             }

                    //             // Info de entrada
                    //             var row = document.createElement("tr");
                    //             var cellEmpty = document.createElement("td");

                    //             row.append(cellEmpty);

                    //             var cellEntradaGota = document.createElement("td");
                    //             var labelEntradaGota = document.createElement("span");
                    //             labelEntradaGota.textContent = entrada.gota !== undefined ? entrada.gota.nome_parametro : "";
                    //             cellEntradaGota.append(labelEntradaGota);

                    //             var cellEntradaUsuario = document.createElement("td");
                    //             var labelEntradaUsuario = document.createElement("span");
                    //             labelEntradaUsuario.textContent = entrada.usuario !== undefined ? entrada.usuario.nome : "";
                    //             cellEntradaUsuario.append(labelEntradaUsuario);

                    //             var cellEntradaQteGota = document.createElement("td");
                    //             var labelEntradaQteGota = document.createElement("span");
                    //             labelEntradaQteGota.textContent = entrada.qte_gotas;
                    //             cellEntradaQteGota.classList.add("text-right");
                    //             cellEntradaQteGota.append(labelEntradaQteGota);

                    //             // Info de Saida
                    //             var cellSaidaUsuario = document.createElement("td");
                    //             var labelSaidaUsuario = document.createElement("span");
                    //             labelSaidaUsuario = saida.usuario !== undefined ? saida.usuario.nome : "";
                    //             cellSaidaUsuario.append(labelSaidaUsuario);

                    //             var cellSaidaBrinde = document.createElement("td");
                    //             var labelSaidaBrinde = document.createElement("span");
                    //             labelSaidaBrinde.textContent = saida.brinde !== undefined ? saida.brinde.nome_brinde_detalhado : "";
                    //             cellSaidaBrinde.append(labelSaidaBrinde);

                    //             var cellSaidaQteGota = document.createElement("td");
                    //             var labelSaidaQteGota = document.createElement("span");
                    //             labelSaidaQteGota.textContent = saida.qte_gotas;
                    //             cellSaidaQteGota.classList.add("text-right");
                    //             cellSaidaQteGota.append(labelSaidaQteGota);

                    //             row.append(cellEntradaGota);
                    //             row.append(cellEntradaUsuario);
                    //             row.append(cellEntradaQteGota);
                    //             row.append(cellSaidaUsuario);
                    //             row.append(cellSaidaBrinde);
                    //             row.append(cellSaidaQteGota);

                    //             rowsPeriodos.push(row);
                    //         }

                    //         // Total periodo

                    //         var rowTotalPeriodo = document.createElement("tr");
                    //         var cellLabelTotal = document.createElement("td");
                    //         var labelTotal = document.createElement("strong");

                    //         labelTotal.textContent = "Total Período: " + mesAtual;
                    //         cellLabelTotal.append(labelTotal);

                    //         var cellLabelEntradaTotal = document.createElement("td");
                    //         var labelEntradaTotal = document.createElement("strong");
                    //         labelEntradaTotal.textContent = pontuacoesEntradaPeriodoList.soma_entradas;
                    //         cellLabelEntradaTotal.classList.add("text-right");
                    //         cellLabelEntradaTotal.colSpan = 3;
                    //         cellLabelEntradaTotal.append(labelEntradaTotal);

                    //         var cellLabelSaidaTotal = document.createElement("td");
                    //         var labelSaidaTotal = document.createElement("strong");
                    //         labelSaidaTotal.textContent = pontuacoesSaidaPeriodoList.soma_saidas;
                    //         cellLabelSaidaTotal.classList.add("text-right");
                    //         cellLabelSaidaTotal.colSpan = 3;
                    //         cellLabelSaidaTotal.append(labelSaidaTotal);

                    //         rowTotalPeriodo.append(cellLabelTotal);
                    //         rowTotalPeriodo.append(cellLabelEntradaTotal);
                    //         rowTotalPeriodo.append(cellLabelSaidaTotal);

                    //         rowsPeriodos.push(rowTotalPeriodo);
                    //     }

                    //     if(pontuacoesLength == 0) {
                    //         // Se não teve registro, adiciona uma linha informando que não teve movimentação

                    //         var rowEmpty = document.createElement("tr");
                    //         var cell = document.createElement("td");
                    //         var label = document.createElement("strong");

                    //         label.textContent = "Não há registros à serem exibidos!";
                    //         cell.append(label);
                    //         cell.colSpan = 7;
                    //         cell.classList.add("text-center");
                    //         rowEmpty.append(cell);
                    //         rowsPeriodos.push(rowEmpty);
                    //     }

                    //     // Linhas Periodo

                    //     // Linha Total Geral

                    //     var rowTotal = document.createElement("tr");
                    //     var cellLabelTotal = document.createElement("td");
                    //     var labelTotal = document.createElement("strong");

                    //     labelTotal.classList.add("text-bold");
                    //     labelTotal.textContent = "Total Geral";
                    //     cellLabelTotal.append(labelTotal);

                    //     var textTotalEntradas = document.createElement("strong");
                    //     var cellTotalEntradas = document.createElement("td");
                    //     textTotalEntradas.textContent = data.total_entradas;
                    //     cellTotalEntradas.classList.add("text-right");
                    //     cellTotalEntradas.colSpan = 3;
                    //     cellTotalEntradas.append(textTotalEntradas);

                    //     var textTotalSaidas = document.createElement("strong");
                    //     var cellTotalSaidas = document.createElement("td");
                    //     textTotalSaidas.textContent = data.total_saidas;
                    //     cellTotalSaidas.classList.add("text-right");
                    //     cellTotalSaidas.colSpan = 3;
                    //     cellTotalSaidas.append(textTotalSaidas);

                    //     rowTotal.append(cellLabelTotal);
                    //     rowTotal.append(cellTotalEntradas);
                    //     rowTotal.append(cellTotalSaidas);

                    //     rowsPeriodos.push(rowTotal);

                    //     rowsPeriodos.forEach(element => {
                    //         rows.push(element);
                    //     });
                    // });
                } else {
                    data.clientes.forEach(estabelecimento => {
                        // Dados do Estabelecimento
                        var rowCliente = document.createElement("tr");

                        var cellLabelCliente = document.createElement("td");
                        var labelCliente = document.createElement("strong");
                        labelCliente.textContent = "Estabelecimento: ";
                        cellLabelCliente.colSpan = 2;
                        cellLabelCliente.append(labelCliente);

                        var cellInfoCliente = document.createElement("td");
                        var infoCliente = document.createElement("strong");
                        infoCliente.textContent = estabelecimento.nome_fantasia + " / " + estabelecimento.razao_social;
                        cellInfoCliente.colSpan = 2;
                        cellInfoCliente.append(infoCliente);


                        rowCliente.append(cellLabelCliente);
                        rowCliente.append(cellInfoCliente);

                        // Dados de Funcionário

                        var rowsInfoFuncionario = [];

                        estabelecimento.funcionarios.forEach(funcionario => {

                            var rowFuncionario = document.createElement("tr");

                            var cellTituloFuncionario = document.createElement("td");
                            var textTituloFuncionario = document.createElement("strong");
                            textTituloFuncionario.textContent = "Funcionário: ";
                            cellTituloFuncionario.colSpan = 2;
                            cellTituloFuncionario.append(textTituloFuncionario);

                            var cellLabelFuncionario = document.createElement("td");
                            var textLabelFuncionario = document.createElement("strong");
                            textLabelFuncionario.textContent = funcionario.usuario.nome + " (" + funcionario.usuario.email + ")";
                            cellLabelFuncionario.append(textLabelFuncionario);
                            cellLabelFuncionario.colSpan = 2;

                            rowFuncionario.append(cellTituloFuncionario);
                            rowFuncionario.append(cellLabelFuncionario);


                            // Dados de usuários cadastrados

                            var rowsUsuarios = [];

                            rowsInfoFuncionario.push(rowFuncionario);

                            // Header de informações dos clientes (SE tiver), se não tiver, apenas um header informando que não há usuários cadastrados para aquele período)

                            if(funcionario.usuario.clientes_has_usuarios.length > 0) {

                                var rowHeaderUsuarios = document.createElement("tr");

                                // nome email cpf data

                                var cellNomeTitulo = document.createElement("td");
                                var textNomeTitulo = document.createElement("strong");
                                textNomeTitulo.textContent = "Nome:";
                                cellNomeTitulo.append(textNomeTitulo);

                                var cellEmailTitulo = document.createElement("td");
                                var textEmailTitulo = document.createElement("strong");
                                textEmailTitulo.textContent = "Email:";
                                cellEmailTitulo.append(textEmailTitulo);

                                var cellCPFTitulo = document.createElement("td");
                                var textCPFTitulo = document.createElement("strong");
                                textCPFTitulo.textContent = "CPF:";
                                cellCPFTitulo.append(textCPFTitulo);

                                var cellDataTitulo = document.createElement("td");
                                var textDataTitulo = document.createElement("strong");
                                textDataTitulo.textContent = "Data:";
                                cellDataTitulo.append(textDataTitulo);

                                rowHeaderUsuarios.append(cellNomeTitulo);
                                rowHeaderUsuarios.append(cellEmailTitulo);
                                rowHeaderUsuarios.append(cellCPFTitulo);
                                rowHeaderUsuarios.append(cellDataTitulo);
                                rowsUsuarios.push(rowHeaderUsuarios);

                                funcionario.usuario.clientes_has_usuarios.forEach(clienteUsuario => {
                                    var cellNomeUsuario = document.createElement("td");
                                    var nomeUsuario = document.createElement("span");
                                    nomeUsuario.textContent = clienteUsuario.usuario.nome;
                                    cellNomeUsuario.append(nomeUsuario);

                                    var cellEmailUsuario = document.createElement("td");
                                    var textEmailUsuario = document.createElement("span");
                                    textEmailUsuario.textContent = clienteUsuario.usuario.email;
                                    cellEmailUsuario.append(textEmailUsuario);

                                    var cellCpfUsuario = document.createElement("td");
                                    var textCpfUsuario = document.createElement("span");
                                    textCpfUsuario.textContent = clienteUsuario.usuario.cpf_formatado;
                                    cellCpfUsuario.classList.add("text-right");
                                    cellCpfUsuario.append(textCpfUsuario);

                                    var cellDataCriacaoUsuario = document.createElement("td");
                                    var dataCriacaoUsuario = document.createElement("span");
                                    var data = moment(clienteUsuario.data, "YYYY-MM-DD HH:mm:ss").format("DD/MM/YYYY HH:mm:ss");
                                    dataCriacaoUsuario.textContent = data;
                                    cellDataCriacaoUsuario.classList.add("text-right");
                                    cellDataCriacaoUsuario.append(dataCriacaoUsuario);

                                    var rowUsuarioCadastrado = document.createElement("tr");
                                    rowUsuarioCadastrado.append(cellNomeUsuario);
                                    rowUsuarioCadastrado.append(cellEmailUsuario);
                                    rowUsuarioCadastrado.append(cellCpfUsuario);
                                    rowUsuarioCadastrado.append(cellDataCriacaoUsuario);

                                    rowsUsuarios.push(rowUsuarioCadastrado);

                                });

                                var rowTotalFuncionario = document.createElement("tr");

                                var cellTotalFuncionario = document.createElement("td");
                                var labelTotalFuncionario = document.createElement("strong");
                                labelTotalFuncionario.textContent = "Soma: ";
                                cellTotalFuncionario.append(labelTotalFuncionario);

                                var cellQteTotalFuncionario = document.createElement("td");
                                var textTotalFuncionario = document.createElement("strong");
                                textTotalFuncionario.textContent = funcionario.usuario.clientes_has_usuarios_soma;
                                cellQteTotalFuncionario.classList.add("text-right");
                                cellQteTotalFuncionario.colSpan = 3;
                                cellQteTotalFuncionario.append(textTotalFuncionario);

                                rowTotalFuncionario.append(cellTotalFuncionario);
                                rowTotalFuncionario.append(cellQteTotalFuncionario);

                                rowsUsuarios.forEach(row => {
                                    rowsInfoFuncionario.push(row);
                                });
                                rowsInfoFuncionario.push(rowTotalFuncionario);

                            } else {
                                // Não há usuário para o funcionário em questão

                                var rowInfoSemUsuario = document.createElement("tr");
                                var cellInfoSemUsuario = document.createElement("td");
                                var labelInfoSemUsuario = document.createElement("strong");
                                labelInfoSemUsuario.textContent = "Não há usuários cadastrados no período para o funcionário: " + funcionario.usuario.nome;
                                cellInfoSemUsuario.colSpan = 4;
                                cellInfoSemUsuario.classList.add("text-center");
                                cellInfoSemUsuario.append(labelInfoSemUsuario);
                                rowInfoSemUsuario.append(cellInfoSemUsuario);

                                rowsInfoFuncionario.push(rowInfoSemUsuario);
                            }
                        });




                        rows.push(rowCliente);

                        rowsInfoFuncionario.forEach(item => {
                            rows.push(item);
                        });

                    });

                    // Linha de soma total

                    var rowTotal = document.createElement("tr");
                    var cellLabelTotal = document.createElement("td");
                    var labelTotal = document.createElement("strong");

                    labelTotal.classList.add("text-bold");
                    labelTotal.textContent = "Total";
                    cellLabelTotal.append(labelTotal);

                    var textTotal = document.createElement("strong");
                    textTotal.textContent = data.clientes_has_usuarios_total;
                    var cellTotal = document.createElement("td");
                    cellTotal.classList.add("text-right");
                    cellTotal.colSpan = 3;
                    cellTotal.append(textTotal);

                    rowTotal.append(cellLabelTotal);
                    rowTotal.append(cellTotal);

                    rows.push(rowTotal);
                }
                conteudoTabela.append(rows);
            },
            error: function(response) {
                closeLoaderAnimation();
                var data = response.responseJSON;
                callModalError(data.mensagem.message, data.mensagem.errors);
            },
            complete: function(response) {
                closeLoaderAnimation();
            }
        });
    }

    // #endregion

    // #region Bindings

    funcionariosSelectListBox.on("change", funcionariosSelectListBoxOnChange);
    clientesSelectListBox.on("change", clientesSelectListBoxOnChange);
    dataInicio.on("change", dataInicioOnChange);
    dataFim.on("change", dataFimOnChange);
    tipoRelatorio.on("change", tipoRelatorioOnChange);

    $(pesquisarBtn).on("click", function() {
        getUsuariosCadastrados(form.clientesId, form.funcionariosId, form.dataInicio, form.dataFim, form.tipoRelatorio);
    });

    imprimirBtn.on("click", imprimirRelatorio);

    // #endregion

    // #endregion

    // "Constroi" a tela
    init();
});
