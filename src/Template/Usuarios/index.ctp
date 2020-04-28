<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
?>
        <?php echo $this->element('../Usuarios/filtro_usuarios', ['controller' => 'usuarios', 'action' => 'index', 'show_filiais' => false, 'filter_redes' => false]); ?>
        <div class="row">
                <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Lista de usuários</h5>
                    </div>
                    <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="usuarios-table">
                            <thead>
                                <tr>
                                    <th>Tipo de perfil</th>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>E-mail</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                            </tbody>
                        
                        </table>
                        
                    </div>

                    </div>
                </div>
            </div>
    </div>
    <?php 
        $this->append('script');
        echo $this->Html->css(sprintf("DataTables/datatables.min.css?version=%s", SYSTEM_VERSION));
        $this->end();
        $this->append('script');
        echo $this->Html->script(sprintf("DataTables/datatables.min.js?version=%s", SYSTEM_VERSION));
        echo $this->Html->script('layout-update/pipeline_wrapper');
        ?>
        <script type="text/javascript">
            var usuarios = 
              {
                init: function()
                  {
                    var self = this;
                    self.initDT();
                    $(document).on('click', "#filtrar_usuarios", self.filtrarUsuarios);
                    return this;
                  },
                initDT: function()
                  {
                     if(typeof window["#usuarios-table"] === 'undefined')
                       {
                        initPipelinedDT("#usuarios-table", 
                        [
                            {className: "text-center"},
                            {className: "text-center"},
                            {className: "text-center"},
                            {className: "text-center"},
                            {className: "text-center", orderable:false}
                        ], 
                        '/app_gotas/usuarios/carregar-usuarios', 
                        undefined, 
                        function(d)
                          {
                            var filtros = $("#filtro_usuarios_form").serialize();
                            d.filtros = filtros;
                            return d;
                          },
                        [ 5, 15, 20, 100 ],
                        undefined,
                        function(row)
                          {
                            var column_key = 4;
                            var column = row[column_key];
                            //Botão de visualizar
                            row[column_key] = '<a href="/app_gotas/usuarios/view/' + column['usuario_id'] + '" class="btn btn-xs btn-default botao-navegacao-tabela" title="Ver detalhes"><i class="fa fa-info-circle"></i> </a> ' + 
                            //Botão de editar operador
                            '<a href="/app_gotas/usuarios/editar-operador/' + column['usuario_id'] + '" class="btn btn-xs btn-primary botao-navegacao-tabela" title="Editar"><i class="fa fa-edit"></i></a>';
                            if(column['botoes_extras'])
                              {
                                if(column['conta_ativa'])
                                  {
                                    row[column_key] += ' <a href="#" class="btn btn-xs  btn-danger btn-confirm" title="Desativar" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente desabilitar o acesso do usuário ' + row[0] + '?" data-action="' + column['url_desativar'] + '"><i class="fa fa-power-off"></i> </a>';
                                  }
                                else
                                  {
                                    row[column_key] += ' <a href="#" class="btn btn-xs  btn-primary btn-confirm" title="Ativar" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente habilitar o acesso do usuário ' + row[0] + '?" data-action="' + column['url_ativar'] + '"><i class="fa fa-power-off"></i></a>';
                                  }
                              }
                            row[column_key] += ' <a href="#" class="btn btn-xs  btn-danger btn-confirm" title="Remover" data-toggle="modal" data-target="#modal-delete-with-message" data-message="Deseja realmente apagar o registro  ' + row[0] + '?" data-action="' + column['url_deletar'] + '"><i class="fa fa-trash"></i></a>';

                            return row;
                          });
                       }
                  },
                filtrarUsuarios: function(e)
                  {
                    e.preventDefault();
                    if(typeof window['#usuarios-table'] !== 'undefined')
                      {
                         window['#usuarios-table'].clearPipeline().draw();
                      }
                  },
              };
            $(document).ready(function()
              {
                usuarios.init();
              })
        </script>
        <?php
        $this->end();
    ?>