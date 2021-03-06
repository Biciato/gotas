/**
 * Arquivo de funcionalidades do template webroot/view/clientes/edit.tpl
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-12
 */

var clientesEdit = {

    //#region Properties

    id: 0,
    redesId: 0,

    validationOptions: {
        messages: {
            codigo_equipamento_rti: {
                required: "Informe o Código de Equipamento RTI, utilizado para impressão de senhas",
                min: "Valor mínimo 1",
                max: "Valor máximo 999"
            },
            tipo_unidade: {
                required: "Informe se Estabelecimento é Loja ou Posto"
            },
            razao_social: {
                required: "Informe a Razão Social",
                minlength: " Fantasia deve conter pelo menos 3 caracteres"
            },
            cnpj: {
                required: "Informe o CNPJ do Estabelecimento",
                minlength: "Mínimo 14 dígitos"
            },
            endereco: {
                required: "Informe o Endereço",
                minlength: "Mínimo 3 dígitos"
            },
            cep: {
                required: "Informe o CEP",
                minlength: "Mínimo 8 dígitos"
            },
            estado: {
                required: "Informe o Estado do Estabelecimento"
            },
            tel_celular: {
                required: "Informe o Celular de contato no formato (XX)XXXXX-XXXX",
            },
            inicio_turno: {
                required: "Informe o Primeiro Turno do Dia (Formato HH:MM)",
                minlength: "Informe o Primeiro Turno do Dia (Formato HH:MM)",
                min: "Horário Mínimo 00:00",
                max: "Horário Mínimo 23:59",
            }
        },
        rules: {
            codigo_equipamento_rti: {
                required: true,
                min: 1,
                max: 999
            },
            tipo_unidade: {
                required: true
            },
            razao_social: {
                required: true,
                minlength: 3
            },
            cnpj: {
                required: true,
                minlength: 14
            },
            endereco: {
                required: true,
                minlength: 3
            },
            cep: {
                required: true,
                minlength: 8
            },
            estado: "required",
            tel_celular: {
                required: true,
                minlength: 11
            }

        },
    },

    //#endregion

    //#region Functions

    /**
     * Realiza configuração de eventos dos campos da tela
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-12
     */
    configureEvents: function () {
        'use strict';
        var self = this;

        $(document).find("#clientes-edit-form #codigo-equipamento-rti").mask("999");
        $(document).find("#clientes-edit-form #codigo-equipamento-rti").focus();
        $(document)
            .off("blur", "#codigo-equipamento-rti")
            .on('blur', "#codigo-equipamento-rti", function () {
                this.value = this.value.padStart(3, '0');
            });

        $(document).find("#cnpj").mask('99.999.999/9999-99');
        $(document).find("#cep").mask("99.999-999");

        $(document)
            .off("blur", "#clientes-edit-form #tel-fixo")
            .off("keydown", "#clientes-edit-form #tel-fixo")
            .on("blur", "#clientes-edit-form #tel-fixo", function () {
                this.value = clientesView.setTelephoneFormat(this.value, 10);
            })
            .on("keyup", "#clientes-edit-form #tel-fixo", function (event) {
                let value = event.target.value;

                if (value !== undefined && value !== null) {
                    event.target.value = event.target.value.replace(/\D/g, "");
                }
            });
        $(document)
            .off("blur", "#clientes-edit-form #tel-celular")
            .off("keydown", "#clientes-edit-form #tel-celular")
            .on("blur", "#clientes-edit-form #tel-celular", function () {
                this.value = clientesView.setTelephoneFormat(this.value, 11);
            })
            .on("keyup", "#clientes-edit-form #tel-celular", function (event) {
                let value = event.target.value;

                if (value !== undefined && value !== null) {
                    event.target.value = event.target.value.replace(/\D/g, "");
                }
            });
        $(document)
            .off("blur", "#clientes-edit-form #tel-fax")
            .off("keydown", "#clientes-edit-form #tel-fax")
            .on("blur", "#clientes-edit-form #tel-fax", function () {
                this.value = clientesView.setTelephoneFormat(this.value, 10);
            })
            .on("keyup", "#clientes-edit-form #tel-fax", function (event) {
                let value = event.target.value;

                if (value !== undefined && value !== null) {
                    event.target.value = event.target.value.replace(/\D/g, "");
                }
            });

        $(document)
            .off("change", "#clientes-edit-form #qte-turnos")
            .on("change", "#clientes-edit-form #qte-turnos", function (event) {
                let self = clientesEdit;

                self.fillTimeBoards();
            });
        $(document)
            .off("blur", "#clientes-edit-form #turno")
            .off("keydown", "#clientes-edit-form #turno")
            .off("keyup", "#clientes-edit-form #turno")
            .on("blur", "#clientes-edit-form #turno", function () {
                let self = clientesEdit;

                let value = this.value;
                value = value.replace(/^(\d{2})(\d{2})/g, "$1:$2");

                if (value > 2359) value = 2359;

                this.value = value;

                self.fillTimeBoards();
            })
            .on("keydown", "#clientes-edit-form #turno", function (event) {
                let value = this.value;
                if (event.keyCode == 13)
                    value = value.replace(/(\d{2})(\d{2})/, "$1:$2");

                this.value = value;
            })
            .on("keyup", "#clientes-edit-form #turno", function (event) {
                if (event.keyCode !== 13) {
                    let value = this.value;
                    value = value.replace(/\D/g, "").substring(0, 4);

                    if (value > 2359) value = 2359;

                    this.value = value;
                }
            });

        $(document)
            .off("click", "#clientes-edit-form #btn-save")
            .on("click", "#clientes-edit-form #btn-save", self.formSubmit)
            .off("keyup", "#clientes-edit-form")
            .on("keyup", "#clientes-edit-form", function (evt) {
                if (evt.keyCode == 13) {
                    $(this).trigger('click');
                }
            });

        return self;
    },
    /**
     * 'Construtor'
     *
     * @param {Integer} id Id do Estabelecimento
     * @param {Integer} redesId Id da Rede
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-12
     */
    init: async function (id, redesId = undefined) {
        let self = this;

        self.id = id;
        self.redesId = redesId;
        console.log("Rede :redesId Cliente :id".replace(":redesId", redesId).replace(":id", id));
        let title = "GOTAS - Editar Estabelecimento :establishment";


        try {
            let cliente = await clientesService.getById(self.id);
            document.title = title.replace(":establishment", cliente.nome_fantasia_municipio_estado);

            self.fillData(cliente);
        } catch (error) {
            console.log(error);
            var msg = {};

            if (error.responseJSON.code !== undefined) {
                toastr.error(error.responseJSON.message);

                return false;
            } else if (error.responseJSON !== undefined && error.responseJSON.mensagem !== undefined) {
                toastr.error(error.responseJSON.mensagem.errors.join(" "), error.responseJSON.mensagem.message);
                return false;
            } else if (error.responseText !== undefined) {
                msg = error.responseText;
            } else {
                msg = error;
            }

            toastr.error(msg);
            return false;
        }

        self.configureEvents();

        return self;
    },
    /**
     * Preenche/Limpa os dados do Formulário
     *
     * @param {Cliente} data Object Cliente
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-12
     */
    fillData: function (data) {
        let self = this;
        document.title = "GOTAS - Estabelecimento ";
        console.log(data);

        if (data === undefined || data === null || data.id === undefined) {
            // Se informação vazia, limpa todos os campos

            $("span[id=nome-fantasia-municipio-estado]").text(null);
            $("#codigo-equipamento-rti").val(null);
            $("#tipo-unidade").val();
            $("#nome-fantasia").val();
            $("#razao-social").val();
            $("#cnpj").val(null);
            $("#endereco").val(null);
            $("#endereco_numero").val(null);
            $("#endereco_complemento").val(null);
            $("#cep").val(null);
            $("#bairro").val(null);
            $("#municipio").val(null);
            $("#estado").val(null);
            $("#pais").val(null);
            $("#latitude").val(null);
            $("#longitude").val(null);
            $("#tel-fixo").val(null);
            $("#tel-fax").val(null);
            $("#tel-celular").val(null);
            $("#impressao_sw_linha_continua").val(null);
            $("#delimitador-nota-impressao").val(null);
            $("#delimitador-nota-produtos-inicial").val(null);
            $("#delimitador-nota-produtos-final").val(null);
            $("#quadro-horarios").empty();
        } else {
            $("span[id=nome-fantasia-municipio-estado]").text(data.nome_fantasia_municipio_estado);
            $("#codigo-equipamento-rti").val(data.codigo_equipamento_rti);
            $("#tipo-unidade").val(data.tipo_unidade);
            $("#nome-fantasia").val(data.nome_fantasia);
            $("#razao-social").val(data.razao_social);
            $("#cnpj").val(data.cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, "$1.$2.$3/$4-$5"));
            $("#endereco").val(data.endereco);
            $("#endereco_numero").val(data.endereco_numero);
            $("#endereco_complemento").val(data.endereco_complemento);
            $("#cep").val(data.cep.replace(/(\d{2})(\d{3})(\d{3})/g, "$1.$2-$3"));
            $("#bairro").val(data.bairro);
            $("#municipio").val(data.municipio);
            $("#estado").val(data.estado);
            $("#pais").val(data.pais);
            $("#latitude").val(data.latitude);
            $("#longitude").val(data.longitude);
            $("#tel-fixo").val(self.setTelephoneFormat(data.tel_fixo, 10));
            $("#tel-fax").val(self.setTelephoneFormat(data.tel_fax, 10));
            $("#tel-celular").val(self.setTelephoneFormat(data.tel_celular, 11));
            $("#impressao_sw_linha_continua").val(data.impressao_sw_linha_continua ? 1 : 0);
            $("#delimitador-nota-impressao").val(data.delimitador_nota_impressao);
            $("#delimitador-nota-produtos-inicial").val(data.delimitador_nota_produtos_inicial);
            $("#delimitador-nota-produtos-final").val(data.delimitador_nota_produtos_final);
            $("#delimitador-qr-code").val(data.delimitador_qr_code);

            if ((data.clientes_has_quadro_horarios !== undefined && data.clientes_has_quadro_horarios !== null) && data.clientes_has_quadro_horarios.length > 0) {

                let horario = data.clientes_has_quadro_horarios[0].horario;
                horario = moment(horario, "YYYY-MM-DD HH:mm:ss").format("HH:mm");
                $("#turno").val(horario);

                self.fillTimeBoards();
            }
        }
    },
    /**
     * Preenche a region que demonstra os horários de trabalho
     *
     * @returns void
     */
    fillTimeBoards: function () {
        var horas = $("#turno").val().match(/(\d{2})/gm);

        if (horas != undefined && horas.length > 0) {

            var hora = parseInt(horas[0]);
            var minuto = parseInt(horas[1]);
            var qteTurnos = $("#qte-turnos").val();
            var divisao = 24 / qteTurnos;
            var turnos = [];
            var horaTemp = hora;

            for (let i = 0; i < qteTurnos; i++) {
                var turno = {};
                turno.id = i;
                turno.hora = horaTemp.toString().length == 1 ? "0" + horaTemp : horaTemp;
                turno.minuto = minuto.toString().length == 1 ? "0" + minuto : minuto;
                var horaTurno = horaTemp + divisao;
                if (horaTurno > 23) {
                    horaTurno = horaTurno - 24;
                }

                turno.proximaHora = horaTurno.toString().length == 1 ? "0" + horaTurno : horaTurno;
                turno.proximaMinuto = minuto.toString().length == 1 ? "0" + minuto : minuto;
                horaTemp = horaTurno;

                turnos.push(turno);
            }

            $("#quadro-horarios").empty();
            let count = 0;
            $.each(turnos, function (index, value) {
                let horaAtual = value.hora + ":" + value.minuto;
                let horaProxima = value.proximaHora + ":" + value.proximaMinuto;
                let html = `
                <div class="form-group row">
                    <label class="col-lg-2">Turno ${count + 1}</label>
                    <div class="col-lg-10">
                        <input type="text"
                            class="form-control" readonly disabled value="${horaAtual} até ${horaProxima}" />
                    </div>
                </div>
                `;
                $("#quadro-horarios").append(html);
                count++;
            });
        }
    },

    /**
     * Dispara submit ao clicar no botão de salvar do form
     * @param {*} evt
     */
    formSubmit: function (evt, self) {
        'use strict';
        evt.preventDefault();

        // use somente para testes
        // clientesEdit.save($("#clientes-edit-form"));
        // return;

        if (clientesEdit.validateForm("#clientes-edit-form").form()) {
            clientesEdit.save($("#clientes-edit-form"));
        } else {
            toastr.error("Há erros no formulário. Corrija-os antes de continuar!")
        }

        return self;
    },
    /**
     * Trata os dados antes de submeter ao salvar
     *
     * @param {FormElement} FormElement
     *
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-06
     */
    save: async function (form) {
        let self = this;
        // serializa o form e remove espaços em branco, transforma em array
        let objToTreat = JSON.parse(JSON.stringify($(form).serializeArray()));
        let objPost = {};

        /**
         * Todos os elementos da tela que não precisam de tratamento, são convertidos em objeto
         * Caso alguma das propriedades precise de um tratamento adicional, faça após o foreach
         */
        objToTreat.forEach(item => {
            objPost[item.name] = item.value;
        });

        // Remove formatação de campos numéricos
        objPost.cnpj = objPost.cnpj.replace(/\D/gm, "");
        objPost.cep = objPost.cep.replace(/\D/gi, "");
        objPost.tel_fax = objPost.tel_fax.replace(/\D/gi, "");
        objPost.tel_fixo = objPost.tel_fixo.replace(/\D/gi, "");
        objPost.tel_celular = objPost.tel_celular.replace(/\D/gi, "");
        objPost.redes_id = self.redesId;
        objPost.id = self.id;

        console.log(objPost);
        try {
            let response = await clientesService.save(objPost);

            if (response === undefined || response === null || !response) {
                toastr.error(response.mensagem.message);
                return false;
            }

            // Gravação feita com sucesso, redireciona
            toastr.success(response.mensagem.message);
            window.location = `#/redes/view/${self.redesId}`;
        } catch (error) {
            console.log(error);
            var msg = {};

            if (error.responseJSON.code !== undefined) {
                toastr.error(error.responseJSON.message);

                return false;
            } else if (error.responseJSON !== undefined && error.responseJSON.mensagem !== undefined) {
                toastr.error(error.responseJSON.mensagem.errors.join(" "), error.responseJSON.mensagem.message);
                return false;
            } else if (error.responseText !== undefined) {
                msg = error.responseText;
            } else {
                msg = error;
            }

            toastr.error(msg);
            return false;
        }

        return self;
    },

    /**
     * Define formatação de telefone
     * @param {String} value Telefone
     * @param {Integer} size Tamanho
     */
    setTelephoneFormat: function (value, size) {
        let format = /^(\d{2})(\d{4})(\d{4})/g;

        if (size == 11) {
            format = /^(\d{2})(\d{5})(\d{4})/g;
        }

        value = value.replace(/\D/g, "");
        value = value.substring(0, size);

        return value.replace(format, "($1)$2-$3")
    },
    /**
     * Realiza validação em um formulário selecionado
     * @param {Form} form Formulário alvo
     * @returns jQuery.Validation Validação em um Formulário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-12
     */
    validateForm: function (form) {
        var self = this;
        return $(form).validate({
            rules: self.validationOptions.rules,
            messages: self.validationOptions.messages,
            // Utilize a opção a seguir para validar as abas ocultas
            ignore: "tab-pane"
        });
    }

    //#endregion
};
