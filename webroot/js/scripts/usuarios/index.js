/**
 * Arquivo de funcionalidades do template src/Templates/Usuarios/index.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-25-5
 */

// Event that fetch user data and show it inside a modal
$(document).on('click', '.detalhes-usuario', function(e) {
    e.preventDefault();
    const id = e.currentTarget.attributes['data-id'].value
    $.get('/api/usuarios/visualizar_usuario', { id }).then((resp) => {
        buildModal(resp)
    });
});

// Sets modal user data
const buildModal = (data) => {
    $('#detalhe-cpf > span').text(data.source.cpf)
    $('#detalhe-nome > span').text(data.source.nome)
    $('#detalhe-tipo_perfil > span').text(data.source.tipo_perfil)
    $('#detalhe-email > span').text(data.source.email)
    $('#detalhe-sexo > span').text(data.source.sexo)
    $('#detalhe-data_nascimento > span').text(data.source.data_nascimento)
    $('#detalhe-telefone > span').text(data.source.telefone)
    $('#detalhe-necessidades_especiais > span').text(data.source.necessidades_especiais)
    $('#detalhe-data_criacao > span').text(data.source.data_criacao)
    $('#detalhe-ultima_atualizacao > span').text(data.source.ultima_atualizacao)
};

// Event that reinit DataTable after search button is pressed
$(document).on('click', '#filtrar_usuarios', (e) => {
    e.preventDefault();
    $('.usuarios-index #data-table').DataTable().destroy();
    usuariosIndex.initDataTable();
});

// Event that removes user
$(document).on('click', '.delete-item', function(e) {
    e.preventDefault();
    $('input[name="confirm_remover"]').val(e.target.attributes['data-id'].value)
});

$(document).on('click', '#confirm_remover', function(e) {
    e.preventDefault();
    usuariosIndex.removerUsuario(e);
})

$(document).on('click', '.change-status', function(e) {
    e.preventDefault();
    const attributes = e.target.attributes;
    usuariosIndex.changeStatus(e, attributes['data-id'].value, attributes['data-active'].value);
})

const usuariosIndex = {
    /**
     * 'Constructor'
     */
    init: function () {
        'use strict';
        var self = this;
        document.title = 'GOTAS - Usuarios';
        self.getRedes();
        self.initDataTable();
        self.mascararCampos(self.inputHandler);

        // Adiciona enter dentro do form, pesquisar
        $(document)
            .off("keydown", "#form")
            .on("keydown", "#form", function (evt) {
                if (evt.keyCode == 13) {
                    evt.preventDefault();
                    self.refreshDataTable(evt);
                    return false;
                }
            });

        $(document)
            .off("click", ".usuarios-index #btn-search")
            .on("click", ".usuarios-index #btn-search", self.refreshDataTable);

        $(document)
            .off("click", ".usuarios-index #data-table .delete-item")
            .on("click", ".usuarios-index #data-table .delete-item", self.deleteNetworkOnClick);
        $(document)
            .off("click", ".usuarios-index #data-table .change-status")
            .on("click", ".usuarios-index #data-table .change-status", self.changeStatusOnClick);

        return self;
    },
    changeStatus: function(e, id, isActive) {
        const condicao = isActive === 'false' ? 1 : 0
        $.ajax({
            url: '/api/usuarios/change-status',
            method: 'POST',
            data: { usuarios_id: id, condicao },
            dataType: 'json',
            success: function(resp) {
                toastr.success(resp.mensagem.message);
                usuariosIndex.refreshDataTable(e);
            },
            error: function(resp) {
                toastr.error(resp.mensagem.message);
            }
        })
    },
    mascararCampos: function(inputHandler) {
        VMasker(document.querySelector('input[name="cpf"]')).maskPattern('999.999.999-99');
    },
    inputHandler: function(masks, max, event) {
        let c = event.target;
        let v = c.value.replace(/\D/g, '');
        const m = c.value.length > max ? 1 : 0;
        VMasker(c).unMask();
        VMasker(c).maskPattern(masks[m]);
        c.value = VMasker.toPattern(v, masks[m]);
    },
    buildFiltroSelect: function(collection, select, className = null) {
        $.each(collection, function(k, value) {
            $(select).append(`<option class="${className}" value="${value.id}">${value.nome}</option>`);
        });
    },
    getRedes: function() {
        $.get('/api/redes/get_redes_list')
            .then((resp) =>
                usuariosIndex.buildFiltroSelect(
                    resp.data.redes.map((item) => ({ nome: item.nome_rede, id: item.id })),
                    '#redes_filtro',
                    'redes_filtro_option'
                )
            )
            .then(() => {
                $('#redes_filtro').change(function(e) {
                    e.preventDefault();
                    $('#unidades_filtro').attr('disabled', true);
                    $('#unidades_filtro').empty();
                    const id = e.target.value;
                    console.log(id)
                    if (id != '0') {
                        usuariosIndex.getClientes(id);
                        $('#unidades_filtro').attr('disabled', false);
                        $('#unidades_filtro').removeAttr('disabled');
                    }
                })
            })
    },
    getClientes: function(id) {
        $.get('/api/clientes', { filtros: { redes_id: id }, draw: '1'})
            .then((resp) =>
                usuariosIndex.buildFiltroSelect(
                    resp.data_table_source.data.map((item) => ({ nome: item.nome_fantasia, id: item.id })),
                    '#unidades_filtro'
                )
            )
    },
    /**
     * Pesquisa dados e popula datatable
     *
     * @returns DataTables
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-22
     */
    initDataTable: function (evt) {
        'use strict';
        let btnHelper = new ButtonHelper();
        let imgHelper = new ImageHelper();
        let columns = [{
                data: "id",
                title: "Id",
                orderable: true,
                visible: false,
            },
            {
                data: "tipo_perfil_convertido",
                width: "10%",
                title: "Tipo Perfil",
                className: "text-center",
                orderable: false,
            },
            {
                data: "nome",
                title: "Nome",
                orderable: true,
            },
            {
                data: "cpf_formatado",
                title: "CPF",
                orderable: false,
            },
            {
                data: "email",
                title: "E-mail",
                orderable: false,
            },
            {
                data: "actions",
                title: "Ações",
                orderable: false,
            }
        ];

        initPipelinedDT(
            ".usuarios-index #data-table",
            columns,
            '/api/usuarios',
            undefined,
            function (d) {
                var filters = {};

                let tipoPerfil = $("#tipo_perfil").val();
                let nome = $("#nome").val();
                let email = $("#email").val();
                let cpf = $("#cpf").val();
                const redesId = $('#redes_filtro').val() === '' ? undefined : $('#redes_filtro').val();
                const clientesId = ['', '0'].includes($('#unidades_filtro').val()) ? undefined : $('#unidades_filtro').val();

                if (tipoPerfil !== undefined && tipoPerfil !== null) {
                    filters.tipo_perfil = tipoPerfil;
                }

                if (nome !== undefined && nome !== null) {
                    filters.nome = nome;
                }

                if (email !== undefined && email !== null) {
                    filters.email = email;
                }

                if (cpf !== undefined && cpf !== null) {
                    filters.cpf = cpf;
                }

                if (redesId !== undefined && redesId !== null) {
                    filters.redes_id = redesId;
                }

                if (clientesId !== undefined && clientesId !== null) {
                    filters.clientes_id = clientesId;
                }

                d.filtros = filters;

                return d;
            },
            [5, 15, 20, 100],
            undefined,
            function (rowData) {
                let attributes = {
                    id: rowData.id,
                    active: rowData.conta_ativa,
                    name: rowData.nome_rede
                };
                if (rowData.nome_img_completo !== undefined && rowData.nome_img_completo !== null) {
                    rowData["nome_img_completo"] = imgHelper.generateDefaultImage(rowData.nome_img_completo, "Logo da Rede", "Logo da Rede", "nome-img-logo").outerHTML;
                }

                let actionView = btnHelper.generateLinkViewToDestination(`#/usuarios/view/${rowData.id}`, btnHelper.ICON_INFO, null, "Ver Detalhes", 'detalhes-usuario', rowData.id);
                let editView = btnHelper.generateLinkEditToDestination(`#/usuarios/edit/${rowData.id}`, null, "Editar");
                let changeStatus = btnHelper.generateImgChangeStatus(attributes, rowData.conta_ativa, undefined, "Ativar/Inativar", "change-status");
                let deleteBtn = btnHelper.genericImgDangerButton(attributes, undefined, "Remover", "delete-item", undefined);

                let buttons = [actionView, editView, deleteBtn, changeStatus];
                let buttonsString = "";

                buttons.forEach(x => {
                    buttonsString += x.outerHTML + " ";
                });

                rowData["actions"] = buttonsString;
                return rowData;
            });
    },
    /**
     * Atualiza tabela de dados
     */
    refreshDataTable: function (evt) {
        'use strict';
        evt.preventDefault();
        if (typeof window['.usuarios-index #data-table'] !== 'undefined') {
            window['.usuarios-index #data-table'].clearPipeline().draw();
        }
    },

    //#endregion
    removerUsuario: function(e) {
        $('.modal-footer > button').hide()
        $('.preloader').show()
        $.ajax({
            url: '/api/usuarios/delete_usuario/' + $('input[name="confirm_remover"]').val(),
            method: 'DELETE',
            success: (resp) => {
                toastr.success(resp.mensagem.message)
                usuariosIndex.refreshDataTable(e)
                $('.modal').modal('hide')
                $('.modal-footer > button').show()
                $('.preloader').hide()
            },
            error: () => {
                toastr.error('Não foi possível remover o usuário nesse momento !!!')
                $('.modal').modal('hide')
                $('.modal-footer > button').show()
                $('.preloader').hide()
            }
        });
    }

};
