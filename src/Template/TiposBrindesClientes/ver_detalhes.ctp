<?php

/**
 * ver_detalhes.ctp
 *
 * View para genero_brindes_clientes/ver_detlahes
 *
 * Variáveis:
 * @var       \App\View\AppView $this
 * @var       \App\Model\Entity\GeneroBrindesClientes $generoBrindeCliente
 *
 * @category  View
 * @package   App\Template\GeneroBrindesClientes
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
$this->Breadcrumbs->add('Detalhes da Rede', ['controller' => 'Redes', 'action' => 'ver_detalhes', $cliente->rede_has_cliente->redes_id]);
$this->Breadcrumbs->add('Detalhes da Unidade', ['controller' => 'clientes', 'action' => 'ver_detalhes', $cliente->id]);
$this->Breadcrumbs->add('Gênero de Brindes Habilitados', ["controller" => "genero_brindes_clientes", "action" => "genero_brindes_clientes", $cliente->id]);
$this->Breadcrumbs->add('Info do Gênero de Brindes', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->Element("../GeneroBrindesClientes/left_menu", ["mode" => "view", "clientesId" => $cliente["id"]]) ?>
<div class="genero-brindes view col-lg-9 col-md-10 columns content">
    <legend><?= h($generoBrindesClientes->genero_brinde->nome) ?></legend>

    <?= $this->Element("../GeneroBrindes/tabela_info_genero_brinde", ["generoBrinde" => $generoBrindesClientes["genero_brinde"]]) ?>
</div>
