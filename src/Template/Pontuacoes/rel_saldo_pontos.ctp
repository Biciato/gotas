<?php

/**
 * index.ctp
 *
 * View para pontuacoes/rel-saldo-pontos
 *
 * @filesource src\Template\RedesCpfListaNegra\redes_cpf_lista_negra.ctp
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.1.8
 * @date 2020-03-11
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RedesCpfListaNegra[]|\Cake\Collection\CollectionInterface $redesCpfListaNegra
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$title = "Saldo de Pontos de Usuário";

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ["class" => "active"]);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<nav class="col-lg-3 col-md-2 " id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a><?= __('Ações') ?></a></li>
    </ul>
</nav>
<div class="clientes index col-lg-9 col-md-10 columns content container-fluid">
    <div id="dados">

        <legend><?= $title ?></legend>

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
                                <div class="col-lg-6">
                                    <label for="redes_list">Rede:</label>
                                    <select name="redes_list" id="redes-list" class="form-control"></select>
                                </div>
                                <div class="col-lg-6">
                                    <label for="nome_usuario_form_search">Nome de Usuário:</label>
                                    <input type="text" name="nome_usuario_form_search" id="nome-usuario-form-search" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12 text-right">
                                    <div class="btn btn-primary" id="btn-pesquisar">
                                        <span class="fa fa-search"></span>
                                        Pesquisar
                                    </div>
                                    <div class="imprimir btn btn-default print-button-thermal" id="btn-imprimir">
                                        <i class="fa fa-print"></i>
                                        Imprimir
                                    </div>
                                    <div class="imprimir btn btn-success" id="btn-exportar">
                                        <i class="fas fa-file-excel-o"></i>
                                        Exportar
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="print-region">
        <div class="form-group row">
            <div id="container-report" class="col-lg-12"> </div>
        </div>
    </div>
</div>

<?php

$extensionDebug = Configure::read("debug") ? '' : '.min';

?>

<script src="/webroot/js/scripts/pontuacoes/rel-saldo-pontos<?= $extensionDebug ?>.js?version=<?= SYSTEM_VERSION ?>"></script>

<link rel="stylesheet" href="/webroot/css/styles/pontuacoes/rel-saldo-pontos<?= $extensionDebug ?>.css?version=<?= SYSTEM_VERSION ?>" />