/**
 * @author Leandro Biciato
 * @file webroot\js\scripts\usuarios\add.js
 * @since 1.2.3
 * @date 2020-06-01
 *
 */

const usuariosAdd = {
    init: function() {
        'use strict';
        var self = this;
        $('#btn-save').click(function() { self.registrar(self) });
        $('#tipoDocumento').click(function() { self.toggleDoc() });
        self.mascararCampos(self.inputHandler);
        self.getTipoPerfis();
    },
    mountData: function() {
        return [
            'cpf',
            'doc_estrangeiro',
            'tipo_perfil',
            'telefone',
            'email',
            'senha',
            'confirm_senha',
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
        ].reduce((obj, item) => ({
            ...obj,
            [item]: $(`input[name="${item}"]`).val()
        }), {});
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
    registrar: function(self) {
        $.ajax({
            url: '/api/usuarios/adicionar_operador/' + $('#redes_select').val(),
            method: 'POST',
            data: {
                ...self.mountData(),
                tipo_perfil: $('#tipo_perfil_select').val(),
                clientes_id: $('#unidades_select').val(),
                redes_id: $('#redes_select').val()
            },
            dataType: 'JSON',
            success: (resp) => {
                if (!resp.mensagem.status) {
                    toastr.error(resp.mensagem.message)
                } else {
                    toastr.success('Usuário cadastrado com sucesso !!!')
                    $(':input').val('').removeAttr('checked').removeAttr('selected');
                }
            },
            error: (resp) => {
                if (resp.responseJSON.mensagem && Array.isArray(resp.responseJSON.mensagem.errors)) {
                    resp.responseJSON.mensagem.errors.forEach((error) => toastr.error(error));
                }
                if (resp.responseJSON &&
                    resp.responseJSON.mensagem &&
                    resp.responseJSON.mensagem.errors &&
                    resp.responseJSON.mensagem.errors.errors) {
                    if (resp.responseJSON.mensagem.errors.errors.confirm_senha &&
                        resp.responseJSON.mensagem.errors.errors.confirm_senha._empty) {
                        toastr.error(resp.responseJSON.mensagem.errors.errors.confirm_senha._empty)
                    }
                    if (resp.responseJSON.mensagem.errors.errors.email &&
                        resp.responseJSON.mensagem.errors.errors.email.unique) {
                        toastr.error(resp.responseJSON.mensagem.errors.errors.email.unique)
                    }
                    if (resp.responseJSON.mensagem.errors.errors.nome &&
                        resp.responseJSON.mensagem.errors.errors.nome._empty) {
                        toastr.error(resp.responseJSON.mensagem.errors.errors.nome._empty)
                    }
                    if (resp.responseJSON.mensagem.errors.errors.telefone &&
                        resp.responseJSON.mensagem.errors.errors.telefone._empty) {
                        toastr.error(resp.responseJSON.mensagem.errors.errors.telefone._empty)
                    }
                } else {
                    toastr.error(resp)
                }
            }
        });
    },
    buildFiltroSelect: function(collection, select, className = null) {
        $.each(collection, function(k, value) {
            $(select).append(`<option class="${className}" value="${value.id}">${value.nome}</option>`);
        });
    },
    getTipoPerfis: function() {
        usuariosService.getPerfisList()
            .then((resp) => {
                const perfis = Object.keys(resp.insert).map((key) => ({
                    id: key,
                    nome: resp.insert[key]
                }));
                usuariosAdd.buildFiltroSelect(perfis, tipo_perfil_select);
                if (resp.insert[1] === 'Administrador da Rede') {
                    $('#redes_select').attr('disabled', false);
                    usuariosAdd.getRedes();
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
                        usuariosAdd.getRedes();
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
                usuariosAdd.buildFiltroSelect(
                    list.map((rede) => ({ id: rede.id, nome: rede.nome_rede })),
                    '#redes_select',
                    'redes_filtro_option'
                )
                if ($('#tipo_perfil_select').val() > 2) {
                    $('#unidades_select').attr('disabled', false);
                    $('#unidades_select').empty();
                    usuariosAdd.getClientes(list[0].id);
                }
            })
            .then(() => {
                $('#redes_select').change(function(e) {
                    e.preventDefault();
                    const id = e.target.value;
                    if (id != '0' && $('#tipo_perfil_select').val() > 2) {
                        $('#unidades_select').empty();
                        usuariosAdd.getClientes(id);
                    }
                })
            })
    },
    getClientes: function(id) {
        $.get('/api/clientes', { filtros: { redes_id: id }, draw: '1'})
            .then((resp) =>
                usuariosAdd.buildFiltroSelect(
                    resp.data_table_source.data.map((item) => ({ nome: item.nome_fantasia, id: item.id })),
                    '#unidades_select'
                )
            )
    },
    // Toggles cpf/doc estrangeiro input based on checkbox
    toggleDoc: function() {
        if ($('#tipoDocumento').is(':checked')) {
            $('#cpf_box').hide()
            $('#doc_estrangeiro_box').fadeIn()
        } else {
            $('#doc_estrangeiro_box').hide()
            $('#cpf_box').fadeIn()
        }
    }
}
