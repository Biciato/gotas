/**
 * @file webroot\js\scripts\clientes\rel_cliente_final.js
 *
 * Arquivo de funções para Relatório de cliente final
 *
 * @author  Vinícius Carvalho de Abreu <vinicius@aigen.com.br>
 * @since 2020-03-21
 */

$
    (function () {
        'use strict';
        // #region Properties

        var form = {};
        var redesList = [];
        var redesSelectListBox = $("#redes-list");
        var redesSelectedItem = {};
        var clientesSelectListBox = $("#clientes-list");
        var clientesSelectedItem = {};
        var clientesList = [];
        var pesquisarPor = $("#usuario-options-search");
        var termoPesquisa = $("#usuario-parameter-search");
        var containerReport = $("#container-report");
        var pesquisarBtn = $("#btn-pesquisar");
        var imprimirBtn = $("#btn-imprimir");
        var exportarBtn = $("#btn-exportar");
        var buscarUsuariosBtn = $("#btn-buscar-usuarios");
        var veiculoRegion = $("#veiculo-region");
        var usuariosRegion = $("#usuarios-region");
        var usuariosSelectedItem = null;
        var usuarioNome = $("#usuario-nome");
        var usuarioSaldo = $("#usuario-saldo");
        var usuariosList = [];
        var usuariosSelectedItem = null;
        var usuariosTable = $("#usuarios-table");
        var selectedUser = $("#usuario-selecionado");

        var dataAtual = moment().format("DD/MM/YYYY");

        var valorDataInicio = new Date();
        valorDataInicio.setDate(valorDataInicio.getDate()-30);

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
            initialDate: valorDataInicio
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

        /**
         * Constructor
         */
        function init() {
            // Inicializa campos date
            dataInicio.datepicker().datepicker("setDate", valorDataInicio);
            dataFim.datepicker().datepicker("setDate", dataAtual);

            // Dispara todos os eventos que precisam de inicializar
            dataInicio.on("change", dataInicioOnChange);
            dataFim.on("change", dataFimOnChange);
            dataInicio.change();
            dataFim.change();

            redesSelectListBox.unbind("change");
            redesSelectListBox.on("change", redesSelectListBoxOnChange);
            clientesSelectListBox.unbind("change");
            clientesSelectListBox.on("change", clientesSelectListBoxOnChange);

            pesquisarPor.unbind("change");
            pesquisarPor.on("change", pesquisarPorOnChange);
            pesquisarPor.trigger("change");
            termoPesquisa.unbind("change");
            termoPesquisa.on("change", termoPesquisaOnChange);
            termoPesquisa.trigger("change");

            buscarUsuariosBtn.on('click', buscarUsuariosOnClick);

            // Atribuições de clicks aos botões de obtenção de relatório
            pesquisarBtn.on("click", pesquisar);
            imprimirBtn.addClass("disabled");
            imprimirBtn.addClass("readonly");
            imprimirBtn.unbind("click");
            imprimirBtn.on("click", imprimirRelatorio);
            exportarBtn.addClass("disabled");
            exportarBtn.addClass("readonly");
            exportarBtn.unbind("click");
            exportarBtn.on("click", exportarExcel);

            

            getRedesList();
        }

        /**
         * relatorio_entrada_saida.js::clientesSelectListBoxOnChange()
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
            var clientesId = isNaN(clienteSelected) ? 0 : clienteSelected;
            form.clientesId = clientesId;

            if (form.clientesId > 0) {
                clientesSelectedItem = clientesList.find(x => x.id === form.clientesId);
            } else {
                clientesSelectedItem = {};
            }
        }

        /**
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::dataInicioOnChange
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
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::dataFimOnChange
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

        function pesquisarPorOnChange() {
            var pesquisar_por = this.value;
            form.pesquisarPor = pesquisar_por;
            var textElement = termoPesquisa;
            var valueElement = clearNumbers(textElement.val());
            var listElement = pesquisarPor;
            var searchButton = buscarUsuariosBtn;
            textElement.val(null);
            textElement.unbind("focus")
                .unbind("blur")
                .unbind("keyup")
                .unbind("keydown")
                .prop("maxlength", 100)
                .unmask();

            if (listElement.val() === "nome") {

                return false;
            } else if (listElement.val() === "cpf") {
                textElement.mask("999.999.999-99");
                return false;
            } else if (listElement.val() === "telefone") {
                textElement.prop("maxlength", 11);

                textElement.on('focus', function () {
                    textElement.prop("maxlength", 11);

                }).on('focus', function () {
                    textElement.unmask();

                    textElement.prop("maxlength", 11);

                }).on('blur', function () {
                    if (this.value.length == 10) {
                        textElement.mask("(99)9999-9999");
                    } else {
                        textElement.mask("(99)99999-9999");
                    }
                }).on("keyup", function (event) {
                    // console.log(event);
                    this.value = clearNumbers(event.target.value);

                }).on("keydown", function (event) {
                    textElement.prop("maxlength", 11);

                    valueElement = clearNumbers(textElement.val());

                    if (event.keyCode == 13) {

                        if (listElement.val() === "telefone") {
                            if (valueElement.length <= 10) {
                                textElement.mask("(99)9999-9999");
                            } else {
                                textElement.mask("(99)99999-9999");
                            }
                        }

                        searchButton.click();
                    } else {
                        textElement.unmask();
                    }
                });

                return false;
            } else {
                textElement.mask("AAA9B99", {
                    'translation': {
                        A: {
                            pattern: /[A-Za-z]/
                        },
                        9: {
                            pattern: /[0-9]/
                        },
                        B: {
                            pattern: /\D*/
                        }
                    },
                    onKeyPress: function (value, event) {
                        event.currentTarget.value = value.toUpperCase();
                    }
                });
                return false;
            }
        }

        function termoPesquisaOnChange() {
            var termo_pesquisa = this.value;
            form.termoPesquisa = termo_pesquisa;
        }


        /**
         * Gera Excel
         *
         * Realiza pesquisa ao servidor e obtem conteúdo preparado para arquivo em Excel
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.5
         */
        async function exportarExcel() {
            try {
                let response = await getRelClienteFinal(form.redesId, form.clientesId, form.dataInicio, form.dataFim, usuariosSelectedItem, "Excel");
                // let response = await getRelClienteFinal(form.redesId, form.clientesId, "", "", "Table");

                if (response === undefined || response === null) {
                    return false;
                }

                var content = "data:application/vnd.ms-excel," + encodeURIComponent(response.data);
                var downloadLink = document.createElement("a");
                downloadLink.href = content;
                downloadLink.download = "Relatório de Ranking de Operações.xls";

                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);

            } catch (error) {
                var msg = {};
                if (error.responseJSON !== undefined) {
                    msg = error.responseJSON.mensagem;
                    callModalError(msg.message, msg.errors);
                } else if (error.responseText !== undefined) {
                    msg = error.responseText;
                    callModalError(msg);
                } else {
                    msg = error;
                    callModalError(msg);
                }
            }
        }

        /**
         * Obtem dados de Ranking de Operações
         *
         * @param {int} redesId Id da Rede. Se não informado, pesquisa por todos da rede
         * @param {int} clientesId Id do Estabelecimento. Se não informado, pesquisa por todos da rede
         * @param {DateTime} dataInicio Data de Início da pesquisa
         * @param {DateTime} dataFim Data de Fim da pesquisa
         * @param {string} tipoExportacao Tipo de Exportação (Table / Excel / Object)
         *
         * @returns $promise Retorna uma jqAjax Promise
         */
        function getRelClienteFinal(redesId, clientesId, dataInicio, dataFim, usuario, tipoExportacao) {
            var data = {
                redes_id: redesId,
                data_inicio: dataInicio,
                data_fim: dataFim,
                tipo_exportacao: tipoExportacao,
                usuario_selecionado: usuario
            };

            if (clientesId !== undefined && clientesId > 0) {
                data.clientes_id = form.clientesId;
            }

            if (redesId === undefined || redesId === 0) {
                callModalError("É necessário escolher uma rede para pesquisar!");
                return false;
            }

            return Promise.resolve($.ajax({
                type: "GET",
                url: "/api/clientes/cliente_final",
                data: data,
                dataType: "JSON",
            }));
        }

        function buscarUsuariosOnClick() {
            
            var url = pesquisarPor.val() === "placa" ? "/api/veiculos/get_usuarios_by_veiculo" : "/api/usuarios/get_usuarios_finais";
            
            var dataToSend = {};
            
            if (pesquisarPor.val() === "nome") {
                dataToSend.nome = termoPesquisa.val().trim();
            } else if (pesquisarPor.val() === "cpf") {
                dataToSend.cpf = clearNumbers(termoPesquisa.val().trim());
            } else if (pesquisarPor.val() === "telefone") {
                dataToSend.telefone = clearNumbers(termoPesquisa.val().trim());
            } else if (pesquisarPor.val() === "placa") {
                dataToSend.placa = termoPesquisa.val().trim();
            }
            
            veiculoRegion.hide();
            
            usuariosSelectedItem = {};
            usuarioNome.val(null);
            usuarioSaldo.val(null);
            
            $.ajax({
                type: "GET",
                url: url,
                data: dataToSend,
                dataType: "JSON",
                success: function (res) {
                    containerReport.empty();
                    usuariosSelectedItem = null;
                    imprimirBtn.addClass("disabled");
                    imprimirBtn.addClass("readonly");
                    imprimirBtn.unbind("click");
                    exportarBtn.addClass("disabled");
                    exportarBtn.addClass("readonly");
                    exportarBtn.unbind("click");
                    selectedUser.val("");

                    usuariosRegion.show();
                    var veiculo = {};
                    
                    usuariosList = [];
                    
                    if (pesquisarPor.val() === "placa") {
                        veiculo = res.data.veiculo;
                        veiculoRegion.show();
                        
                        updateVeiculosDetails(veiculo);
                        veiculo.usuarios_has_veiculos.forEach(item => {
                            
                            usuariosList.push(item.usuario);
                        });
                    } else
                    usuariosList = res.data.usuarios;
                    
                    var usuarioData = [];
                    usuariosList.forEach(usuario => {
                        
                        var selectButton = "<div data-id='" + usuario.id + "' class='btn btn-primary usuario-button-select' title='Selecionar'><i class='fas fa-check-circle'></i></div>";
                        
                        usuarioData.push({
                            id: usuario.id,
                            nome: usuario.nome,
                            telefone: usuario.telefone === undefined || usuario.telefone === null ? "" :  convertTextToPhone(usuario.telefone),
                            data_nasc: usuario.data_nasc === undefined || usuario.data_nasc === null ? "" : moment(usuario.data_nasc, "YYYY-MM-DD").format("DD/MM/YYYY"),
                            acoes: selectButton
                        });
                    });

                    if ($.fn.DataTable.isDataTable("#" + usuariosTable.attr('id'))) {
                        usuariosTable.DataTable().clear();
                        usuariosTable.DataTable().destroy();
                    }

                    usuariosTable.DataTable({
                        language: {
                            "url": "/js/DataTables/i18n/dataTables.pt-BR.lang",
                            // "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json",
                        },
                        columns: [{
                                data: "id",
                                name: "Id",
                                orderable: true,
                                visible: false,
                            },
                            {
                                data: "nome",
                                name: "Nome",
                                orderable: true,
                            },
                            {
                                data: "telefone",
                                name: "Telefone",
                                orderable: true,
                            },
                            {
                                data: "data_nasc",
                                name: "Data Nasc.",
                                orderable: true,
                            },
                            {
                                data: "acoes",
                                name: "Ações",
                                orderable: false,
                            }
                        ],
                        data: usuarioData
                    });

                    setTimeout(() => {

                        // Após renderizar a tabela, remove e reassocia evento de click dos botões
                        var usuarioButtonSelect = $(".usuario-button-select");
                        usuarioButtonSelect.unbind("click");

                        usuarioButtonSelect.on("click", function () {

                            var id = $(this).data('id');
                            usuariosSelectedItem = id;
                            var tds = $(this).parent().parent().children();
                            selectedUser.val(tds[0].textContent);
                            usuariosRegion.slideUp();
                            veiculoRegion.slideUp();

                        });
                    }, 300);


                },
                error: function (res) {

                    var mensagem = res.responseJSON.mensagem;
                    callModalError(mensagem.message, mensagem.errors);
                }

            });
        }

        function updateVeiculosDetails(data) {

            var veiculoPlaca = $("#veiculo-placa");
            var veiculoModelo = $("#veiculo-modelo");
            var veiculoFabricante = $("#veiculo-fabricante");
            var veiculoAno = $("#veiculo-ano");
            if (data === undefined) {
                veiculoPlaca.val(null);
                veiculoModelo.val(null);
                veiculoFabricante.val(null);
                veiculoAno.val(null);
            } else {
                veiculoPlaca.val(data.placa);
                veiculoModelo.val(data.modelo);
                veiculoFabricante.val(data.fabricante);
                veiculoAno.val(data.ano);
            }
        }
        /**
         * Evento disparado ao digitar cpf do Usuário
         *
         * Ao digitar o cpf do usuário, busca o registro na base de dados se o CPF existir
         *
         * @param {Event} event Event
         *
         * @returns void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.4
         */
        function usuarioParameterOptionsOnChange(e) {
           
        }
        /**
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::imprimirRelatorio
         *
         * Imprime relatório
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-12
         */
        function imprimirRelatorio() {
            setTimeout($(".print-region").printThis({
                importCss: false
            }), 100);
        };

        /**
         * Realiza pesquisa dos dados
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 1.1.6
         * @date 2020-03-05
         */
        async function pesquisar() {
            try {
                let response = await getRelClienteFinal(form.redesId, form.clientesId, form.dataInicio, form.dataFim, usuariosSelectedItem, "Table");
                // let response = await getRelClienteFinal(form.redesId, form.clientesId, "", "", "Table");

                if (response === undefined || response === null) {
                    return false;
                }

                imprimirBtn.removeClass("disabled");
                imprimirBtn.removeClass("readonly");
                imprimirBtn.unbind("click");
                imprimirBtn.on("click", imprimirRelatorio);
                exportarBtn.removeClass("disabled");
                exportarBtn.removeClass("readonly");
                exportarBtn.unbind("click");
                exportarBtn.on("click", exportarExcel);

                $(containerReport).empty();
                var indexesData = Object.keys(response.data);
                indexesData.forEach(element => {
                    $(containerReport).append(response.data[element]);
                });
            } catch (error) {
                var msg = {};
                if (error.responseJSON !== undefined) {
                    msg = error.responseJSON.mensagem;
                    callModalError(msg.message, msg.errors);
                } else if (error.responseText !== undefined) {
                    msg = error.responseText;
                    callModalError(msg);
                } else {
                    msg = error;
                    callModalError(msg);
                }
            }
        };

        /**
         * Redes On Change
         *
         * Comportamento ao atualizar rede selecionada
         *
         * webroot\js\scripts\pontuacoes\relatorio_entrada_saida.js::redesSelectListBoxOnChange
         *
         * @returns void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-26
         */
        function redesSelectListBoxOnChange() {
            var rede = parseInt(this.value);

            if (isNaN(rede)) {
                rede = 0;
            }

            redesSelectedItem = redesList.find(x => x.id == rede);

            if (redesSelectedItem.id > 0) {
                form.redesId = redesSelectedItem.id;
                getClientesList(redesSelectedItem.id);
            } else {
                form.redesId = 0;
                var option = document.createElement("option");
                option.value = 0;
                option.textContent = "Selecione uma Rede para continuar...";
                option.title = "Selecione uma Rede para continuar...";

                clientesList = [];
                clientesSelectListBox.empty();
                clientesSelectListBox.append(option);
            }
        }

        // #region Get / Set REST Services

        /**
         * webroot\js\scripts\gotas\relatorio_entrada_saida.js::getClientesList
         *
         * Obtem lista de clientes disponível para seleção
         *
         * @param {int} redesId Id da rede
         *
         * @return SelectListBox
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-06
         */
        function getClientesList(redesId) {
            $.ajax({
                type: "GET",
                url: "/api/clientes/get_clientes_list",
                data: {
                    redes_id: redesId
                },
                dataType: "JSON",
                success: function (res) {
                    if (res.data.clientes.length > 0) {
                        clientesList = [];
                        clientesSelectListBox.empty();

                        var cliente = {
                            id: 0,
                            nomeFantasia: "Todos"
                        };

                        clientesList.push(cliente);
                        clientesSelectListBox.prop("disabled", false);

                        res.data.clientes.forEach(cliente => {
                            var cliente = {
                                id: cliente.id,
                                nomeFantasia: cliente.nome_fantasia_municipio_estado
                            };

                            clientesList.push(cliente);
                        });

                        clientesList.forEach(cliente => {
                            var option = document.createElement("option");
                            option.value = cliente.id;
                            option.textContent = cliente.nomeFantasia;

                            clientesSelectListBox.append(option);
                        });

                        // Se só tem 2 registros, significa que
                        if (clientesList.length == 2) {
                            clientesSelectedItem = clientesList[1];

                            // Option vazio e mais um Estabelecimento? Desabilita pois só tem uma seleção possível
                            clientesSelectListBox.prop("disabled", true);
                        }

                        if (clientesSelectedItem !== undefined && clientesSelectedItem.id > 0) {
                            clientesSelectListBox.val(clientesSelectedItem.id);
                        }
                    }
                },
                error: function (response) {
                    var data = response.responseJSON;
                    callModalError(data.mensagem.message, data.mensagem.error);
                },
                complete: function (response) {
                    clientesSelectListBox.change();
                }
            });
        }

        /**
         * webroot\js\scripts\gotas\relatorio_entrada_saida.js::getRedesList
         *
         * Obtem lista de redes disponível para seleção
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-09-06
         *
         * return SelectListBox
         */
        function getRedesList() {
            $.ajax({
                type: "GET",
                url: "/api/redes/get_redes_list",
                data: {},
                dataType: "JSON",
                success: function (response) {
                    var data = response.data.redes;

                    if (data.length > 0) {
                        redesSelectListBox.empty();
                        redesList = [];
                        redesSelectedItem = {};
                        redesSelectListBox.prop("disabled", false);
                        redesSelectListBox.prop("readonly", false);

                        var rede = {
                            id: undefined,
                            nome: "<Selecionar>"
                        };

                        redesList.push(rede);

                        data.forEach(item => {
                            var rede = {
                                id: item.id,
                                nome: item.nome_rede
                            };

                            redesList.push(rede);
                        });

                        redesList.forEach(rede => {
                            var option = document.createElement("option");
                            option.value = rede.id;
                            option.textContent = rede.nome;

                            redesSelectListBox.append(option);
                        });

                        if (redesList.length == 2) {
                            // Se é 2 o tamanho, significa que só veio 1 rede do lado do servidor,
                            // que ou é a unica rede disponível ou a única rede selecionável dependendo do perfil do usuário
                            redesSelectedItem = redesList.find(x => x.id == data[0].id);
                            redesSelectListBox.val(redesSelectedItem.id);
                            redesSelectListBox.prop("disabled", true);
                            redesSelectListBox.prop("readonly", true);
                        }
                    }
                },
                error: function (response) {
                    var mensagem = response.responseJSON.mensagem;

                    callModalError(mensagem.message, mensagem.errors);
                },
                complete: function () {
                    redesSelectListBox.change();
                }
            });
        }

        // #endregion

        // #endregion

        // "Constroi" a tela
        init();
    })
    .ajaxStart(callLoaderAnimation)
    .ajaxStop(closeLoaderAnimation)
    .ajaxError(closeLoaderAnimation);
