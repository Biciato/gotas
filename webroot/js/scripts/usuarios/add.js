/**
 * @author Leandro Biciato
 * @file webroot\js\scripts\usuarios\add.js
 * @since 1.2.3
 * @date 2020-06-01
 *
 */
$(document).on('click', '.perfil_option', function(e) {
    if (e.target.value > 0) {
        e.preventDefault();
        $('#redes_select').removeAttr('disabled', false);
        $('#redes_select').empty();
        usuariosAdd.populateRedeSelect()
    } else {
        $('#redes_select').attr('disabled', true);
        $('#redes_select').empty();
        $('#unidades_select').attr('disabled', true);
        $('#unidades_select').empty();
    }
})

$(document).on('click', '.redes_option', function(e) {
    e.preventDefault();
    $('#unidades_select').removeAttr('disabled', false);
    $('#unidades_select').empty();
    usuariosAdd.populateUnidadeSelect(e.target.value)
})

const usuariosAdd = {
    init: function() {
        'use strict';
        var self = this;
        $('#btn-save').click(function() { self.registrar(self) });
        $('#tipoDocumento').click(function() { self.toggleDoc() });
        self.populatePerfilSelect();
        self.mascararCampos(self.inputHandler);
    },
    buildSelect: function(collection, select, className = null) {
        $.each(collection, function(k, value) {
            $(select).append(`<option class="${className}" value="${value.id}">${value.nome}</option>`);
        });
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
                resp.responseJSON.mensagem.errors.forEach((error) => toastr.error(error));
            }
        });
    },
    populatePerfilSelect: function() {
        usuariosService.getPerfisList().then(list => {
            const perfis = Object.keys(list.no_rule).map((id => ({
                id,
                nome: list.no_rule[id]
            })))
            usuariosAdd.buildSelect(perfis, '#tipo_perfil_select', 'perfil_option')
        })
    },
    populateRedeSelect: function() {
        redesService.getList().then(redes =>
            usuariosAdd.buildSelect(redes.map((rede) => ({
                id: rede.id,
                nome: rede.nome_rede
            })), '#redes_select', 'redes_option'))
    },
    populateUnidadeSelect: function(id) {
        redesService.getUnidadesList(id).then(unidades =>
            usuariosAdd.buildSelect(unidades.map((unidade) => ({
                id: unidade.id,
                nome: unidade.nome_fantasia
            })), '#unidades_select', 'unidades_option'))
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
