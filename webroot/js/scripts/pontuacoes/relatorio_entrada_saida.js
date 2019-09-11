$(function() {
    // #region Properties

    var form = {};
    var data = {};
    var clientesSelectListBox = $("#clientes-list");
    var clientesList = [];
    var brindesSelectListBox = $("#brindes-list");
    var brindesList = [];

    var tipoRelatorio = $("#tipo-relatorio");
    var pesquisarBtn = $("#btn-pesquisar");
    var imprimirBtn = $("#btn-imprimir");

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
        brindesList = [];
        var option = document.createElement("option");
        option.value = undefined;
        option.textContent = "Selecione um Posto para continuar...";
        option.title = "Selecione um Posto para continuar...";
        brindesList.push(option);

        brindesSelectListBox.empty();
        brindesSelectListBox.append(brindesList);

        // Inicializa campos date

        dataInicio.datepicker().datepicker("setDate", dataAtual);
        dataFim.datepicker().datepicker("setDate", dataAtual);

        // Dispara todos os eventos que precisam de inicializar
        dataInicioOnChange();
        dataFimOnChange();
        tipoRelatorioOnChange();
        getClientesList();
        clientesSelectListBoxOnChange();
        brindesSelectListBoxOnChange();
    }


    /**
     * relatorio_entrada_saida.js::brindesSelectListBoxOnChange()
     *
     * Comportamento ao trocar o brinde selecionado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-11
     *
     * @return void
     */
    function brindesSelectListBoxOnChange() {
        var brinde = parseInt(brindesSelectListBox.val());

        brinde = isNaN(brinde) ? undefined : brinde;
        form.brindesId = brinde;
        console.log(form);
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
        var clienteSelected = this.value;

        if (isNaN(clienteSelected)) {
            clienteSelected = parseInt($("#cliente-selected").val());
        }

        if (!isNaN(clienteSelected)) {
            form.clientesId = clienteSelected;
            // Obtem Brindes
            getBrindesList(clienteSelected);
        }
    }

    function dataInicioOnChange() {
        var date = moment(this.value).format("YYYY-MM-DD");
        form.dataInicio = date;
        console.log('1');
    }

    function dataFimOnChange() {
        var date = moment(this.value).format("YYYY-MM-DD");
        form.dataFim = date;
        console.log('2');

    }

    function tipoRelatorioOnChange() {
        form.tipoRelatorio = tipoRelatorio.val();
    }

    // #region Get / Set REST Services

    function getBrindesList(clientesId) {
        callLoaderAnimation();
        $.ajax({
            type: "POST",
            url: "/api/brindes/get_brindes_unidade",
            data: {
                clientes_id: clientesId
            },
            dataType: "JSON",
            success: function(response) {
                console.log(response);

                if (response.brindes !== undefined) {
                    brindesSelectListBox.empty();

                    var data = response.brindes.data;
                    var collection = [];
                    var options = [];
                    var option = document.createElement("option");
                    option.title = "Selecionar Brinde para filtro específico";
                    option.textContent = "Todos";
                    options.push(option);

                    data.forEach(dataItem => {
                        var option = document.createElement("option");
                        var item = {
                            id: dataItem.id,
                            nome: dataItem.nome_brinde_detalhado
                        };

                        option.value = item.id;
                        option.textContent = item.nome;
                        collection.push(item);
                        options.push(option);
                    });

                    brindesSelectListBox.append(options);
                    brindesList = collection;
                }
            },
            error: function(response) {
                callModalError(
                    response.mensagem.message,
                    response.mensagem.error
                );
            },
            complete: function(response) {
                closeLoaderAnimation();
            }
        });
    }

    /**
     * webroot\js\scripts\gotas\relatorio_entrada_saida.js::getClientesList
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
                    option.textContent = "Selecionar";

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

                    // Option vazio e mais um posto? Desabilita pois só tem uma seleção possível
                    if (clientesList.length == 2) {
                        $(clientesSelectListBox).attr("disabled", true);
                    }
                }
                console.log(res);

                closeLoaderAnimation();
            },
            error: function(res) {
                console.log(res);
                closeLoaderAnimation();
            }
        });
    }

    function getEntradaSaida(
        clientesId,
        brindesId,
        dataInicio,
        dataFim,
        tipoRelatorio
    ) {
        console.log("oi");
        var data = {
            clientes_id: clientesId,
            brindes_id: brindesId,
            data_inicio: dataInicio,
            data_fim: dataFim,
            tipo_relatorio: tipoRelatorio
        };

        callLoaderAnimation();
        $.ajax({
            type: "GET",
            url: "/api/pontuacoes/get_pontuacoes_relatorio_entrada_saida",
            data: data,
            dataType: "JSON",
            success: function(response) {
                console.log(response);
            },
            error: function(response) {},
            complete: function(response) {
                closeLoaderAnimation();
            }
        });
    }

    // #endregion

    // #region Bindings

    brindesSelectListBox.on("change", brindesSelectListBoxOnChange);
    clientesSelectListBox.on("change", clientesSelectListBoxOnChange);

    dataInicio.on("change", dataInicioOnChange);
    dataFim.on("change", dataFimOnChange);

    tipoRelatorio.on("change", tipoRelatorioOnChange);

    $(pesquisarBtn).on("click", function() {
        console.log(form);
        getEntradaSaida(
            form.clientesId,
            form.brindesId,
            form.dataInicio,
            form.dataFim,
            form.tipoRelatorio
        );
    });
    // pesquisarBtn.on("click", function() { alert('oi') });
    console.log("oi");

    // #endregion

    // #endregion

    // 'Constroi' a tela
    init();

});
