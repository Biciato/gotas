/**
 * Arquivo de funcionalidades do template src/Templates/Usuarios/edit.ctp
 *
 * @author Leandro Biciato
 * @file webroot\js\scripts\usuarios\edit.js
 * @since 1.2.3
 * @date 2020-06-01
 *
 */

 // Input field names
const fields = [
    'id',
    'cpf',
    'doc_estrangeiro',
    'tipo_perfil',
    'telefone',
    'email',
    'senha',
    'necessidades_especiais',
    'data_nasc',
    'nome',
    'sexo',
    'cep',
    'endereco',
    'endereco_numero',
    'endereco_complemento',
    'bairro',
    'municipio',
    'cidade',
    'estado',
    'pais'
];

const usuariosEdit = {
    init: function() {
        'use strict';
        var self = this;
        self.getUser(self);
        $('#btn-save').click(self.save)
        self.mascararCampos(self.inputHandler);
        self.getTipoPerfis();
    },
    buildFiltroSelect: function(collection, select, className = null) {
        $.each(collection, function(k, value) {
            $(select).append(`<option class="${className}" value="${value.id}">${value.nome}</option>`);
        });
    },
    getUser: function() {
        $.ajax({
            url: '/api/usuarios/get_usuario_by_id',
            method: 'POST',
            data: { id: window.location.href.split('/')[6] },
            success: function(resp) { usuariosEdit.fillInputs(JSON.parse(resp)['msg']) },
            error: function(resp) { toastr.error(resp.mensagem.message) }
        })
    },
    fillInputs: function(user) {
        fields.forEach((item) => {
            if (item === 'cpf') {
                $(`input[name="${item}"]`).val(user['cpf_formatado'])
            } else if (item === "necessidades_especiais") {
                $(`#${item}`).val(user[item] ? 1 : 0)
            } else if (item === "sexo") {
                $(`#${item}`).val(user[item])
            } else if (item === "data_nasc") {
                const date = new Date(user[item])
                $(`input[name="${item}"`).val(date.toLocaleDateString())
            } else {
                $(`input[name="${item}"]`).val(user[item])
            }
        });
    },
    mascararCampos: function(inputHandler) {
        const telMask = ['(99) 9999-99999', '(99) 99999-9999'];
        const tel = document.querySelector('input[name="telefone"]');
        VMasker(tel).maskPattern(telMask[0]);
        tel.addEventListener('input', inputHandler.bind(undefined, telMask, 14), false);
        VMasker(document.querySelector('input[name="cpf"]')).maskPattern('999.999.999-99');
        VMasker(document.querySelector('input[name="data_nasc"]')).maskPattern('99/99/9999');
    },
    inputHandler: function(masks, max, event) {
        let c = event.target;
        let v = c.value.replace(/\D/g, '');
        const m = c.value.length > max ? 1 : 0;
        VMasker(c).unMask();
        VMasker(c).maskPattern(masks[m]);
        c.value = VMasker.toPattern(v, masks[m]);
    },
    getTipoPerfis: function() {
        usuariosService.getPerfisList()
            .then((resp) => {
                const perfis = Object.keys(resp.insert).map((key) => ({
                    id: key,
                    nome: resp.insert[key]
                }));
                usuariosEdit.buildFiltroSelect(perfis, tipo_perfil_select);
                if (resp.insert[1] === 'Administrador da Rede') {
                    $('#redes_select').attr('disabled', false);
                    usuariosEdit.getRedes();
                }
            })
            .then(() => {
                $('#tipo_perfil_select').change(function(e) {
                    if (e.target.value > 0) {
                        e.preventDefault();
                        $('#redes_select').removeAttr('disabled', false);
                        $('#redes_select').empty();
                        if (['1','2'].includes(e.target.value)) {
                            $('#unidades_select').attr('disabled', true);
                            $('#unidades_select').empty();
                        }
                        usuariosEdit.getRedes();
                    } else {
                        $('#redes_select').attr('disabled', true);
                        $('#redes_select').empty();
                        $('#unidades_select').attr('disabled', true);
                        $('#unidades_select').empty();
                    }
                })
            })
    },
    getRedes: function() {
        redesService.getList()
            .then((list) => {
                usuariosEdit.buildFiltroSelect(
                    list.map((rede) => ({ id: rede.id, nome: rede.nome_rede })),
                    '#redes_select',
                    'redes_filtro_option'
                )
                if ($('#tipo_perfil_select').val() > 2) {
                    $('#unidades_select').attr('disabled', false);
                    $('#unidades_select').empty();
                    usuariosEdit.getClientes(list[0].id);
                }
            })
            .then(() => {
                $('#redes_select').change(function(e) {
                    e.preventDefault();
                    const id = e.target.value;
                    if (id != '0' && $('#tipo_perfil_select').val() > 2) {
                        $('#unidades_select').empty();
                        usuariosEdit.getClientes(id);
                    }
                })
            })
    },
    getClientes: function(id) {
        $.get('/api/clientes', { filtros: { redes_id: id }, draw: '1'})
            .then((resp) =>
                usuariosEdit.buildFiltroSelect(
                    resp.data_table_source.data.map((item) => ({ nome: item.nome_fantasia, id: item.id })),
                    '#unidades_select'
                )
            )
    },
    save: function() {
        $.ajax({
            url: '/api/usuarios/set_perfil',
            method: 'POST',
            dataType: 'json',
            data: usuariosEdit.mountData(),
            success: (resp) => {
                if (resp.mensagem.message === 'EMAIL inválido!') {
                    toastr.error(resp.mensagem.message)
                }
                if (resp.mensagem.errors) {
                    if (resp.mensagem.errors.data_nasc) {
                        toastr.error('Por favor, insira uma data de nascimento')
                    } else {
                        toastr.success(resp.mensagem.message)
                        setTimeout(() => window.location.href = '#/usuarios/index', 2000)
                    }
                }
            },
            error: (resp) => resp.responseJSON.mensagem.errors.forEach((error) => toastr.error(error))
        })
    },
    mountData: function() {
        return fields.reduce((obj, item) => ({
            ...obj,
            [item]: item === 'sexo' ? $('#sexo').val() : $(`input[name="${item}"]`).val()
        }), {});
    }
}
