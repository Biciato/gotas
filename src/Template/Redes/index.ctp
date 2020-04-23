<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rede[]|\Cake\Collection\CollectionInterface $redes
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

$extensionDebug = Configure::read("debug") ? "" : ".min";
// $this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

// $this->Breadcrumbs->add('Redes', [], ['class' => 'active']);

// echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);
?>

<div class="content">
    <div class="form-group">
        <div class="row redes-index">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= __('Redes') ?></h5>
                    </div>
                    <div class="ibox-content">
                        <div class="panel-group">
                            <div class="panel panel-default">
                                <div class="panel-heading panel-heading-sm text-center" data-toggle="collapse" href="#collapse1" data-target="#filter-coupons">
                                    <div>
                                        <span class="fa fa-search"></span>
                                        Exibir / Ocultar Filtros
                                    </div>
                                </div>
                                <div id="filter-coupons" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <form id="form">

                                            <div class="form-group row">
                                                <div class="col-lg-4">
                                                    <label for="nome_rede">Nome:</label>
                                                    <input type="text" name="nome_rede" id="nome-rede" class="form-control">
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="ativado">Ativado:</label>
                                                    <select name="ativado" id="ativado" class="form-control">
                                                        <option value="">Todos</option>
                                                        <option value="1" selected>Sim</option>
                                                        <option value="0">Não</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="app_personalizado">Aplicativo Personalizado:</label>
                                                    <select name="app_personalizado" id="app-personalizado" class="form-control">
                                                        <option value="" selected>Todos</option>
                                                        <option value="1">Sim</option>
                                                        <option value="0">Não</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-12 text-right">
                                                    <div class="btn btn-primary" id="btn-search">
                                                        <span class="fa fa-search"></span>
                                                        Pesquisar
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-hover" id="redes-index-data-table">
                            <thead>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <script src="/webroot/js/scripts/redes/index<?= $extensionDebug ?>.js?version=<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/css/styles/redes/index<?= $extensionDebug ?>.css?version=<?= SYSTEM_VERSION ?>" /> -->



<?php

$this->append("script");
echo $this->Html->script(sprintf("scripts/redes/index%s.js?version=%s", $extensionDebug, SYSTEM_VERSION));
$this->end();
$this->append("css");
echo $this->Html->css(sprintf("styles/redes/index%s.css?version=%s", $extensionDebug, SYSTEM_VERSION));
$this->end();


?>
