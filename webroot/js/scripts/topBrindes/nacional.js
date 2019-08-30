/**
 * @file webroot\js\scripts\topBrindes\nacional.js
 * 
 * Arquivo de funções para src\Template\TopBrindes\nacional.ctp
 * 
 * @author  Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-08-01
 */

'use strict';
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
     * nacional.js::top-brindes-box-items.sortable()
     * 
     * Habilita sortable
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-04
     * 
     * @returns void
     */
    topBrindesSortable.sortable({
        distance: 20,
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

    /**
     * nacional.js::top-brindes-box-items.onClick
     * 
     * pega o objeto selecionado e exibe os detalhes
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-04
     * 
     * @returns void
     */
    topBrindesSortable.on("click", "li", function() {
        var item = this.value;

        var t = $.grep(topBrindesList, function(is) {
            return is.id == item;
        });

        if (t.length > 0) {
            t = t[0];
        }
        
        topBrindesSelectedItem = t;

        showTopBrindesDetails(topBrindesSelectedItem);
    });

    /**
     * nacional.js::clientesSelectOnChange
     * 
     * Dispara event ao selecionar cliente
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-06
     *
     * @param {Event} e
     * 
     * @returns void
     */
    function clientesSelectOnChange(e) {
        callLoaderAnimation("Obtendo brindes de unidade...");

        var clientesId = e.target.value;
        closeLoaderAnimation();
        getBrindesPosto(clientesId);
    };

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
    function compareItemsSortable(list1, list2) {
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
     * nacional.js::closeTopBrindesDetails
     * 
     * Fecha a tela de detalhes
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-07
     * 
     * @returns void
     */
    function closeTopBrindesDetails() {
        $(".top-brindes-details").fadeOut(500);
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
    function deleteTopBrinde() {
        $("#modal-remover").modal("hide");

        if(topBrindesSelectedItem !== undefined && topBrindesSelectedItem.id > 0) {
            callLoaderAnimation();
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

    /**
     * nacional.js::getTopBrindesNacional
     * 
     * Obtem Top Brindes Nacional
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com> 
     * @since 2019-08-06
     * 
     * @returns void
     */
    function getTopBrindesNacional() {
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

                var data = response.data.top_brindes;
                var count = 0;
                var rows = [];

                data.forEach(element => {
                    var item = {
                        id : element.id,
                        img : element.brinde.nome_img_completo,
                        nome : element.brinde.nome,
                        posicao : element.posicao,
                        tipoVenda : element.brinde.tipo_venda,
                        tipoEquipamento : element.brinde.tipo_equipamento,
                        ilimitado : element.brinde.ilimitado,
                        precoGotas: element.brinde.preco_atual.preco,
                        precoReais: element.brinde.preco_atual.valor_moeda_venda,
                        precoReaisFormatado: element.brinde.preco_atual.valor_moeda_venda_formatado !== null ? element.brinde.preco_atual.valor_moeda_venda_formatado : "" ,
                        esgotado :element.brinde.status_estoque
                    };

                    var esgotado = item.esgotado !== undefined && item.esgotado == "Esgotado";
                    var template = "<li class='item-box' name='item-box" + count + "' id='item-box" + count + "' value='" + item.id + "'>";

                    if (esgotado) {
                        // Se for esgotado, mostra a span de 'esgotado' e modifica a imagem para grayscale
                        template += "<span id='item-box-esgotado-" + count + "' class='item-box-esgotado-text'>" + item.esgotado + "</span>";
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

    /**
     * nacional.js::getBrindesPosto
     * 
     * Obtem os Brindes do Posto selecionado
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com> 
     * @since 2019-08-04
     * 
     * @param {int} clientesId Id de Clientes selecionado (Posto)
     * 
     * @returns void
     */
    function getBrindesPosto(clientesId) {
        brindesSelectList.empty();

        if (clientesId !== undefined && clientesId > 0) {
            var data = {
                clientes_id: clientesId
            };

            callLoaderAnimation();

            $.ajax({
                type: "GET",
                url: "/api/brindes/get_brindes_unidades_para_top_brindes",
                data: data,
                dataType: "json",
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

                    var brindes = response.data.brindes;
                    var rowsTemplate = [];
                    var itemsBrindes = [];

                    brindes.forEach(element => {
                        var item = {
                            id: element.id,
                            nome: element.nome_brinde_detalhado,
                            img: element.nome_img_completo,
                            precoGotas: element.preco_atual.preco,
                            precoReais: element.preco_atual.valor_moeda_venda,
                            precoReaisFormatado: element.preco_atual.valor_moeda_venda_formatado !== null ? element.preco_atual.valor_moeda_venda_formatado : "" ,
                            esgotado: element.status_estoque,
                        };

                        var esgotado = item.esgotado !== undefined && item.esgotado == "Esgotado";

                        var template = "<tr><td>";

                        if (esgotado) {
                            // Se for esgotado, mostra a span de 'esgotado' e modifica a imagem para grayscale
                            template += "<span class='brindes-postos-img-text-esgotado'>" + item.esgotado + "</span>";
                        }
                        template += "<img src='" + item.img + "' ";

                        if (esgotado) {
                            template += "class='brindes-postos-img-esgotado-disabled'";
                        }

                        template += "</td>";
                        template += "<td><div class='text'> <strong>" + item.nome + "</strong></div> </td>";
                        template +=  "<td><div class='text'> <strong>" + item.precoGotas + "</strong></div> </td>";
                        template +=  "<td><div class='text'> <strong>" + item.precoReaisFormatado + "</strong></div> </td>";
                        template += "<td><button class='btn btn-primary botao-add-top-brinde'  value='" + item.id + "' ><i class='fa fa-check'></i></<button></td>";
                        template += "</tr>";
                        itemsBrindes.push(item);
                        rowsTemplate.push(template);
                    });

                    brindesList = itemsBrindes;

                    if (brindesList.length == 0){
                        var row = document.createElement("tr");
                        var cell = document.createElement("td");
                        cell.colSpan = 5;
                        var span = document.createElement("span");
                        span.textContent = "Não há registros à exibir";
                        cell.append(span);

                        row.append(cell);
                        rowsTemplate.push(row);
                    }
                    
                    brindesSelectList.append(rowsTemplate);
                }
            });
        }
    };

    /**
     * nacional.js::getCurrentItemsSortable
     * 
     * Obtem o top brinde selecionado atualmente
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com> 
     * @since 2019-08-06
     * 
     * @param {int} clientesId Id de Clientes selecionado (Posto)
     * 
     * @returns void
     */
    function getCurrentItemsSortable(elementClass) {
        topBrindesElementSortable = $(elementClass).sortable("toArray");
        var position = 1;
        var itemsPosition = [];

        topBrindesElementSortable.forEach(element => {
            var id = $("#" + element).val();
            var item = {
                id: id,
                posicao: position
            };

            itemsPosition.push(item);
            position++;
        });

        return itemsPosition;
    };

    /**
     * nacional.js::getClientes
     * 
     * Obtem os Postos de uma rede
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * 
     * @since 2019-08-07
     * @returns void
     */
    function getClientes() {
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
                var option = new Option("Selecionar...", null);
                options.push(option);

                data.forEach(element => {
                    var item = {};

                    item.id = element.id;
                    item.nomeFantasia = element.nome_fantasia;
                    item.razaoSocial = element.razao_social;
                    rows.push(item);

                    var option = new Option(item.nomeFantasia, item.id);
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

    /**
     * nacional.js::setTopBrindeNacional
     * 
     * Define Brinde selecionado como Top Brinde Nacional
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com> 
     * @since 2019-08-06
     * 
     * @param {int} brindesId Id do Brinde
     * 
     * @returns void
     */
    function setTopBrindeNacional(brindesId) {
        callLoaderAnimation("Aguarde, atribuindo Top Brinde...");
        $.ajax({
            type: "POST",
            url: "/api/top_brindes/set_top_brinde_nacional",
            data: { brindes_id: brindesId },
            dataType: "JSON",
            success: function(response) {
                closeLoaderAnimation();

                // Fecha tela de adicionar e recarrega tela principal
                showMainScreen();
                getTopBrindesNacional();
                $("#new-button").css("display", "block");
                $("#back-button").css("display", "none");
            },
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
            }
        });
    };

    /**
     * nacional.js::setPosicaoTopBrindes
     *
     * Define posição dos topBrindes
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-06
     *
     * @param {array} itemsToSend Lista para guardar (estrutura: [{ id: id, posicao: posicao}])
     *
     * @returns void
     */
    function setPosicaoTopBrindes(itemsToSend) {
        callLoaderAnimation("Aguarde, reajustando...");
        $.ajax({
            type: "PUT",
            url: "/api/top_brindes/set_posicoes_top_brindes",
            data: {
                top_brindes: itemsToSend
            },
            dataType: "JSON",
            success: function(response) {
                closeLoaderAnimation();
            },
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
            }
        });
    };

    /**
     * nacional.js::showMainScreen
     * 
     * Exibe tela principal
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-08
     * 
     * @returns void
     */
    function showMainScreen() {
        $("#dados").fadeIn(100);
        $("#top-brindes-add").fadeOut(100);
    };

    /**
     * nacional.js::showTopBrindesAdd
     * 
     * Exibe tela de adicionar topBrindes
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-08
     * 
     * @returns void
     */
    function showTopBrindesAdd() {
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
     * nacional.js::showTopBrindesDetails
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
    function showTopBrindesDetails(topBrinde) {
        $(".top-brindes-details").hide();
        $(".top-brindes-details").fadeIn(500);
        $("#top-brindes-details-img").hide();
        $("#top-brindes-details-img").fadeIn(700);
        $("#top-brindes-details-nome").val(topBrinde.nome);
        $("#top-brindes-details-tipo-venda").val(topBrinde.tipoVenda);
        $("#top-brindes-details-esgotado").val(topBrinde.esgotado);
        $("#top-brindes-details-preco-gotas").val(topBrinde.precoGotas);
        $("#top-brindes-details-preco-reais").val(topBrinde.precoReaisFormatado);
        $("#top-brindes-details-ilimitado").val(
            topBrinde.ilimitado ? "Sim" : "Não"
        );
        $("#top-brindes-details-img").attr("src", topBrinde.img);
    };

    /**
     * nacional.js::showTopBrindesModalDelete
     * 
     * Exibe Modal de Remoção Top Brindes
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-07
     * 
     * @returns void
     */
    function showTopBrindesModalDelete() {
        $("#modal-remover #nome-registro").text(topBrindesSelectedItem.nome);
        $("#modal-remover").modal();
    };

    // Exibe modal brindes top nacional
    $("#brindes-list tbody").on("click", ".botao-add-top-brinde", showTopBrindesAdd);

    // Dispara adicionar top brindes nacional

    $("#modal-atribuir .modal-footer #confirmar").on("click", function() {
        $("#modal-atribuir").modal("hide");
        setTopBrindeNacional(brindesSelectedItem.id);
    });

    /**
     * Bind de elementos e atributos
     */

    clientesSelectListBox.on("change", clientesSelectOnChange);

    // Mostra Form de vinculação de top Brinde
    var newNavigation = function(e) {
        $("#dados").fadeOut(100);
        $("#top-brindes-add").fadeIn(100);
        $("#new-button").hide();
        $("#back-button").css("display", "block");
        getClientes();
        brindesSelectList.empty();
    };

    function backNavigation() {
        $("#top-brindes-add").hide();
        $("#dados").fadeIn(500);
        getTopBrindesNacional();
        brindesSelectList.empty();
        clientesSelectListBox.empty();
        $("#new-button").show();
        $("#back-button").hide();
    }

    // exibe tela principal

    // #endregion

    // Left Bar
    $("#new-button").on("click", newNavigation);
    $("#back-button").on("click", backNavigation);

    // Top Brindes Details

    $("#top-brindes-details-delete").on("click", showTopBrindesModalDelete);
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
