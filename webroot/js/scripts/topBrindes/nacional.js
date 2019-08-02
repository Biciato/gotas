$(function() {
    var items = [];
    var brindesSelectList = $("#brindes-list");
    var clientesList = [];
    var clientesSelectListBox = $("#postos-rede");
    var clienteSelectedItem = {};


    // #region Functions

    var getBrindes = function(clientesId) {
        var data = {
            clientes_id: clientesId
        };

        $.ajax({
            type: "POST",
            url: "/api/brindes/get_brindes_unidade",
            data: data,
            dataType: "json",
            success: function(response) {},
            error: function(response) {
                closeLoaderAnimation();

                var error = response.responseJSON.mensagem;
                callModalError(error.message, error.errors);
            },
            complete: function(response) {
                closeLoaderAnimation();

                var brindes = response.responseJSON.brindes.data;
                brindesSelectList.empty();

                var rows = [];
                brindes.forEach(element => {
                    var template = "<li> ";
                    template += "<img src='"+element.id+"' /> <div class='text'> <strong>" + element.nome+ "</strong></div>";
                    template += "<div class='button-area'><div class='btn btn-primary'><i class='fa fa-check'></i></div></div>";
                    template += "</li>";
                    rows.push(template);
                });

                brindesSelectList.append(rows);
                
            }
        });
    };

    /**
     *
     */
    var getPostosRede = function() {
        callLoaderAnimation();

        $.ajax({
            type: "GET",
            url: "/api/clientes/get_clientes_list",
            data: {},
            dataType: "json",
            success: function(response) {},
            error: function(response) {
                closeLoaderAnimation();
                var error = response.responseJSON.mensagem;
                callModalError(error.message, error.errors);
            },
            complete: function(response) {
                closeLoaderAnimation();
                var data = response.responseJSON.clientes;

                clientesSelectListBox.empty();
                var rows = [];
                var options = [];
                option = $("<option value=''>Selecionar...</option>");
                options.push(option);

                data.forEach(element => {
                    var item = {};

                    item.id = element.id;
                    item.nomeFantasia = element.nome_fantasia;
                    item.razaoSocial = element.razao_social;
                    rows.push(item);

                    var option = $(
                        "<option value='" +
                            item.id +
                            "'>" +
                            item.nomeFantasia +
                            "</option>"
                    );
                    options.push(option);
                });
                // alimenta o data source de clientes
                clientesList = rows;
                clientesSelectListBox.append(options);
            }
        });
    };

    var clientesSelectOnChange = function(e) {
        callLoaderAnimation("Obtendo brindes de unidade...");

        var clientesId = e.target.value;
        console.log(e);
        closeLoaderAnimation();
        getBrindes(clientesId);
    };

    /**
     * Bind de elementos e atributos
     */

    clientesSelectListBox.on("change", clientesSelectOnChange);

    $(items).on("change", function(e) {
        console.log(e);
    });

    items = [
        {
            id: 0
        }
    ];

    // Mostra Form de vinculação de top Brinde
    var novo = function(e) {
        $("#dados").fadeOut(100);
        $("#form-vinculo").fadeIn(100);
        getPostosRede();
    };

    // #endregion

    // Left Bar
    $("#novo").on("click", novo);

    /**
     * Habilita sortable
     */
    $(".box-items").sortable({
        stop: function(event, ui) {
            console.log(event);
            console.log(ui);
        }
    });
});
