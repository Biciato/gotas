<?php

/**
 * ver_detalhes.ctp
 *
 * View para tipos_brindes_clientes/ver_detlahes
 *
 * Variáveis:
 * @var       \App\View\AppView $this
 * @var       \App\Model\Entity\TiposBrindesClientes $tiposBrindesClientes
 *
 * @category  View
 * @package   App\Template\TiposBrindesClientes
 * @author    Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date      03/06/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   1.0
 * @link      http://pear.php.net/package/PackageName
 * @since     File available since Release 1.0.0
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// Navegação

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
$this->Breadcrumbs->add('Detalhes da Rede', ['controller' => 'Redes', 'action' => 'ver_detalhes', $cliente->redes_has_cliente->redes_id]);
$this->Breadcrumbs->add('Detalhes da Unidade', ['controller' => 'clientes', 'action' => 'ver_detalhes', $cliente->id]);
$this->Breadcrumbs->add('Tipos de Brindes Habilitados', array("action" => "tipos_brindes_cliente", $cliente["id"]));

$this->Breadcrumbs->add('Info do Tipo de Brindes', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->Element("../TiposBrindesClientes/left_menu", ["mode" => "view", "clientesId" => $cliente["id"]]) ?>
<div class="tipos-brindes view col-lg-9 col-md-10 columns content">
    <legend><?= h($tiposBrindesClientes["tipos_brindes_rede"]["nome"]) ?></legend>

    <?= $this->Element("../TiposBrindesClientes/tabela_info_tipos_brindes_cliente", array("tiposBrindesCliente" => $tiposBrindesClientes)) ?>
</div>
