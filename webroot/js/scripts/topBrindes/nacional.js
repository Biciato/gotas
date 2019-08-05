$(function() {
    var items = [];
    var brindesList = [];
    var brindesSelectedItem = {};
    var brindesSelectList = $("#brindes-list tbody");
    var clientesList = [];
    var clientesSelectListBox = $("#postos-rede");
    var clienteSelectedItem = {};
    var topBrindesSortable = $(".top-brindes-box-items");

    // #region Functions

    /**
     * Habilita sortable
     */
    $(".top-brindes-box-items").sortable({
        stop: function(event, ui) {
            console.log(event);
            console.log(ui);
        }
    });

    var setTopBrindeNacional = function(e) {
        callLoaderAnimation("Aguarde, atribuindo Top Brinde...");
        $.ajax({
            type: "POST",
            url: "/api/top_brindes/set_top_brinde_nacional",
            data: { brindes_id: e },
            dataType: "JSON",
            success: function(response) {},
            error: function(response) {
                closeLoaderAnimation();

                var error = response.responseJSON.mensagem;
                callModalError(error.message, error.errors);
            },
            complete: function(response) {
                closeLoaderAnimation();

                // Fecha tela de adicionar e recarrega tela principal
                showMainScreen();
                getBrindesNacional();
            }
        });
    };

    var showMainScreen = function() {
        $("#dados").fadeIn(100);
        $("#form-vinculo").fadeOut(100);

        // fecha todos os modais
        $(".modal").modal("hide");
    };

    var getBrindesNacional = function() {
        callLoaderAnimation("Aguarde... Obtendo Top Brindes...");
        var data = {};

        $.ajax({
            type: "GET",
            url: "/api/top_brindes/get_top_brindes_nacional",
            data: data,
            dataType: "JSON",
            success: function(response) {},
            error: function(response) {
                closeLoaderAnimation();
                console.log(response);
                var error = response.responseJSON.mensagem;

                if (error !== undefined) {
                    callModalError(error.message, error.errors);
                } else {
                    error = response.responseJSON;

                    callModalError(error.message, []);
                }
            },
            complete: function(response) {
                closeLoaderAnimation();

                console.log(response.responseJSON);
                // @todo completar

                var data = response.responseJSON.top_brindes;
                topBrindesSortable.empty();

                var count = 1;
                data.forEach(element => {
                    var item = {};

                    item.img = element.brinde.nome_img_completo;
                    item.nome = element.brinde.nome;
                    item.posicao = element.posicao;

                    var template =
                        "<li class='item-box' name='item-box" +
                        count +
                        "' id='item-box" +
                        count +
                        "'>";
                    template += "<img src='" + item.img + "' />";
                    template += "</li>";

                    topBrindesSortable.append(template);
                });
            }
        });
    };

    var getBrindesPosto = function(clientesId) {
        brindesSelectList.empty();

        if (clientesId !== undefined && clientesId > 0) {
            var data = {
                clientes_id: clientesId
            };

            callLoaderAnimation();

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

                    var rowsTemplate = [];
                    var itemsBrindes = [];

                    // var template = "<li> ";
                    // template += "<img src='"+item.img+"' /> <div class='text'> <strong>" + item.nome+ "</strong></div>";
                    // template += "<div class='button-area'><div class='btn btn-primary'><i class='fa fa-check'></i></div></div>";
                    // template += "</li>";

                    brindes.forEach(element => {
                        var item = {
                            id: element.id,
                            nome: element.nome_brinde_detalhado,
                            img: element.nome_img_completo
                        };

                        var template = "<tr>";
                        template += "<td><img src='" + item.img + "' /></td>";
                        template +=
                            "<td><div class='text'> <strong>" +
                            item.nome +
                            "</strong></div> </td>";
                        template +=
                            "<td><button class='btn btn-primary botao-add-top-brinde'  value='" +
                            item.id +
                            "' ><i class='fa fa-check'></i></<button></td>";
                        template += "</tr>";
                        itemsBrindes.push(item);
                        rowsTemplate.push(template);
                    });

                    brindesList = itemsBrindes;

                    brindesSelectList.append(rowsTemplate);
                }
            });
        }
    };

    var showAddTopBrinde = function() {
        var value = this.value;
        var item = $.grep(brindesList, function(brinde) {
            return (brinde.id = value);
        });

        if (item.length > 0) {
            item = item[0];
        }

        brindesSelectedItem = item;

        // Chama modal de atribuição
        $("#modal-atribuir").modal();
        $("#modal-atribuir #nome-registro").text(item.nome);
        $("#modal-atribuir .modal-footer #confirmar").val(item.id);
    };

    // Exibe modal brindes top nacional
    $("#brindes-list tbody").on(
        "click",
        ".botao-add-top-brinde",
        showAddTopBrinde
    );

    // Dispara adicionar top brindes nacional

    $("#modal-atribuir .modal-footer #confirmar").on("click", function() {
        $("#modal-atribuir").modal('hide');
        setTopBrindeNacional(brindesSelectedItem.id);
    });

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
        getBrindesPosto(clientesId);
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
    var showNew = function(e) {
        $("#dados").fadeOut(100);
        $("#form-vinculo").fadeIn(100);
        getPostosRede();
        brindesSelectList.empty();
    };

    // exibe tela principal

    // #endregion

    // Left Bar
    $("#novo").on("click", showNew);

    getBrindesNacional();
});
