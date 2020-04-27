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
    var usuarios = {
        init: function() {
            var self = this;
            self.initDT();
            $(document).on('click', "#filtrar_usuarios", self.filtrarUsuarios);
            return this;
        },
        initDT: function() {
            if (typeof window["#usuarios-table"] === 'undefined') {
                initPipelinedDT("#usuarios-table",
                    [{
                            className: "text-center"
                        },
                        {
                            className: "text-center"
                        },
                        {
                            className: "text-center"
                        },
                        {
                            className: "text-center"
                        },
                        {
                            className: "text-center",
                            orderable: false
                        }
                    ],
                    '/api/usuarios/carregar-usuarios',
                    undefined,
                    function(d) {
                        var filtros = $("#filtro_usuarios_form").serialize();
                        d.filtros = filtros;
                        return d;
                    });
            }
        },
        filtrarUsuarios: function(e) {
            e.preventDefault();
            if (typeof window['#usuarios-table'] !== 'undefined') {
                window['#usuarios-table'].clearPipeline().draw();
            }
        }
    };
    $(document).ready(function() {
        usuarios.init();
    })
</script>
<?php
$this->end();
?>