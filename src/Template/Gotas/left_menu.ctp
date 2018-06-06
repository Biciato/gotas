<?php 

use Cake\Routing\Router;
use Cake\Core\Configure;

$mode = isset($mode) ? $mode : null;
$show_reports_admin_rti = isset($show_reports_admin_rti) ? $show_reports_admin_rti : false;


?>

 <nav class="col-lg-3 col-md-4 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active">
            <?= $this->Html->link(__('Menu'), []) ?>
        </li>

        <li class="active">
            <?= $this->Html->link(__('Ações'), []) ?>
        </li>
        <?php if ($mode == 'view') : ?>
        
            <li>
                <?= $this->Html->link(__('Adicionar Configuração de Gota'), [
                    'action' => 'adicionar_gota',
                    $clientes_id
                ]) ?>
            </li>
            
        <?php endif; ?>     

        <li class="active">
        <?= $this->Html->link(__('Relatórios'), []) ?>
        </li>


        <?= $this->element('../Gotas/atalhos_relatorios_gotas', ['show_reports_admin_rti' => $show_reports_admin_rti]) ?> 

    </ul>
</nav>

