/**
 * Arquivo de funcionalidades do template webroot/view/clientes/add.tpl
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-12
 */

var clientesAdd = {

    //#region Properties
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

        $(document).find("#codigo_equipamento_rti").mask("999");
        $(document)
            .off("blur", "#codigo_equipamento_rti")
            .on('blur', "#codigo_equipamento_rti", function () {
                var valueCheck = this.value;

                while (valueCheck.length < 3) {
                    valueCheck = "0" + valueCheck;
                }
                this.value = valueCheck;
            });

        $(document).find("#cnpj").mask('99.999.999/9999-99');
        $(document).find("#cep").mask("99.999-999");

        $(document)
            .off("blur", "#form #tel-fixo")
            .off("keydown", "#form #tel-fixo")
            .on("blur", "#form #tel-fixo", function () {
                this.value = clientesView.setTelephoneFormat(this.value, 10);
            })
            .on("keyup", "#form #tel-fixo", function (event) {
                let value = event.target.value;

                if (value !== undefined && value !== null) {
                    event.target.value = event.target.value.replace(/\D/g, "");
                }
            });
        $(document)
            .off("blur", "#form #tel-celular")
            .off("keydown", "#form #tel-celular")
            .on("blur", "#form #tel-celular", function () {
                this.value = clientesView.setTelephoneFormat(this.value, 11);
            })
            .on("keyup", "#form #tel-celular", function (event) {
                let value = event.target.value;

                if (value !== undefined && value !== null) {
                    event.target.value = event.target.value.replace(/\D/g, "");
                }
            });
        $("#tel-fax")
            .off("blur", "#form #tel-fax")
            .off("keydown", "#form #tel-fax")
            .on("blur", "#form #tel-fax", function () {
                this.value = clientesView.setTelephoneFormat(this.value, 10);
            })
            .on("keyup", function (event) {
                let value = event.target.value;

                if (value !== undefined && value !== null) {
                    event.target.value = event.target.value.replace(/\D/g, "");
                }
            });

        $(document)
            .off("click", "#form #btn-save")
            .on("click", "#form #btn-save", self.formSubmit)
            .off("keyup", "#form")
            .on("keyup", "#form", function (evt) {
                if (evt.keycode == 13) {
                    this.formSubmit();
                }
            });

        return self;
    },
    /**
     * 'Construtor'
     *
     * @param {Integer} id Id do estabelecimento
     * @returns void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-12
     */
    init: async function () {
        let self = this;

        document.title = "GOTAS - Novo Estabelecimento";
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

            let quadroHorarios = [];
            let count = 0;

            // Cria a lista
            data.clientes_has_quadro_horarios.forEach(item => {
                let horario = {
                    id: count,
                    text: "Turno " + (count + 1),
                    time: moment(item.horario, "YYYY-MM-DD HH:mm:ss").format("HH:mm")
                };

                quadroHorarios.push(horario);
                count++;
            });

            $("#quadro_horarios").empty();

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

            let quadroHorarios = [];
            let count = 0;

            // Cria a lista
            data.clientes_has_quadro_horarios.forEach(item => {
                let horario = {
                    id: count,
                    text: "Turno " + (count + 1),
                    time: moment(item.horario, "YYYY-MM-DD HH:mm:ss").format("HH:mm")
                };

                quadroHorarios.push(horario);
                count++;
            });

            $("#quadro_horarios").empty();

            // Adiciona os itens na tela
            quadroHorarios.forEach(horario => {
                let html = `
                <div class="form-group row">
                    <label for="quadro_horario_${horario.id}" class="col-lg-2">${horario.text}</label>
                    <div class="col-lg-10">
                        <input type="text" name="quadro_horario_${horario.id}" id="quadro-horario-${horario.id}"
                            class="form-control" readonly disabled value="${horario.time}" />
                    </div>
                </div>
                `;

                if (horario.id < (quadroHorarios.length - 1))
                    html += `<div class="hr-line-dashed"></div>`;

                $("#quadro_horarios").append(html);
            });
        }
    },

    /**
     * Dispara submit ao clicar no botão de salvar do form
     * @param {*} evt
     */
    formSubmit: function (evt) {
        'use strict';
        let self = this;
        evt.preventDefault();

        if (clientesAdd.validateForm("#form").form()) {
            clientesAdd.save($("#form"));
        } else {
            toastr.error("Há erros no formulário. Corrija-os antes de continuar!")
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

    // fillTimeBoards: function () {
    //     var horas = $("#horario").val().match(/(\d{2})/gm);

    //     if (horas != undefined && horas.length > 0) {

    //         var hora = parseInt(horas[0]);
    //         var minuto = parseInt(horas[1]);

    //         var qteTurnos = $("#quantidade_turnos").val();

    //         var divisao = 24 / qteTurnos;
    //         var turnos = [];

    //         var horaTemp = hora;

    //         for (let i = 0; i < qteTurnos; i++) {

    //             var turno = {};

    //             turno.id = i;
    //             turno.hora = horaTemp.toString().length == 1 ? "0" + horaTemp : horaTemp;
    //             turno.minuto = minuto.toString().length == 1 ? "0" + minuto : minuto;
    //             var horaTurno = horaTemp + divisao;
    //             if (horaTurno > 23) {
    //                 horaTurno = horaTurno - 24;
    //             }

    //             turno.proximaHora = horaTurno.toString().length == 1 ? "0" + horaTurno : horaTurno;
    //             turno.proximaMinuto = minuto.toString().length == 1 ? "0" + minuto : minuto;

    //             horaTemp = horaTurno;

    //             turnos.push(turno);
    //         }

    //         $(".horariosContent").empty();
    //         $.each(turnos, function (index, value) {
    //             $(".horariosContent").append("<strong>Turno " + (value.id + 1) + ": </strong> " + value.hora + ":" + value.minuto + " até " + value.proximaHora + ":" + value.proximaMinuto + ".<br />");
    //         });
    //     }
    // },
};

// $(document).ready(function () {

//     initializeTimePicker("horario");
//     initializeDatePicker("data_nasc");



//     $("#quantidade_turnos").on("change", function (ev) {
//         var max = $("#quantidade_turnos").attr('max');

//         if (this.value > max) {
//             this.value = max;
//         }

//         preencheQuadroHorarios();
//     });

//     preencheQuadroHorarios();


//     $("#horario").on("blur", function (ev) {
//         preencheQuadroHorarios();
//     });

//     // Dispara atualização de quantidade de turnos se já tiver preenchido
//     $("#quantidade_turnos").blur();
// });
