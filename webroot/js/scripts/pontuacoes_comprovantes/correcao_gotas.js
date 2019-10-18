var message = "";
$
    (function () {
        "use strict";

        var gotaSelectedItem = {};
        var quantidadeMultiplicador = $("#quantidade-multiplicador");
        var gravarGotasBtn = $("#botao-gravar-gotas");
        var idGotaTabela = 0;
        var redes = [];
        var reiniciarBtn = $("#reiniciar");
        var redeSelectedItem = null;
        var redeSelectListBox = $("#redes");
        var usuarioCpf = $("#usuario-cpf");
        var usuarioNome = $("#usuario-nome");
        var usuarioSaldo = $("#usuario-saldo");
        var usuarioSelectedItem = null;

        /**
         * Constructor
         */
        function init() {
            getRedes();

            reiniciarBtn.unbind("click");
            reiniciarBtn.on("click", init);
            redeSelectListBox.unbind("change");
            redeSelectListBox.on("change", redesOnChange);

            gravarGotasBtn.unbind("click");
            gravarGotasBtn.on("click", setGotasManualUsuario);

            quantidadeMultiplicador.val(null);
            quantidadeMultiplicador.prop("disabled", true);
            quantidadeMultiplicador.on("keyup", updateButtonGravarGotas);
            // quantidadeMultiplicador.mask("####", {
            //     pattern: /-|\d/,
            //     recursive: true
            // });
            quantidadeMultiplicador.mask("Z####", {
                translation: {
                    '#': {
                        pattern: /\d/
                    },
                    'Z': {
                        pattern: /[\-]/,
                        optional: true
                    }
                }

            });

            usuarioSelectedItem = null;
            usuarioNome.val(null);
            usuarioCpf.val(null);
            usuarioSaldo.val(null);
            usuarioCpf.unbind("keyup");
            usuarioCpf.unmask();
            usuarioCpf.mask("###.###.###-##");
            usuarioCpf.on("keyup", usuarioCPFOnChange);

            // Habilita/desabilita botão de gravar as gotas do cliente final
            updateButtonGravarGotas();
        }

        // #region Funções da tela

        /**
         * Evento disparado ao digitar cpf do Usuário
         *
         * Ao digitar o cpf do usuário, busca o registro na base de dados se o CPF existir
         *
         * webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas.js::usuarioCPFOnChange
         *
         * @param {Event} event Event
         *
         * @returns void
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-12
         */
        function usuarioCPFOnChange(e) {
            var cpf = e.target.value;

            cpf = cpf.replace(/[^0-9]/g, "");

            if (cpf.length == 11) {
                console.log(moment());
                getUsuarioByCPF(cpf);
            } else {
                usuarioSelectedItem = null;
                // usuarioCpf.val(null);
                usuarioSaldo.val(null);
                updateButtonGravarGotas();
            }
        }

        /**
         * Gera template
         *
         * Gera template para tabela de gotas a ser enviadas
         *
         * webroot\js\scripts\pontuacoes_comprovantes\correcao_gotas.js::geraTemplateTabelaGotasEnviadas
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
         * Redes on Change
         *
         * Atualiza lista de clientes ao selecionar uma rede
         *
         * webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas.js::redesOnChange
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

            if (!isNaN(id)) {
                redeSelectedItem = redes.find(x => x.id == id);
                console.log(redeSelectedItem);
            } else {
                usuarioSelectedItem = null;
                usuarioCpf.val(null);
                usuarioNome.val(null);
                usuarioSaldo.val(null);
            }
            getUsuarioPontuacoes(usuarioSelectedItem.id, redeSelectedItem.id);
            updateButtonGravarGotas();
        }

        /**
         * webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas.js::updateButtonGravarGotas
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

            var value = quantidadeMultiplicador.val();

            if (redeSelectedItem != null && usuarioSelectedItem != null) {
                quantidadeMultiplicador.prop("disabled", false);
            } else {
                quantidadeMultiplicador.val(null);
                quantidadeMultiplicador.prop("disabled", true);
            }

            if (value !== undefined && value != 0 && redeSelectedItem != null && usuarioSelectedItem != null) {
                gravarGotasBtn.prop("disabled", false);
            }
        }

        // #endregion

        // #region Serviços REST

        /**
         * webroot\js\scripts\pontuacoes_comprovantes\correcao_gotas.js::getRedes
         *
         * Obtem as redes cadastradas e ativas
         *
         * @returns {Select} ListBox
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-09
         */
        function getRedes() {
            $.ajax({
                type: "GET",
                url: "/api/redes/get_redes_list",
                data: {},
                dataType: "JSON",
                success: function (response) {
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
                            redeSelectedItem = rede;
                            redeSelectListBox.val(rede.id);
                            redeSelectListBox.prop("disabled", true);
                            redeSelectListBox.prop("readonly", true);
                        }
                    }
                },
                error: function (response) {
                    var mensagem = response.responseJSON.mensagem;

                    callModalError(mensagem.message, mensagem.errors);
                }
            });
        }

        /**
         * Obtem usuário
         *
         * Obtem usuário através de seu cpf
         *
         * webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas.js::getUsuarioByCPF
         *
         * @param {string} cpf CPF de Usuario
         *
         * @returns {\App\Model\Entity\Usuario} Usuário
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-13
         */
        function getUsuarioByCPF(cpf) {
            $.ajax({
                type: "POST",
                url: "/api/usuarios/get_usuario_by_cpf",
                data: {
                    cpf: cpf
                },
                dataType: "JSON",
                success: function (response) {
                    var data = response.user;
                    console.log(data);
                    usuarioSelectedItem = data;
                    usuarioNome.val(data.nome);
                    getUsuarioPontuacoes(usuarioSelectedItem.id, redeSelectedItem.id);
                    updateButtonGravarGotas();
                },
                error: function (response) {
                    var mensagem = response.responseJSON.mensagem;

                    callModalError(mensagem.message, mensagem.errors);
                }
            });
        }

        /**
         * Obtem pontuações de usuário
         *
         * Obtem pontuações de usuário após selecionar o usuário e a rede
         *
         * webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas.js::getUsuarioPontuacoes
         *
         * @param {*} usuariosId Id de Usuario
         * @param {*} redesId Id da Rede
         *
         * @returns {float} Pontuações
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-13
         */
        function getUsuarioPontuacoes(usuariosId, redesId) {
            if (redeSelectedItem != null && usuarioSelectedItem != null) {
                var data = {
                    usuarios_id: usuariosId,
                    redes_id: redesId
                };
                $.ajax({
                    type: "POST",
                    url: "/api/pontuacoes/get_pontuacoes_rede",
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        var saldo = response.resumo_gotas != undefined ? response.resumo_gotas.saldo : 0;
                        usuarioSaldo.val(saldo);
                    },
                    error: function (response) {
                        var mensagem = response.responseJSON.mensagem;

                        callModalError(mensagem.message, mensagem.errors);
                    }
                });
            }

        }

        /**
         * Grava as gotas do Usuário
         *
         * Realiza a gravação dos dados inseridos pelo operador
         *
         * webroot/js/scripts/pontuacoes_comprovantes/correcao_gotas.js::setGotasManualUsuario
         *
         * @param {Event} evt
         *
         * @returns {void}
         *
         * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
         * @since 2019-10-13
         */
        function setGotasManualUsuario() {
            var data = {
                redes_id: redeSelectedItem.id,
                usuarios_id: usuarioSelectedItem.id,
                quantidade_gotas: quantidadeMultiplicador.val()
            };

            $.ajax({
                type: "POST",
                url: "/api/pontuacoes_comprovantes/set_gotas_manual_usuario",
                data: data,
                dataType: "JSON",
                success: function (response) {
                    callModalSave();
                    // Uma vez gravado, recarrega a tela
                    init();
                },
                error: function (response) {
                    var msg = response.responseJSON.mensagem;

                    callModalError(msg.message, msg.errors);
                }
            });
        }
        // #endregion

        init();
    })
    .ajaxStart(function () {
        callLoaderAnimation(message);
    })
    .ajaxStop(closeLoaderAnimation);
