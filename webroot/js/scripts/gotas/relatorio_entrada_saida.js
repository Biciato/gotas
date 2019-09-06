$(function() {

    getClientesList();

    // #region Properties

    var clientesSelectListBox = $("#clientesList");
    var clientesList = [];
    var brindesSelectListBox = $("#brindesList");
    var brindesList = [];

    // #endregion

    // #region Functions

    function init() {
        brindesList = [];
        var option = document.createElement("option");
        option.value = undefined;
        // option.textContent = "Selecionar";
        option.textContent = "Selecione um Posto para continuar...";
        option.title = "Selecione um Posto para continuar...";
        brindesList.push(option);

        brindesSelectListBox.empty();
        brindesSelectListBox.append(brindesList);
    }

    init();

    function clientesSelectListBoxOnChange() {
        var clienteSelected = this.value;

        if (clienteSelected !== undefined) {
            // Obtem Brindes

            // Obtem
        }

    };

    clientesSelectListBox.on("change", clientesSelectListBoxOnChange);


    // #region Get / Set REST Services

    function getBrindesList(clientesId) {
        callLoaderAnimation();
        $.ajax({
            type: "POST",
            url: "/api/brindes/get_brindes_unidade",
            data: {
                clientes: clientesId
            },
            dataType: "JSON",
            success: function (response) {

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
    function getClientesList(){

        callLoaderAnimation();
        $.ajax({
            type: "GET",
            url: "/api/clientes/get_clientes_list",
            data: {},
            dataType: "JSON",
            success: function (res) {

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
                    var clienteSelected = $("#clienteSelected").val() ;

                    if (clienteSelected !== undefined && clienteSelected > 0) {
                        clientesSelectListBox.val(clienteSelected);
                    }
                }
                console.log(res);

                closeLoaderAnimation();
            },
            error: function (res) {
                console.log(res);
                closeLoaderAnimation();

            }
        });
    }

    // #endregion

    // #endregion


});
