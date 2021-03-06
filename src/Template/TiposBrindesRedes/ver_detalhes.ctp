<?php

/**
 * ver_detalhes.ctp
 *
 * View para tipos_brindes_redes/ver_detlahes
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TiposBrindesRede $tiposBrindesRede
 *
 * @category View
 * @package App\Template\TiposBrindesRedes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date 30/05/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.0.0
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Escolher Rede para Configurar Tipos de Brindes', array("controller" => "tiposBrindesRedes", "action" => "index"));
$this->Breadcrumbs->add('Tipos de Brindes da Rede', array("controller" => "tiposBrindesRedes", "action" => "configurar_tipos_brindes_rede", $tiposBrindesRede["rede"]["id"]));
$this->Breadcrumbs->add('Detalhes do Tipo de Brindes', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->Element("../TiposBrindesRedes/left_menu", ["mode" => "view"]) ?>
<div class="tipos-brindes view col-lg-9 col-md-10 columns content">
    <legend><?= h($tiposBrindesRede->nome) ?></legend>
    <?= $this->Element("../TiposBrindesRedes/tabela_info_tipos_brindes_rede", ["tiposBrindesRede" => $tiposBrindesRede]) ?>
    <div class="form-group row">
        <div class="col-lg-12 text-right">
            <a onclick="history.go(-1); return false;" class="btn btn-primary botao-cancelar"> <i class="fa fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</div>
