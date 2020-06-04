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
        $('#tipoDocumento').click(function() { self.toggleDoc() })
        self.mascararCampos(self.inputHandler);
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
            url: '/api/usuarios/registrar',
            method: 'POST',
            data: self.mountData(),
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
