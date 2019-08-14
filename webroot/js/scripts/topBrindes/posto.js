/**
 * @file webroot\js\scripts\topBrindes\posto.js
 * 
 * Arquivo de funções para src\Template\TopBrindes\posto.ctp
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
    var clientesSelectListBox = $("#clientes-select-list");
    var clientesSelectedItem = {};
    var topBrindesSelectedItem = {};
    var topBrindesList = [];
    var topBrindesElementSortable = [];
    var topBrindesSortableStart = [];
    var topBrindesSortableFinish = [];
    var topBrindesSortable = $(".top-brindes-box-items");

    // #region Functions

    /**
     * posto.js::top-brindes-box-items.sortable()
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
     * posto.js::top-brindes-box-items.onClick
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
     * posto.js::clientesSelectOnChange
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
        var clientesId = e.target.value;

        clientesId = parseInt(clientesId);

        if (Number.isNaN(clientesId)) {
            clientesId = null;
            clientesSelectedItem = null;
            $(".box-items").hide();

        } else {
            var cliente = jQuery.grep(clientesList, function(cl) {
                return cl.id == clientesId;
            });

            if (cliente !== undefined && cliente.length > 0) {
                cliente = cliente[0];
            }

            clientesSelectedItem = cliente;
            $(".box-items").fadeIn(500);
            $("#clientes-selected-name").text(clientesSelectedItem.nomeFantasia);
        }

        getTopBrindesPosto(clientesId);
        closeLoaderAnimation();
        getBrindesPosto(clientesId);
    };

    /**
     * posto.js::compareItemsSortable
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
     * posto.js::closeTopBrindesAvailableTable
     * 
     * Esconde tabela de Top Brindes Disponiveis
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-13
     * 
     * @returns void
     */
    function closeTopBrindesAvailableTable() {
        $(".top-brindes-table").hide();
    }

    /**
     * posto.js::closeTopBrindesDetails
     * 
     * Fecha a tela de detalhes
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-07
     * 
     * @returns void
     */
    function closeTopBrindesDetails() {
        $(".top-brindes-details").hide();
    };

    /**
     * posto.js::deleteTopBrinde
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
                    getTopBrindesPosto(clientesSelectedItem.id);
                    getBrindesPosto(clientesSelectedItem.id);
                    closeTopBrindesDetails();
                    showTopBrindesAvailableTable();
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
     * posto.js::getTopBrindesPosto
     * 
     * Obtem Top Brindes Nacional
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com> 
     * @since 2019-08-06
     * 
     * @returns void
     */
    function getTopBrindesPosto(clientesId) {
        callLoaderAnimation("Aguarde... Obtendo Top Brindes...");
        var dataJson = {clientes_id: clientesId};
        topBrindesSortable.empty();
        topBrindesList = [];

        if (clientesId === null) {
            $(".box-container").hide();
        } else {
            $(".box-container").fadeIn(500);

            $.ajax({
                type: "GET",
                url: "/api/top_brindes/get_top_brindes_posto",
                data: dataJson,
                dataType: "JSON",
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
                        var li = document.createElement("li");
                        li.id = "item-box" + count;
                        li.setAttribute("name", "item-box" + count);
                        li.setAttribute("value", item.id);
                        var span = document.createElement("span");
                        span.id = "item-box-esgotado-" + count;
                        span.className = "item-box-esgotado-text";

                        if (item.esgotado !== undefined && item.esgotado !== "Normal") {
                            span.textContent = item.esgotado;
                        }

                        var img = new Image();
                        img.src = item.img;

                        if (esgotado) {
                            img.className = "item-box-esgotado-img-disabled";
                        }

                        li.appendChild(span);
                        li.appendChild(img);
                        topBrindesSortable.append(li);
                        rows.push(item);
                        count++;
                    });

                    topBrindesList = rows;
                }, error: function(response) {
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
        }
    }

    /**
     * posto.js::getBrindesPosto
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
            callLoaderAnimation("Obtendo brindes de unidade...");

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
     * posto.js::getCurrentItemsSortable
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
     * posto.js::getClientes
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
                // option = $("<option value=''>Selecionar...</option>");
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
     * posto.js::setTopBrindePosto
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
    function setTopBrindePosto(brindesId) {
        var data = {
            brindes_id: brindesId
        };

        callLoaderAnimation("Aguarde, atribuindo Top Brinde...");
        $.ajax({
            type: "POST",
            url: "/api/top_brindes/set_top_brinde_posto",
            data: data,
            dataType: "JSON",
            success: function(response) {
                closeLoaderAnimation();

                // Fecha tela de adicionar e recarrega tela principal
                // showMainScreen();
                getTopBrindesPosto(clientesSelectedItem.id);
                getBrindesPosto(clientesSelectedItem.id);
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
     * posto.js::setPosicaoTopBrindes
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
     * posto.js::showMainScreen
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
        // $("#top-brindes-table").hide();
    };

    /**
     * posto.js::showTopBrindesAdd
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
     * posto.js::showTopBrindesDetails
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
        $(".top-brindes-table").hide();
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
     * posto.js::showTopBrindesModalDelete
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

    /**
     * posto.js::showTopBrindesAvailableTable
     * 
     * Mostra tabela de Top Brindes Disponiveis
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-13
     * 
     * @returns void
     */
    function showTopBrindesAvailableTable() {
        $(".top-brindes-table").fadeIn(500);
    }

    // Exibe modal brindes top nacional
    $("#brindes-list tbody").on("click", ".botao-add-top-brinde", showTopBrindesAdd);

    // Oculta dados do top brinde e exibe a tabela de brindes disponível
    $(".top-brindes-details").on("click", "#top-brindes-details-cancel", function () {  
        closeTopBrindesDetails();
        showTopBrindesAvailableTable();
    });

    // Dispara adicionar top brindes nacional

    $("#modal-atribuir .modal-footer #confirmar").on("click", function() {
        $("#modal-atribuir").modal("hide");
        setTopBrindePosto(brindesSelectedItem.id);
    });

    /**
     * Bind de elementos e atributos
     */

    clientesSelectListBox.on("change", clientesSelectOnChange);

    // Mostra Form de vinculação de top Brinde
    var showNew = function(e) {
        $("#dados").hide();
        // $("#top-brindes-table").fadeIn(100);
        getClientes();
        brindesSelectList.empty();
    };

    // exibe tela principal

    // #endregion

    // Left Bar
    $("#novo").on("click", showNew);

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

    getClientes();
});
