$(function() {
    var brindesList = [];
    var brindesSelectedItem = {};
    var brindesSelectList = $("#brindes-list tbody");
    var clientesList = [];
    var clientesSelectListBox = $("#postos-rede");
    var clienteSelectedItem = {};
    var topBrindesSelectedItem = {};
    var topBrindesList = [];
    var topBrindesElementSortable = [];
    var topBrindesSortableStart = [];
    var topBrindesSortableFinish = [];
    var topBrindesSortable = $(".top-brindes-box-items");

    // #region Functions

    /**
     * Habilita sortable
     */
    $(".top-brindes-box-items").sortable({
        start: function(event, ui) {
            topBrindesSortableStart = getCurrentItemsSortable(
                ".top-brindes-box-items"
            );
        },
        stop: function(event, ui) {
            topBrindesSortableFinish = getCurrentItemsSortable(
                ".top-brindes-box-items"
            );

            var itemsSortableSend = compareItemsSortable(
                topBrindesSortableStart,
                topBrindesSortableFinish
            );
            // Executa rearrange de posições

            if (itemsSortableSend.length > 0) {
                setPosicaoTopBrindes(itemsSortableSend);
            }
        }
    });
    $(".top-brindes-box-items").on("click", "li", function() {
        var item = this.value;

        var t = $.grep(topBrindesList, function(is) {
            return is.id == item;
        });

        if (t.length > 0) {
            t = t[0];
        }
        topBrindesSelectedItem = t;

        showDetailsTopBrinde(topBrindesSelectedItem);
    });

    /**
     * nacional.js::compareItemsSortable
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-06
     *
     * @param {array} list1 Lista 1 de comparação
     * @param {array} list2 Lista 2 de comparação
     *
     * @returns {array} List Lista com valores diferentes
     */
    var compareItemsSortable = function(list1, list2) {
        // as listas terão sempre o mesmo tamanho
        var indexCheck = 0;
        var maxCheck = list1.length;
        var itemsSend = [];
        var item1 = null;
        var item2 = null;

        for (indexCheck = 0; indexCheck < maxCheck; indexCheck++) {
            item1 = list1[indexCheck];
            item2 = list2[indexCheck];
            if (!Object.is(JSON.stringify(item1), JSON.stringify(item2))) {
                itemsSend.push(item2);
            }
        }

        return itemsSend;
    };

    /**
     * Fecha a tela de detalhes
     */
    var closeTopBrindesDetails = function() {
        $(".top-brindes-details").fadeOut(500);
    };

    var confirmTopBrindesDelete = function() {
        $("#modal-remover #nome-registro").text(topBrindesSelectedItem.nome);
        $("#modal-remover").modal();
    };

    /**
     * nacional.js::deleteTopBrinde
     * 
     * Remove um Top Brinde
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-07
     * 
     * @returns void
     */
    var deleteTopBrinde = function() {

        $("#modal-remover").modal("hide");

        callLoaderAnimation();

        if(topBrindesSelectedItem !== undefined && topBrindesSelectedItem.id > 0) {
            var data = { id: topBrindesSelectedItem.id };

            $.ajax({
                type: "DELETE",
                url: "/api/top_brindes/delete_top_brindes",
                data: data,
                dataType: "JSON",
                success: function(response) {
                    closeLoaderAnimation();
                    callModalGeneric(response.mensagem.message);

                    getTopBrindesNacional();

                    closeTopBrindesDetails();
                },
                error: function(response) {
                    closeLoaderAnimation();
                    var error = response.responseJSON.mensagem;

                    if (error !== undefined) {
                        callModalError(error.message, error.errors);
                    } else {
                        error = response.responseJSON;

                        callModalError(error.message, []);
                    }
                }
            });
        }

    };

    var getTopBrindesNacional = function() {
        callLoaderAnimation("Aguarde... Obtendo Top Brindes...");
        var dataJson = {};
        topBrindesSortable.empty();

        $.ajax({
            type: "GET",
            url: "/api/top_brindes/get_top_brindes_nacional",
            data: dataJson,
            dataType: "JSON",
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
            success: function(response) {
                closeLoaderAnimation();

                var data = response.top_brindes;
                var count = 0;
                var rows = [];

                data.forEach(element => {
                    var item = {};

                    item.id = element.id;
                    item.img = element.brinde.nome_img_completo;
                    item.nome = element.brinde.nome;
                    item.posicao = element.posicao;
                    item.tipoVenda = element.brinde.tipo_venda;
                    item.tipoEquipamento = element.brinde.tipo_equipamento;
                    item.ilimitado = element.brinde.ilimitado;
                    item.esgotado = element.brinde.status_estoque;

                    var esgotado =
                        item.esgotado !== undefined &&
                        item.esgotado == "Esgotado";

                    var template =
                        "<li class='item-box' name='item-box" +
                        count +
                        "' id='item-box" +
                        count +
                        "' value='" +
                        item.id +
                        "'>";

                    if (esgotado) {
                        // Se for esgotado, mostra a span de 'esgotado' e modifica a imagem para grayscale
                        template +=
                            "<span id='item-box-esgotado-" +
                            count +
                            "' class='item-box-esgotado-text'>" +
                            item.esgotado +
                            "</span>";
                    }
                    template += "<img src='" + item.img + "'";
                    if (esgotado) {
                        template += "class='item-box-esgotado-img-disabled'";
                    }
                    template += " />";
                    template += "</li>";

                    topBrindesSortable.append(template);

                    count++;
                    rows.push(item);
                });

                topBrindesList = rows;
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
                error: function(response) {
                    closeLoaderAnimation();

                    var error = response.responseJSON.mensagem;
                    callModalError(error.message, error.errors);
                },
                success: function(response) {
                    closeLoaderAnimation();

                    var brindes = response.brindes.data;

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

    var getCurrentItemsSortable = function(elementClass) {
        topBrindesElementSortable = $(elementClass).sortable("toArray");

        var itemsPosition = [];
        var position = 1;
        topBrindesElementSortable.forEach(element => {
            var id = $("#" + element).val();

            var item = {
                id: id,
                posicao: position
            };
            position++;

            itemsPosition.push(item);
        });

        return itemsPosition;
    };

    var setTopBrindeNacional = function(e) {
        callLoaderAnimation("Aguarde, atribuindo Top Brinde...");
        $.ajax({
            type: "POST",
            url: "/api/top_brindes/set_top_brinde_nacional",
            data: { brindes_id: e },
            dataType: "JSON",
            success: function(response) {
                closeLoaderAnimation();

                // Fecha tela de adicionar e recarrega tela principal
                showMainScreen();
                getTopBrindesNacional();
            },
            error: function(response) {
                closeLoaderAnimation();

                var error = response.responseJSON.mensagem;
                callModalError(error.message, error.errors);
            }
        });
    };

    /**
     * nacional.js::setPosicaoTopBrindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-06
     *
     * @param {array} itemsToSend Lista para guardar (estrutura: [{ id: id, posicao: posicao}])
     *
     * @returns void
     */
    var setPosicaoTopBrindes = function(itemsToSend) {
        callLoaderAnimation("Aguarde, reajustando...");
        $.ajax({
            type: "PUT",
            url: "/api/top_brindes/set_posicoes_top_brindes_nacional",
            data: {
                top_brindes: itemsToSend
            },
            dataType: "JSON",
            success: function(response) {
                closeLoaderAnimation();
            },
            error: function(response) {
                closeLoaderAnimation();
            }
        });
    };

    var showMainScreen = function() {
        $("#dados").fadeIn(100);
        $("#form-vinculo").fadeOut(100);
    };

    var showAddTopBrinde = function() {
        var value = this.value;
        var item = $.grep(brindesList, function(brinde) {
            return brinde.id == value;
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

    /**
     * nacional.js::showDetailsTopBrinde
     *
     * Exibe detalhes do Top Brinde selecionado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-07
     *
     * @param {object} topBrinde Top Brinde Selecionado
     *
     * @returns void
     */
    var showDetailsTopBrinde = function(topBrinde) {
        $(".top-brindes-details").hide();
        $(".top-brindes-details").fadeIn(500);
        $("#top-brindes-details-img").hide();
        $("#top-brindes-details-img").fadeIn(700);
        $("#top-brindes-details-nome").val(topBrinde.nome);
        $("#top-brindes-details-tipo-venda").val(topBrinde.tipoVenda);
        $("#top-brindes-details-esgotado").val(topBrinde.esgotado);
        $("#top-brindes-details-ilimitado").val(
            topBrinde.ilimitado ? "Sim" : "Não"
        );
        $("#top-brindes-details-img").attr("src", topBrinde.img);
    };

    // Exibe modal brindes top nacional
    $("#brindes-list tbody").on(
        "click",
        ".botao-add-top-brinde",
        showAddTopBrinde
    );

    // Dispara adicionar top brindes nacional

    $("#modal-atribuir .modal-footer #confirmar").on("click", function() {
        $("#modal-atribuir").modal("hide");
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
            success: function(response) {
                closeLoaderAnimation();
                var data = response.clientes;

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
            },
            error: function(response) {
                closeLoaderAnimation();
                var error = response.responseJSON.mensagem;
                callModalError(error.message, error.errors);
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

    // Top Brindes Details

    $("#top-brindes-details-delete").on("click", confirmTopBrindesDelete);
    $("#top-brindes-details-cancel").on("click", closeTopBrindesDetails);

    // Remove

    $("#modal-remover .modal-footer").on(
        "click",
        "#confirmar",
        deleteTopBrinde
    );

    // Init

    getTopBrindesNacional();
});
