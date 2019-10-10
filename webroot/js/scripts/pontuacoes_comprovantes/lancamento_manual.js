var message = "";
$(function() {
    // "use strict";
    console.log("oi");

    var redes = [];
    var redeSelectListBox = $("#redes");
    var redeSelectedItem = {};
    var clienteSelectListBox = $("#clientes");
    var clientes = [];
    var clienteSelectedItem = {};
    var usuarioSelectedItem = {};
    var usuarioCpf = $("#usuario-cpf");
    var usuarioNome = $("#usuario-nome");
    var usuarioSaldo = $("#usuario-saldo");
    var gotasEnvio = [];

    // var botaoRemover = $("#botao-remover");
    // botaoRemover.on("click", function() {
    //     var a = this;
    //     console.log(a);
    // })

    function init() {
        getRedes();

        redeSelectListBox.on("change", redesOnChange);
        clienteSelectListBox.on("change", clientesOnChange);

        usuarioCpf.mask("###.###.###-##");
        usuarioCpf.on("keyup", cpfUsuarioOnChange);
    }

    // #region Funções da tela

    function clientesOnChange(e) {
        var id = parseInt(e.target.value);

        if (!isNaN(id)) {
            var cliente = clientes.find(x => x.id == id);
            clienteSelectedItem = cliente;
            getGotasEstabelecimento(clienteSelectedItem.id);
        } else {
            clienteSelectedItem = {};
        }
    }

    function cpfUsuarioOnChange(e) {
        var cpf = e.target.value;

        cpf = cpf.replace(/[^0-9]/g, "");

        if (cpf.length == 11) {
            getUsuarioByCPF(cpf);
        }
    }

    /**
     * webroot\js\scripts\pontuacoes_comprovantes\lancamento_manual.js::geraTemplateTabelaGotasEnviadas
     *
     * Gera template de linhas ao adicionar gota na tabela
     *
     * @param {int} id
     * @param {string} gota
     * @param {float} quantidade
     *
     * @returns {string} template
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-10
     */
    function geraTemplateTabelaGotasEnviadas(id, gota, quantidade) {
        var string = [];

        string.push("<tr>");
        string.push("<td>" + gota + "</td>");
        string.push("<td>" + quantidade + "</td>");
        string.push("<td>");
        string.push(
            "<div class='btn btn-danger btn-xs' id='botao-remover' data-value='" +
                id +
                "' title='Remover Gota'>"
        );
        string.push("<i class='fas fa-remove'></i>");
        string.push("</div>");
        string.push("</td>");
        string.push("</tr>");

        return string.join("");
    }

    function redesOnChange(e) {
        var id = parseInt(e.target.value);

        clienteSelectListBox.prop("readonly", false);
        clienteSelectListBox.prop("disabled", false);

        if (!isNaN(id)) {
            redeSelectedItem = redes.find(x => x.id == id);
            console.log(redeSelectedItem);
            getClientes(id);
        } else {
            clienteSelectListBox.empty();
        }
    }

    // #endregion

    // #region Serviços REST

    /**
     * webroot\js\scripts\pontuacoes_comprovantes\lancamento_manual.js::getClientes
     *
     * Obtem os estabelecimentos da rede
     *
     * @returns {Select} ListBox
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-09
     */
    function getClientes(redesId) {
        clienteSelectListBox.empty();

        $.ajax({
            type: "GET",
            url: "/api/clientes/get_clientes_list",
            data: { redes_id: redesId },
            dataType: "JSON",
            success: function(response) {
                clienteSelectListBox.empty();
                var data = response.data.clientes;

                if (data.length > 0) {
                    var option = document.createElement("option");
                    option.value = null;
                    option.textContent = "";
                    clienteSelectListBox.append(option);

                    data.forEach(item => {
                        var cliente = {
                            id: item.id,
                            nome: item.nome_fantasia
                        };

                        redes.push(cliente);
                        var option = document.createElement("option");
                        option.value = cliente.id;
                        option.textContent = cliente.nome;
                        clienteSelectListBox.append(option);
                    });

                    clienteSelectListBox.removeClass("disabled");

                    if (data.length == 1) {
                        var cliente = data[0];
                        clienteSelectedItem = cliente;
                        clienteSelectListBox.val(cliente.id);
                        clienteSelectListBox.addClass("disabled");
                        clienteSelectListBox.prop("disabled", true);
                        clienteSelectListBox.prop("readonly", true);
                    }
                }
            },
            error: function(response) {
                var mensagem = response.responseJSON.mensagem;
                callModalError(mensagem.message, mensagem.errors);
            },
            complete: function(response) {}
        });
    }

    function getGotasEstabelecimento(clientesId) {
        var data = {
            clientes_id: clientesId
        };
        $.ajax({
            type: "method",
            url: "url",
            data: data,
            dataType: "dataType",
            success: function(response) {}
        });
    }

    /**
     * webroot\js\scripts\pontuacoes_comprovantes\lancamento_manual.js::getRedes
     *
     * Obtem as redes cadastradas e ativas
     *
     * @returns {Select} ListBox
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-09
     */
    function getRedes() {
        //

        $.ajax({
            type: "GET",
            url: "/api/redes/get_redes_list",
            data: {},
            dataType: "JSON",
            success: function(response) {
                var data = response.data.redes;

                if (data.length > 0) {
                    redeSelectListBox.empty();

                    var option = document.createElement("option");
                    option.value = null;
                    option.textContent = "";
                    redeSelectListBox.append(option);

                    data.forEach(item => {
                        var rede = {
                            id: item.id,
                            nome: item.nome_rede
                        };

                        redes.push(rede);

                        var option = document.createElement("option");
                        option.value = rede.id;
                        option.textContent = rede.nome;
                        redeSelectListBox.append(option);
                    });

                    if (data.length == 1) {
                        var rede = data[0];
                        redeSelectListBox.val(rede.id);
                        redeSelectListBox.classList.add("disabled");
                        redeSelectListBox.prop("disabled", true);
                        redeSelectListBox.prop("readonly", true);
                    }
                }
            },
            error: function(response) {
                var mensagem = response.responseJSON.mensagem;

                callModalError(mensagem.message, mensagem.errors);
            },
            complete: function(response) {
                //
            }
        });
    }

    function getUsuarioByCPF(cpf) {
        $.ajax({
            type: "POST",
            url: "/api/usuarios/get_usuario_by_cpf",
            data: { cpf: cpf },
            dataType: "JSON",
            success: function(response) {
                var data = response.user;
                console.log(data);
                usuarioSelectedItem = data;
                usuarioNome.val(data.nome);

                getUsuarioPontuacoes(
                    usuarioSelectedItem.id,
                    redeSelectedItem.id
                );
            },
            error: function(response) {
                var mensagem = response.responseJSON.mensagem;

                callModalError(mensagem.message, mensagem.errors);
            }
        });
    }

    /**
     *
     * @param {*} usuariosId
     * @param {*} redesId
     */
    function getUsuarioPontuacoes(usuariosId, redesId) {
        var data = { usuarios_id: usuariosId, redes_id: redesId };
        $.ajax({
            type: "POST",
            url: "/api/pontuacoes/get_pontuacoes_rede",
            data: data,
            dataType: "JSON",
            success: function(response) {
                var saldo =
                    response.resumo_gotas != undefined
                        ? response.resumo_gotas.saldo
                        : 0;
                usuarioSaldo.val(saldo);
            },
            error: function(response) {
                var mensagem = response.responseJSON.mensagem;

                callModalError(mensagem.message, mensagem.errors);
            }
        });
    }

    // #endregion

    init();
})
    .ajaxStart(function() {
        // console.log(this.message);
        callLoaderAnimation(message);
    })
    .ajaxStop(closeLoaderAnimation);
