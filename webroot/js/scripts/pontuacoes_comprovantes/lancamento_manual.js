var message = "";
$(function() {
    "use strict";

    var clienteSelectListBox = $("#clientes");
    var clientes = [];
    var clienteSelectedItem = null;
    var gotas = [];
    var gotasEnvio = [];
    var gotasSelectListBox = $("#gotas");
    var gotaSelectedItem = {};
    var dadosDiv = $("#dados");
    var gotaTabela = $("#gotas-table tbody");
    var quantidadeMultiplicador = $("#quantidade-multiplicador");
    var gravarGotasBtn = $("#botao-gravar-gotas");
    var idGotaTabela = 0;
    var inserirGotaBtn = $("#botao-inserir-gota");
    var qrCodeText = $("#qr-code");
    var redes = [];
    var reiniciarBtn = $("#reiniciar");
    var redeSelectedItem = null;
    var redeSelectListBox = $("#redes");
    var usuarioCpf = $("#usuario-cpf");
    var usuarioNome = $("#usuario-nome");
    var usuarioSaldo = $("#usuario-saldo");
    var usuarioSelectedItem = null;
    var valorReais = $("#valor-reais");

    /**
     * Constructor
     */
    function init() {
        getRedes();

        reiniciarBtn.unbind("click");
        reiniciarBtn.on("click", init);
        redeSelectListBox.unbind("change");
        redeSelectListBox.on("change", redesOnChange);

        clienteSelectListBox.unbind("change");
        clienteSelectListBox.on("change", clientesOnChange);
        gotasSelectListBox.unbind("change");
        gotasSelectListBox.on("change", gotasOnChange);
        inserirGotaBtn.unbind("click");
        inserirGotaBtn.on("click", inserirGotaProcessamento);
        gravarGotasBtn.unbind("click");
        gravarGotasBtn.on("click", gravarGotasUsuario);

        clienteSelectListBox.prop("readonly", false);
        clienteSelectListBox.prop("disabled", false);
        clienteSelectedItem = null;
        clienteSelectListBox.val(null);

        gotasSelectListBox.val(null);
        gotasSelectListBox.prop("disabled", true);
        quantidadeMultiplicador.val(null);
        quantidadeMultiplicador.prop("disabled", true);
        quantidadeMultiplicador.mask("####.###", { reverse: true });
        valorReais.val(null);
        valorReais.prop("disabled", true);
        inserirGotaBtn.prop("disabled", true);

        gotasEnvio = [];
        gotaTabela.empty();
        qrCodeText.val(null);

        usuarioSelectedItem = null;
        usuarioNome.val(null);
        usuarioCpf.val(null);
        usuarioSaldo.val(null);
        usuarioCpf.unbind("keyup");
        usuarioCpf.unmask();
        usuarioCpf.mask("###.###.###-##");
        usuarioCpf.on("keyup", cpfUsuarioOnChange);
        valorReais.maskMoney({ prefix: "R$ ", decimal: ",", thousands: "." });

        // Habilita/desabilita botão de gravar as gotas do cliente final
        updateButtonGravarGotas();
        toggleGotasDiv();
    }

    // #region Funções da tela

    /**
     * Evento disparado ao trocar cliente selecionado
     *
     * Ao selecionar um cliente, chama o método de pesquisa das gotas do estabelecimento selecionado
     *
     * webroot/js/scripts/pontuacoes_comprovantes/lancamento_manual.js::clientesOnChange
     *
     * @param {Event} event Event
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-12
     */
    function clientesOnChange(event) {
        var id = parseInt(event.target.value);

        if (!isNaN(id)) {
            var cliente = clientes.find(x => x.id == id);
            clienteSelectedItem = cliente;
        } else {
            clienteSelectedItem = null;
        }

        toggleGotasDiv();
    }

    /**
     * Evento disparado ao digitar cpf do Usuário
     *
     * Ao digitar o cpf do usuário, busca o registro na base de dados se o CPF existir
     *
     * webroot/js/scripts/pontuacoes_comprovantes/lancamento_manual.js::cpfUsuarioOnChange
     *
     * @param {Event} event Event
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-12
     */
    function cpfUsuarioOnChange(e) {
        var cpf = e.target.value;

        cpf = cpf.replace(/[^0-9]/g, "");

        if (cpf.length == 11) {
            console.log(moment());
            getUsuarioByCPF(cpf);
        } else {
            usuarioSelectedItem = null;
        }

    }

    /**
     * Gera template
     *
     * Gera template para tabela de gotas a ser enviadas
     *
     * webroot\js\scripts\pontuacoes_comprovantes\lancamento_manual.js::geraTemplateTabelaGotasEnviadas
     *
     * @param {int} id
     * @param {string} gota
     * @param {float} quantidade
     * @param {float} valor
     *
     * @returns {string} template
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-10
     */
    function geraTemplateTabelaGotasEnviadas(id, gota, quantidade, valor) {
        var string = [];

        string.push("<tr>");
        string.push("<td>" + gota + "</td>");
        string.push("<td class='text-right'>" + quantidade + "</td>");
        string.push("<td class='text-right'>" + valor + "</td>");
        string.push("<td>");
        string.push(
            "<div class='btn btn-danger btn-xs botao-remover' id='botao-remover' data-value='" +
                idGotaTabela +
                "' title='Remover Gota'>"
        );
        string.push("<i class='fas fa-remove'></i>");
        string.push("</div>");
        string.push("</td>");
        string.push("</tr>");

        idGotaTabela++;

        return string.join("");
    }

    /**
     * Método disparado ao selecionar gotas
     *
     * Ao selecionar uma gota, permite/bloqueia os campos em questão.
     *
     * webroot/js/scripts/pontuacoes_comprovantes/lancamento_manual.js::gotasOnChange
     *
     * @param {Event} evt
     *
     * @returns {void}
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-12
     */
    function gotasOnChange(evt) {
        var value = evt.target.value;

        quantidadeMultiplicador.val(null);
        valorReais.val(null);
        quantidadeMultiplicador.prop("disabled", true);
        valorReais.prop("disabled", true);
        inserirGotaBtn.prop("disabled", true);

        if (!isNaN(value)) {
            gotaSelectedItem = gotas.find(x => x.id == value);
            quantidadeMultiplicador.prop("disabled", false);
            valorReais.prop("disabled", false);
            inserirGotaBtn.prop("disabled", false);
        } else {
            gotaSelectedItem = {};
        }
    }

    /**
     * Grava as gotas do Usuário
     *
     * Realiza a gravação dos dados inseridos pelo operador
     *
     * webroot/js/scripts/pontuacoes_comprovantes/lancamento_manual.js::gravarGotasUsuario
     *
     * @param {Event} evt
     *
     * @returns {void}
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-13
     */
    function gravarGotasUsuario() {
        var pontuacoes = [];

        gotasEnvio.forEach(gota => {
            var pontuacao = {
                gotas_id: gota.id,
                quantidade_multiplicador: gota.quantidadeMultiplicador,
                valor: gota.valor
            };
            pontuacoes.push(pontuacao);
        });

        var data = {
            qr_code: qrCodeText.val(),
            clientes_id: clienteSelectedItem.id,
            usuarios_id: usuarioSelectedItem.id,
            pontuacoes: pontuacoes
        };

        setPontuacoesUsuario(data);
    }

    /**
     * Insere Gota
     *
     * Insere gota na fila para pontuar usuário
     *
     * webroot/js/scripts/pontuacoes_comprovantes/lancamento_manual.js::inserirGotaProcessamento
     *
     * @returns {void}
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-12
     */
    function inserirGotaProcessamento() {
        var gota = gotaSelectedItem;
        var litros = quantidadeMultiplicador.val();
        var valor = valorReais.val();

        if (gota.id == undefined) {
            callModalError("Selecione uma Gota para continuar!");
            return false;
        }

        if (litros.length == 0 || litros == 0) {
            callModalError(
                "Necessário informar <strong>Qte. Litros Abastecidos</strong>!"
            );
            return false;
        }

        var template = geraTemplateTabelaGotasEnviadas( gota.id, gota.nome, litros, valor);
        gotaTabela.append(template);
        gotasEnvio.push({
            id: gota.id,
            nome: gota.nome,
            quantidadeMultiplicador: litros,
            // converte para double
            valor: Number(valor.replace(/[^0-9_-]+/g, "")) / 100
        });

        // Habilita/desabilita botão de gravar as gotas do cliente final
        updateButtonGravarGotas();

        gotasSelectListBox.val(null);
        quantidadeMultiplicador.val(null);
        valorReais.val(null);
        inserirGotaBtn.prop("disabled", true);
        quantidadeMultiplicador.prop("disabled", true);
        valorReais.prop("disabled", true);

        // Re-atribui a função de remover Gota ao clicar
        $(".botao-remover").unbind("click");
        $(".botao-remover").on("click", removerGotaProcessamento);
    }

    /**
     * Insere Gota
     *
     * Insere gota na fila para pontuar usuário
     *
     * webroot/js/scripts/pontuacoes_comprovantes/lancamento_manual.js::inserirGotaProcessamento
     *
     * @param {Event} evt
     *
     * @returns {void}
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-12
     */
    function redesOnChange(evt) {
        var id = parseInt(evt.target.value);

        clienteSelectListBox.prop("readonly", false);
        clienteSelectListBox.prop("disabled", false);
        clienteSelectedItem = null;
        clienteSelectListBox.val(null);

        if (!isNaN(id)) {
            redeSelectedItem = redes.find(x => x.id == id);
            console.log(redeSelectedItem);
            getClientes(id);
        } else {
            usuarioSelectedItem = null;
            usuarioCpf.val(null);
            usuarioNome.val(null);
            usuarioSaldo.val(null);
            clienteSelectListBox.empty();
        }
        toggleGotasDiv();
    }

    /**
     * Remove Gota
     *
     * Remove gota da fila para pontuar usuário
     *
     * webroot/js/scripts/pontuacoes_comprovantes/lancamento_manual.js::inserirGotaProcessamento
     *
     * @param {Event} evt
     *
     * @returns {void}
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-12
     */
    function removerGotaProcessamento() {
        var value = parseInt(this.getAttribute("data-value"));
        idGotaTabela = 0;
        gotasEnvio.splice(value, 1);
        gotaTabela.empty();

        gotasEnvio.forEach(element => {
            var template = geraTemplateTabelaGotasEnviadas(
                element.id,
                element.nome,
                element.quantidadeMultiplicador,
                element.valor
            );
            gotaTabela.append(template);
        });

        // Re-atribui a função de remover Gota ao clicar
        $(".botao-remover").unbind("click");
        $(".botao-remover").on("click", removerGotaProcessamento);

        // Habilita/desabilita botão de gravar as gotas do cliente final
        updateButtonGravarGotas();

        // @todo continuar

        console.log(gotasEnvio);
        // alert("oi");
        console.log(value);
    }

    function toggleGotasDiv() {
        if (redeSelectedItem != null && usuarioSelectedItem != null) {
            getUsuarioPontuacoes(usuarioSelectedItem.id, redeSelectedItem.id);
        }

        if (redeSelectedItem != null && clienteSelectedItem!= null && usuarioSelectedItem!= null) {
            getGotasEstabelecimento(clienteSelectedItem.id);
            dadosDiv.fadeIn(500);
        } else {
            dadosDiv.fadeOut(500);
        }
    }

    /**
     * webroot/js/scripts/pontuacoes_comprovantes/lancamento_manual.js::updateButtonGravarGotas
     *
     * Atualiza status habilitado de botão gravar gotas do usuário
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-12
     */
    function updateButtonGravarGotas() {
        gravarGotasBtn.prop("disabled", true);

        if (gotasEnvio.length > 0) {
            gravarGotasBtn.prop("disabled", false);
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

                        clientes.push(cliente);
                        var option = document.createElement("option");
                        option.value = cliente.id;
                        option.textContent = cliente.nome;
                        clienteSelectListBox.append(option);
                    });

                    clienteSelectListBox.removeClass("disabled");

                    if (data.length == 1) {
                        var cliente = data[0];
                        clienteSelectedItem = cliente;
                        clientes.push(clienteSelectedItem);
                        clienteSelectListBox.val(cliente.id);
                        clienteSelectListBox.change();
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
            type: "GET",
            url: "/api/gotas/get_gotas_clientes",
            data: data,
            dataType: "JSON",
            success: function(response) {
                gotasSelectListBox.empty();

                var data = response.data.gotas;

                var emptyOption = document.createElement("option");
                emptyOption.value = null;
                emptyOption.textContent = "<Selecionar>";
                gotasSelectListBox.append(emptyOption);
                gotas = [];

                data.forEach(element => {
                    var gota = {
                        id: element.id,
                        nome: element.nome_parametro,
                        multiplicador: element.multiplicador_gota
                    };

                    var option = document.createElement("option");
                    option.value = gota.id;
                    option.textContent =
                        gota.nome + " - Multiplicador: " + gota.multiplicador;
                    gotasSelectListBox.append(option);
                    gotas.push(gota);
                });

                gotasSelectListBox.prop("disabled", true);

                if (data.length > 0) {
                    gotasSelectListBox.prop("disabled", false);
                }
            },
            error: function(response) {
                var msg = response.responseJSON.mensagem;
                callModalError(msg.message, msg.errors);
            }
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
                toggleGotasDiv();
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
                var saldo = response.resumo_gotas != undefined ? response.resumo_gotas.saldo : 0;
                usuarioSaldo.val(saldo);
            },
            error: function(response) {
                var mensagem = response.responseJSON.mensagem;

                callModalError(mensagem.message, mensagem.errors);
            }
        });
    }

    function setPontuacoesUsuario(data) {
        $.ajax({
            type: "POST",
            url:
                "/api/pontuacoes_comprovantes/set_comprovante_fiscal_usuario_manual",
            data: data,
            dataType: "JSON",
            success: function(response) {
                callModalSave();
                // Uma vez gravado, recarrega a tela
                init();
            },
            error: function(response) {
                var msg = response.responseJSON.mensagem;

                callModalError(msg.message, msg.errors);
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
