/**
 * Arquivo de funcionalidades do template src/Templates/Usuarios/index.ctp
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-25-5
 */

// Event that fetch user data and show it inside a modal
$(document).on('click', '.detalhes-usuario', function(e) {
    const id = e.currentTarget.attributes[1].value
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
    const id = e.target.attributes[1].value
    $.ajax({
        url: '/api/usuarios/delete_usuario/' + id,
        method: 'DELETE',
        success: (resp) => {
            toastr.success(resp.mensagem.message)
            usuariosIndex.refreshDataTable(e)
        },
        error: () => {
            toastr.error('Não foi possível remover o usuário nesse momento !!!')
        }
    });
});

// Event that handles search data and fetch it
$(document).on('click', '#redes_filtro > option', function(e) {
    $('#unidades_filtro').attr('disabled', false);
    $('#unidades_filtro').empty();
    const id = e.target.value;
    if (id !== '0') {
        usuariosIndex.getClientes(id);
    } else {
        $('#unidades_filtro').attr('disabled', true);
    }
});

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
    buildFiltroSelect: function(collection, select) {

        $.each(collection, function(k, value) {
            $(select).append('<option value="' + value.id + '">' + value.nome + '</option>');
        });
    },
    getRedes: function() {
        $.get('/api/redes/get_redes_list')
            .then((resp) =>
                usuariosIndex.buildFiltroSelect(
                    resp.data.redes.map((item) => ({ nome: item.nome_rede, id: item.id })),
                    '#redes_filtro'
                )
            )
    },
    getClientes: function(id) {
        $.get('/api/clientes', { filtros: { redes_id: id }})
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
                data: "tipo_perfil",
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
                data: "cpf",
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
                const redesId = ['', '0'].includes($('#redes_filtro').val()) ? undefined : $('#redes_filtro').val();
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
                    active: rowData.ativado,
                    name: rowData.nome_rede
                };

                if (rowData.nome_img_completo !== undefined && rowData.nome_img_completo !== null) {
                    rowData["nome_img_completo"] = imgHelper.generateDefaultImage(rowData.nome_img_completo, "Logo da Rede", "Logo da Rede", "nome-img-logo").outerHTML;
                }

                let actionView = btnHelper.generateLinkViewToDestination(`#/usuarios/view/${rowData.id}`, btnHelper.ICON_CONFIG, null, "Ver Detalhes/Configurar", 'detalhes-usuario', rowData.id);
                let editView = btnHelper.generateLinkEditToDestination(`#/usuarios/edit/${rowData.id}`, null, "Editar");
                let deleteBtn = btnHelper.genericImgDangerButton(attributes, undefined, undefined, "delete-item", undefined);
                let changeStatus = btnHelper.generateImgChangeStatus(attributes, rowData.ativado, undefined, undefined, "change-status");

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

};
