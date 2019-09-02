$(function() {

    getClientesList();

    // #region Properties

    var clientesSelectListBox = $("#clientesList");
    var clientesList = [];

    // #endregion

    // #region Functions

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
                    // option.value = 0;
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


});
