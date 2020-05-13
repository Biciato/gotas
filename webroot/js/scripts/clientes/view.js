/**
 * Arquivo de funcionalidades do template webroot/view/clientes/view.tpl
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-11
 */

var clientesView = {
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
            .on("keyup", function (event) {
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
            .on("keyup", function (event) {
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
    init: async function (id) {
        let self = this;

        self.configureEvents();

        try {
            let cliente = await clientesService.getById(id);

            self.fillData(cliente);
        } catch (error) {
            console.log(error);
            var msg = {};

            if (error.responseJSON !== undefined) {
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
    }
};
