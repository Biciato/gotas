/**
 * Arquivo de funcionalidades do template webroot\view\gotas\index.tpl
 *
 * @author Leandro Biciato <leandro@aigen.com.br>
 * @since 1.2.3
 * @date 2020-03-07
 **/


// Sets modal user data
/* const buildModal = (data) => {
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
}; */

// Event that reinit DataTable after search button is pressed
/* $(document).on('click', '#filtrar_usuarios', (e) => {
    e.preventDefault();
    $('.usuarios-index #data-table').DataTable().destroy();
    gotasIndex.initDataTable();
});

// Event that removes user
$(document).on('click', '.delete-item', function(e) {
    e.preventDefault();
    $('input[name="confirm_remover"]').val(e.target.attributes['data-id'].value)
});

$(document).on('click', '#confirm_remover', function(e) {
    e.preventDefault();
    gotasIndex.removerUsuario(e);
})

$(document).on('click', '.change-status', function(e) {
    e.preventDefault();
    const attributes = e.target.attributes;
    gotasIndex.changeStatus(e, attributes['data-id'].value, attributes['data-active'].value);
}) */

const gotasIndex = {
    /**
     * 'Constructor'
     */
    init: function () {
        'use strict';
        var self = this;
        document.title = 'GOTAS - Gotas';
        self.initDataTable();
        self.getRedes();

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
                gotasIndex.refreshDataTable(e);
            },
            error: function(resp) {
                toastr.error(resp.mensagem.message);
            }
        })
    },
    buildFiltroSelect: function(collection, select, className = null) {
        $.each(collection, function(k, value) {
            $(select).append(`<option class="${className}" value="${value.id}">${value.nome}</option>`);
        });
    },
    getRedes: function() {
        redesService.getList().then((list) => gotasIndex.getClientes(list[0]['id']))
    },
    getClientes: function(id) {
        $.get('/api/clientes', { filtros: { redes_id: id }, draw: '1'})
            .then((resp) =>
                gotasIndex.buildFiltroSelect(
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
                data: "nome_parametro",
                width: "10%",
                title: "Nome da Gota",
                className: "text-center",
                orderable: false,
            },
            {
                data: "multiplicador_gota",
                title: "Valor multiplicador",
                orderable: true,
            },
            {
                data: "cliente",
                title: "Ponto de Atendimento ",
                orderable: false,
            },
            {
                data: "habilitado",
                title: "Status",
                orderable: false,
            },
            {
                data: "actions",
                title: "Ações",
                orderable: false,
            }
        ];

        initPipelinedDT(
            ".gotas-index #data-table",
            columns,
            '/api/gotas',
            undefined,
            function (d) {
                var filters = {};

                let tipoPerfil = $("#tipo_perfil_select").val();
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

                let actionView = btnHelper.generateLinkViewToDestination(`#/gotas/view/${rowData.id}`, btnHelper.ICON_INFO, null, "Ver Detalhes", 'detalhes-gota', rowData.id);
                let editView = btnHelper.generateLinkEditToDestination(`#/gotas/edit/${rowData.id}`, null, "Editar");
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
                gotasIndex.refreshDataTable(e)
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
